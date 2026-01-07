<?php

namespace App\Services;

use App\Models\Company;
use App\Models\Patient;
use App\Models\Result;
use App\Models\Outlet;
use App\Models\User;
use App\Helpers\PasswordHelper;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

/**
 * Company Service
 * Handles business logic for company management and analytics
 */
class CompanyService
{
    /**
     * Cache duration in minutes
     */
    private const CACHE_DURATION = 30;
    /**
     * Create new company with user account
     *
     * @param array $data
     * @return Company
     * @throws \Throwable
     */
    public function create(array $data): Company
    {
        DB::beginTransaction();
        try {
            // Generate secure password if creating user
            if (!empty($data['create_user'])) {
                $passwordData = PasswordHelper::generateForNewUser();

                $user = User::create([
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'password' => $passwordData['password'],
                    'role_type' => 'company',
                    'must_change_password' => $passwordData['must_change_password'],
                ]);

                $data['user_id'] = $user->id;
            }

            // Create company
            $company = Company::create([
                'user_id' => $data['user_id'] ?? null,
                'name' => $data['name'],
                'address' => $data['address'] ?? null,
                'phone' => $data['phone'] ?? null,
                'email' => $data['email'] ?? null,
                'contact_person' => $data['contact_person'] ?? null,
                'is_active' => $data['is_active'] ?? true,
            ]);

            DB::commit();

            \Log::info('Company created successfully', [
                'company_id' => $company->id,
            ]);

            return $company;
        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('Failed to create company', [
                'error' => $e->getMessage(),
                'data' => $data,
            ]);
            throw $e;
        }
    }

    /**
     * Update company
     *
     * @param int $companyId
     * @param array $data
     * @return Company
     * @throws \Throwable
     */
    public function update(int $companyId, array $data): Company
    {
        DB::beginTransaction();
        try {
            $company = Company::findOrFail($companyId);

            $company->update([
                'name' => $data['name'] ?? $company->name,
                'address' => $data['address'] ?? $company->address,
                'phone' => $data['phone'] ?? $company->phone,
                'email' => $data['email'] ?? $company->email,
                'contact_person' => $data['contact_person'] ?? $company->contact_person,
                'is_active' => $data['is_active'] ?? $company->is_active,
            ]);

            // Update user if exists
            if ($company->user && isset($data['user_name'])) {
                $company->user->update([
                    'name' => $data['user_name'],
                ]);
            }

            DB::commit();

            \Log::info('Company updated successfully', [
                'company_id' => $companyId,
            ]);

            return $company->fresh(['user']);
        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('Failed to update company', [
                'company_id' => $companyId,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Delete company
     *
     * @param int $companyId
     * @return bool
     * @throws \Throwable
     */
    public function delete(int $companyId): bool
    {
        DB::beginTransaction();
        try {
            $company = Company::findOrFail($companyId);

            // Delete associated user if exists
            if ($company->user) {
                $company->user->delete();
            }

            $result = $company->delete();

            DB::commit();

            \Log::info('Company deleted successfully', [
                'company_id' => $companyId,
            ]);

            return $result;
        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('Failed to delete company', [
                'company_id' => $companyId,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Get company associated with current user with improved matching
     */
    public function getUserCompany(): ?Company
    {
        $user = Auth::user();

        if (!$user) {
            return null;
        }

        // Cache the company lookup
        $cacheKey = "user_company_{$user->id}";

        return Cache::remember($cacheKey, self::CACHE_DURATION, function () use ($user) {
            // If user is directly associated with company
            if ($user->company_id) {
                return Company::find($user->company_id);
            }

            // If user email matches company email
            $company = Company::where('email', $user->email)->first();

            if ($company) {
                return $company;
            }

            // Check if user has a company relation through patient record
            $patient = Patient::where('user_id', $user->id)->first();
            if ($patient && $patient->company_id) {
                return Company::find($patient->company_id);
            }

            // Fallback: find company by user name pattern or email domain
            $emailDomain = explode('@', $user->email)[1] ?? '';

            if ($emailDomain) {
                $company = Company::where('email', 'like', "%{$emailDomain}%")->first();
                if ($company) {
                    return $company;
                }
            }

            return null;
        });
    }

    /**
     * Get patients belonging to company
     */
    public function getCompanyPatients(Company $company)
    {
        return Patient::where('company_id', $company->id)
            ->where('is_active', true)
            ->latest();
    }

    /**
     * Get outlets serving company patients
     */
    public function getCompanyOutlets(Company $company): Collection
    {
        return Outlet::whereIn('id', function($query) use ($company) {
            $query->select('outlet_id')
                  ->from('patients')
                  ->where('company_id', $company->id)
                  ->distinct();
        })->get();
    }

    /**
     * Calculate enhanced company statistics
     */
    public function calculateCompanyStats(Company $company, Collection $patientIds): array
    {
        $totalPatients = $patientIds->count();
        $thisMonth = now()->month;
        $thisYear = now()->year;
        $lastMonth = now()->subMonth();

        $stats = [
            'total_patients' => $totalPatients,
            'male_patients' => Patient::whereIn('id', $patientIds)->where('gender', 'L')->count(),
            'female_patients' => Patient::whereIn('id', $patientIds)->where('gender', 'P')->count(),
            'total_health_checks' => Result::whereIn('patient_id', $patientIds)->count(),
            'this_month_checks' => Result::whereIn('patient_id', $patientIds)
                ->whereMonth('created_at', $thisMonth)
                ->whereYear('created_at', $thisYear)
                ->count(),
            'last_month_checks' => Result::whereIn('patient_id', $patientIds)
                ->whereMonth('created_at', $lastMonth->month)
                ->whereYear('created_at', $lastMonth->year)
                ->count(),
            'skb_count' => Result::whereIn('patient_id', $patientIds)->where('type', 'skb')->count(),
            'mc_count' => Result::whereIn('patient_id', $patientIds)->where('type', 'mc')->count(),
            'active_outlets' => Patient::whereIn('id', $patientIds)
                ->distinct('outlet_id')
                ->count('outlet_id'),
        ];

        // Calculate growth percentage
        if ($stats['last_month_checks'] > 0) {
            $stats['growth_percentage'] = round(
                (($stats['this_month_checks'] - $stats['last_month_checks']) / $stats['last_month_checks']) * 100,
                1
            );
        } else {
            $stats['growth_percentage'] = $stats['this_month_checks'] > 0 ? 100 : 0;
        }

        return $stats;
    }

    /**
     * Calculate detailed statistics
     */
    public function calculateDetailedStats(Collection $patientIds): array
    {
        $baseStats = [
            'total_patients' => $patientIds->count(),
            'total_health_checks' => Result::whereIn('patient_id', $patientIds)->count(),
            'skb_count' => Result::whereIn('patient_id', $patientIds)->where('type', 'skb')->count(),
            'mc_count' => Result::whereIn('patient_id', $patientIds)->where('type', 'mc')->count(),
        ];

        $baseStats['avg_checkups_per_patient'] = $patientIds->count() > 0
            ? round($baseStats['total_health_checks'] / $patientIds->count(), 1)
            : 0;

        $baseStats['last_30_days'] = Result::whereIn('patient_id', $patientIds)
            ->where('created_at', '>=', now()->subDays(30))
            ->count();

        $baseStats['last_7_days'] = Result::whereIn('patient_id', $patientIds)
            ->where('created_at', '>=', now()->subDays(7))
            ->count();

        $baseStats['health_rate'] = $baseStats['total_health_checks'] > 0
            ? round(($baseStats['skb_count'] / $baseStats['total_health_checks']) * 100, 1)
            : 0;

        return $baseStats;
    }

    /**
     * Get enhanced monthly chart data
     */
    public function getMonthlyChartData(Collection $patientIds): array
    {
        $months = [];
        $data = [];
        $skbData = [];
        $mcData = [];

        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $months[] = $date->format('M Y');

            $totalCount = Result::whereIn('patient_id', $patientIds)
                ->whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->count();

            $skbCount = Result::whereIn('patient_id', $patientIds)
                ->whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->where('type', 'skb')
                ->count();

            $mcCount = Result::whereIn('patient_id', $patientIds)
                ->whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->where('type', 'mc')
                ->count();

            $data[] = $totalCount;
            $skbData[] = $skbCount;
            $mcData[] = $mcCount;
        }

        return [
            'labels' => $months,
            'data' => $data,
            'skb_data' => $skbData,
            'mc_data' => $mcData
        ];
    }

    /**
     * Get weekly chart data
     */
    public function getWeeklyChartData(Collection $patientIds): array
    {
        $weeks = [];
        $data = [];
        $skbData = [];
        $mcData = [];

        for ($i = 7; $i >= 0; $i--) {
            $startWeek = now()->subWeeks($i)->startOfWeek();
            $endWeek = now()->subWeeks($i)->endOfWeek();
            $weeks[] = $startWeek->format('d M') . ' - ' . $endWeek->format('d M');

            $totalCount = Result::whereIn('patient_id', $patientIds)
                ->whereBetween('created_at', [$startWeek, $endWeek])
                ->count();

            $skbCount = Result::whereIn('patient_id', $patientIds)
                ->whereBetween('created_at', [$startWeek, $endWeek])
                ->where('type', 'skb')
                ->count();

            $mcCount = Result::whereIn('patient_id', $patientIds)
                ->whereBetween('created_at', [$startWeek, $endWeek])
                ->where('type', 'mc')
                ->count();

            $data[] = $totalCount;
            $skbData[] = $skbCount;
            $mcData[] = $mcCount;
        }

        return [
            'labels' => $weeks,
            'data' => $data,
            'skb_data' => $skbData,
            'mc_data' => $mcData
        ];
    }

    /**
     * Get yearly chart data
     */
    public function getYearlyChartData(Collection $patientIds): array
    {
        $years = [];
        $data = [];
        $skbData = [];
        $mcData = [];

        for ($i = 4; $i >= 0; $i--) {
            $year = now()->subYears($i)->year;
            $years[] = $year;

            $totalCount = Result::whereIn('patient_id', $patientIds)
                ->whereYear('created_at', $year)
                ->count();

            $skbCount = Result::whereIn('patient_id', $patientIds)
                ->whereYear('created_at', $year)
                ->where('type', 'skb')
                ->count();

            $mcCount = Result::whereIn('patient_id', $patientIds)
                ->whereYear('created_at', $year)
                ->where('type', 'mc')
                ->count();

            $data[] = $totalCount;
            $skbData[] = $skbCount;
            $mcData[] = $mcCount;
        }

        return [
            'labels' => $years,
            'data' => $data,
            'skb_data' => $skbData,
            'mc_data' => $mcData
        ];
    }

    /**
     * Get recent health check results
     */
    public function getRecentResults(Collection $patientIds, int $limit = 15): Collection
    {
        return Result::whereIn('patient_id', $patientIds)
            ->with(['patient', 'doctor.user', 'outlet', 'medicalDiagnosis'])
            ->latest()
            ->limit($limit)
            ->get();
    }

    /**
     * Get enhanced health status summary
     */
    public function getHealthStatusSummary(Collection $patientIds): array
    {
        $total = Result::whereIn('patient_id', $patientIds)->count();
        $skb = Result::whereIn('patient_id', $patientIds)->where('type', 'skb')->count();
        $mc = Result::whereIn('patient_id', $patientIds)->where('type', 'mc')->count();

        $currentMonthTotal = Result::whereIn('patient_id', $patientIds)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $currentMonthSkb = Result::whereIn('patient_id', $patientIds)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->where('type', 'skb')
            ->count();

        return [
            'total' => $total,
            'healthy_percentage' => $total > 0 ? round(($skb / $total) * 100, 1) : 0,
            'sick_percentage' => $total > 0 ? round(($mc / $total) * 100, 1) : 0,
            'skb_count' => $skb,
            'mc_count' => $mc,
            'current_month_total' => $currentMonthTotal,
            'current_month_healthy_rate' => $currentMonthTotal > 0 ?
                round(($currentMonthSkb / $currentMonthTotal) * 100, 1) : 0,
        ];
    }

    /**
     * Get top outlets
     */
    public function getTopOutlets(Collection $patientIds, int $limit = 10): Collection
    {
        return DB::table('patients')
            ->join('outlets', 'patients.outlet_id', '=', 'outlets.id')
            ->leftJoin('results', 'patients.id', '=', 'results.patient_id')
            ->whereIn('patients.id', $patientIds)
            ->select(
                'outlets.id',
                'outlets.name',
                'outlets.city',
                DB::raw('COUNT(DISTINCT patients.id) as patient_count'),
                DB::raw('COUNT(results.id) as total_checkups'),
                DB::raw('SUM(CASE WHEN results.type = "skb" THEN 1 ELSE 0 END) as healthy_count'),
                DB::raw('SUM(CASE WHEN results.type = "mc" THEN 1 ELSE 0 END) as sick_count')
            )
            ->groupBy('outlets.id', 'outlets.name', 'outlets.city')
            ->orderByDesc('patient_count')
            ->limit($limit)
            ->get();
    }

    /**
     * Get monthly health reports
     */
    public function getMonthlyHealthReports(Collection $patientIds, ?string $startDate = null, ?string $endDate = null, int $limit = 24): Collection
    {
        $query = Result::whereIn('patient_id', $patientIds);

        if ($startDate) {
            $query->where('created_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->where('created_at', '<=', $endDate);
        }

        return $query->selectRaw('
                YEAR(created_at) as year,
                MONTH(created_at) as month,
                COUNT(*) as total_checks,
                SUM(CASE WHEN type = "skb" THEN 1 ELSE 0 END) as healthy_count,
                SUM(CASE WHEN type = "mc" THEN 1 ELSE 0 END) as sick_count,
                ROUND(AVG(CASE WHEN type = "skb" THEN 1 ELSE 0 END) * 100, 2) as health_rate
            ')
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get health trends
     */
    public function getHealthTrends(Collection $patientIds): array
    {
        $currentMonth = Result::whereIn('patient_id', $patientIds)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $previousMonth = Result::whereIn('patient_id', $patientIds)
            ->whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->count();

        $lastQuarter = Result::whereIn('patient_id', $patientIds)
            ->where('created_at', '>=', now()->subMonths(3))
            ->count();

        $trend = 0;
        if ($previousMonth > 0) {
            $trend = round((($currentMonth - $previousMonth) / $previousMonth) * 100, 1);
        }

        return [
            'current_month' => $currentMonth,
            'previous_month' => $previousMonth,
            'last_quarter' => $lastQuarter,
            'trend_percentage' => $trend,
            'trend_direction' => $trend > 0 ? 'increase' : ($trend < 0 ? 'decrease' : 'stable'),
            'quarterly_average' => round($lastQuarter / 3, 1)
        ];
    }

    /**
     * Get outlet health comparison
     */
    public function getOutletHealthComparison(Collection $patientIds): Collection
    {
        return DB::table('patients')
            ->join('outlets', 'patients.outlet_id', '=', 'outlets.id')
            ->join('results', 'patients.id', '=', 'results.patient_id')
            ->whereIn('patients.id', $patientIds)
            ->select(
                'outlets.name',
                'outlets.city',
                DB::raw('COUNT(results.id) as total_checkups'),
                DB::raw('SUM(CASE WHEN results.type = "skb" THEN 1 ELSE 0 END) as healthy_count'),
                DB::raw('SUM(CASE WHEN results.type = "mc" THEN 1 ELSE 0 END) as sick_count'),
                DB::raw('ROUND((SUM(CASE WHEN results.type = "skb" THEN 1 ELSE 0 END) / COUNT(results.id)) * 100, 2) as health_rate')
            )
            ->groupBy('outlets.id', 'outlets.name', 'outlets.city')
            ->having('total_checkups', '>', 0)
            ->orderByDesc('health_rate')
            ->get();
    }

    /**
     * Get disease analytics
     */
    public function getDiseaseAnalytics(Collection $patientIds, string $startDate, string $endDate, int $limit = 10): Collection
    {
        if ($patientIds->isEmpty()) {
            return collect();
        }

        $patientIdsString = $patientIds->implode(',');

        return DB::table('results')
            ->join('medical_diagnoses', 'results.medical_diagnosis_id', '=', 'medical_diagnoses.id')
            ->whereIn('results.patient_id', $patientIds->toArray())
            ->where('results.type', 'mc')
            ->whereBetween('results.created_at', [$startDate, $endDate])
            ->select(
                'medical_diagnoses.name as disease_name',
                DB::raw('COUNT(*) as case_count'),
                DB::raw('ROUND((COUNT(*) / (SELECT COUNT(*) FROM results WHERE patient_id IN (' . $patientIdsString . ') AND type = "mc" AND created_at BETWEEN "' . $startDate . '" AND "' . $endDate . '")) * 100, 2) as percentage')
            )
            ->groupBy('medical_diagnoses.id', 'medical_diagnoses.name')
            ->orderByDesc('case_count')
            ->limit($limit)
            ->get();
    }

    /**
     * Get profile statistics
     */
    public function getProfileStats(Collection $patientIds): array
    {
        return [
            'total_patients' => $patientIds->count(),
            'total_checkups' => Result::whereIn('patient_id', $patientIds)->count(),
            'this_year_checkups' => Result::whereIn('patient_id', $patientIds)
                ->whereYear('created_at', now()->year)
                ->count(),
            'avg_age' => Patient::whereIn('id', $patientIds)
                ->whereNotNull('birth_date')
                ->selectRaw('AVG(TIMESTAMPDIFF(YEAR, birth_date, CURDATE())) as avg_age')
                ->value('avg_age') ?? 0,
        ];
    }

    /**
     * Get outlet performance
     */
    public function getOutletPerformance(Collection $patientIds): Collection
    {
        return DB::table('patients')
            ->join('outlets', 'patients.outlet_id', '=', 'outlets.id')
            ->leftJoin('results', 'patients.id', '=', 'results.patient_id')
            ->whereIn('patients.id', $patientIds)
            ->select(
                'outlets.id',
                'outlets.name',
                'outlets.city',
                DB::raw('COUNT(DISTINCT patients.id) as patient_count'),
                DB::raw('COUNT(results.id) as total_checkups'),
                DB::raw('SUM(CASE WHEN results.type = "skb" THEN 1 ELSE 0 END) as healthy_count'),
                DB::raw('SUM(CASE WHEN results.type = "mc" THEN 1 ELSE 0 END) as sick_count'),
                DB::raw('ROUND(AVG(CASE WHEN results.type = "skb" THEN 1 ELSE 0 END) * 100, 2) as health_rate'),
                DB::raw('COUNT(CASE WHEN results.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN 1 END) as last_30_days')
            )
            ->groupBy('outlets.id', 'outlets.name', 'outlets.city')
            ->having('patient_count', '>', 0)
            ->orderByDesc('total_checkups')
            ->get();
    }

    /**
     * Get detailed monthly trends
     */
    public function getDetailedMonthlyTrends(Collection $patientIds, int $limit = 24): Collection
    {
        return Result::whereIn('patient_id', $patientIds)
            ->selectRaw('
                YEAR(created_at) as year,
                MONTH(created_at) as month,
                COUNT(*) as total,
                SUM(CASE WHEN type = "skb" THEN 1 ELSE 0 END) as healthy,
                SUM(CASE WHEN type = "mc" THEN 1 ELSE 0 END) as sick,
                COUNT(DISTINCT patient_id) as unique_patients,
                ROUND(AVG(CASE WHEN type = "skb" THEN 1 ELSE 0 END) * 100, 2) as health_rate
            ')
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get age group analysis
     */
    public function getAgeGroupAnalysis(Collection $patientIds): Collection
    {
        if ($patientIds->isEmpty()) {
            return collect();
        }

        return DB::table('patients')
            ->whereIn('id', $patientIds)
            ->whereNotNull('birth_date')
            ->selectRaw('
                CASE
                    WHEN TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) < 25 THEN "< 25"
                    WHEN TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) BETWEEN 25 AND 35 THEN "25-35"
                    WHEN TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) BETWEEN 36 AND 45 THEN "36-45"
                    WHEN TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) BETWEEN 46 AND 55 THEN "46-55"
                    ELSE "> 55"
                END as age_group,
                COUNT(*) as patient_count
            ')
            ->groupBy('age_group')
            ->orderBy('age_group')
            ->get();
    }

    /**
     * Get disease trends
     */
    public function getDiseaseTrends(Collection $patientIds, int $monthsBack = 12): Collection
    {
        if ($patientIds->isEmpty()) {
            return collect();
        }

        return DB::table('results')
            ->join('medical_diagnoses', 'results.medical_diagnosis_id', '=', 'medical_diagnoses.id')
            ->whereIn('results.patient_id', $patientIds)
            ->where('results.type', 'mc')
            ->where('results.created_at', '>=', now()->subMonths($monthsBack))
            ->select(
                'medical_diagnoses.name as disease_name',
                DB::raw('COUNT(*) as case_count'),
                DB::raw('MONTH(results.created_at) as month'),
                DB::raw('YEAR(results.created_at) as year')
            )
            ->groupBy('medical_diagnoses.id', 'medical_diagnoses.name', 'year', 'month')
            ->orderBy('case_count', 'desc')
            ->get();
    }

    /**
     * Clear company cache
     */
    public function clearCompanyCache(int $companyId): void
    {
        Cache::forget("user_company_{$companyId}");
        Cache::forget("company_dashboard_{$companyId}");
        Cache::forget("dashboard_stats_{$companyId}");
    }

    /**
     * Update company profile
     */
    public function updateCompany(Company $company, array $data): bool
    {
        $updated = $company->update($data);

        if ($updated) {
            $this->clearCompanyCache($company->id);
        }

        return $updated;
    }
}
