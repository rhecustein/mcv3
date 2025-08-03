<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Patient;
use App\Models\Result;
use App\Models\User;
use App\Models\Outlet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class CompanyClientController extends Controller
{
    /**
     * Cache duration in minutes
     */
    private const CACHE_DURATION = 30;

    /**
     * Show company dashboard with statistics
     */
    public function dashboard()
    {
        try {
            $user = Auth::user();
            $company = $this->getUserCompany();
            
            if (!$company) {
                return redirect()->route('login')
                    ->with('error', 'Company tidak ditemukan untuk akun ini.');
            }

            // Cache key for dashboard data
            $cacheKey = "company_dashboard_{$company->id}";
            
            $dashboardData = Cache::remember($cacheKey, self::CACHE_DURATION, function () use ($company) {
                // Get company patients only
                $companyPatients = $this->getCompanyPatients($company);
                $patientIds = $companyPatients->pluck('id');

                // Calculate statistics
                $stats = $this->calculateCompanyStats($company, $patientIds);
                
                // Chart data for the last 12 months
                $chartData = $this->getMonthlyChartData($patientIds);
                
                // Recent activity
                $recentResults = $this->getRecentResults($patientIds);
                
                // Health status summary
                $healthSummary = $this->getHealthStatusSummary($patientIds);
                
                // Top outlets serving company patients
                $topOutlets = $this->getTopOutlets($patientIds);

                return compact('stats', 'chartData', 'recentResults', 'healthSummary', 'topOutlets');
            });

            return view('companies.dashboard', array_merge(['company' => $company], $dashboardData));

        } catch (\Exception $e) {
            Log::error('Company dashboard error: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->route('login')
                ->with('error', 'Terjadi kesalahan saat memuat dashboard.');
        }
    }

    /**
     * Show company patients list with enhanced filtering and statistics
     */
    public function patients(Request $request)
    {
        try {
            $company = $this->getUserCompany();
            
            if (!$company) {
                return redirect()->route('company.dashboard')
                    ->with('error', 'Company tidak ditemukan.');
            }

            $patientsQuery = $this->getCompanyPatients($company)
                ->with(['user', 'outlet', 'results' => function($query) {
                    $query->latest()->limit(5);
                }]);

            // Search functionality with multiple fields
            if ($request->filled('search')) {
                $search = trim($request->search);
                $patientsQuery->where(function($query) use ($search) {
                    $query->where('full_name', 'like', "%{$search}%")
                          ->orWhere('nik', 'like', "%{$search}%")
                          ->orWhere('identity', 'like', "%{$search}%")
                          ->orWhere('phone', 'like', "%{$search}%");
                });
            }

            // Filter by gender
            if ($request->filled('gender')) {
                $patientsQuery->where('gender', $request->gender);
            }

            // Filter by outlet
            if ($request->filled('outlet_id')) {
                $patientsQuery->where('outlet_id', $request->outlet_id);
            }

            // Filter by age range
            if ($request->filled('age_min') || $request->filled('age_max')) {
                $ageMin = $request->filled('age_min') ? (int)$request->age_min : 0;
                $ageMax = $request->filled('age_max') ? (int)$request->age_max : 100;
                
                $patientsQuery->whereRaw('TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) BETWEEN ? AND ?', 
                    [$ageMin, $ageMax]);
            }

            // Filter by health status
            if ($request->filled('health_status')) {
                $healthStatus = $request->health_status;
                $patientsQuery->whereHas('results', function($query) use ($healthStatus) {
                    $query->where('type', $healthStatus)
                          ->whereIn('id', function($subQuery) {
                              $subQuery->select(DB::raw('MAX(id)'))
                                      ->from('results')
                                      ->groupBy('patient_id');
                          });
                });
            }

            // Sorting
            $sortBy = $request->get('sort_by', 'full_name');
            $sortOrder = $request->get('sort_order', 'asc');
            
            if (in_array($sortBy, ['full_name', 'nik', 'created_at'])) {
                $patientsQuery->orderBy($sortBy, $sortOrder);
            }

            $perPage = $request->get('per_page', 25);
            $patients = $patientsQuery->paginate($perPage);
            
            // Get outlets for filter
            $outlets = $this->getCompanyOutlets($company);

            // Calculate statistics for current filtered data
            $totalPatients = $patientsQuery->count();
            $totalMale = $this->getCompanyPatients($company)->where('gender', 'L')->count();
            $totalFemale = $this->getCompanyPatients($company)->where('gender', 'P')->count();
            $totalOutlets = $outlets->count();

            // If this is an AJAX request, return JSON
            if ($request->ajax()) {
                return response()->json([
                    'patients' => $patients,
                    'stats' => compact('totalPatients', 'totalMale', 'totalFemale', 'totalOutlets')
                ]);
            }

            return view('companies.patients', compact(
                'company', 'patients', 'outlets', 
                'totalPatients', 'totalMale', 'totalFemale', 'totalOutlets'
            ));

        } catch (\Exception $e) {
            Log::error('Company patients error: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'company_id' => $company->id ?? null,
                'trace' => $e->getTraceAsString()
            ]);
            
            if ($request->ajax()) {
                return response()->json(['error' => 'Terjadi kesalahan saat memuat data'], 500);
            }
            
            return redirect()->route('company.dashboard')
                ->with('error', 'Terjadi kesalahan saat memuat data karyawan.');
        }
    }

    /**
     * Show company health reports with advanced analytics
     */
    public function reports(Request $request)
    {
        try {
            $company = $this->getUserCompany();
            
            if (!$company) {
                return redirect()->route('company.dashboard')
                    ->with('error', 'Company tidak ditemukan.');
            }

            $patientIds = $this->getCompanyPatients($company)->pluck('id');
            
            // Date range filter
            $startDate = $request->get('start_date', now()->subMonths(12)->format('Y-m-d'));
            $endDate = $request->get('end_date', now()->format('Y-m-d'));
            
            // Monthly health reports
            $monthlyReports = $this->getMonthlyHealthReports($patientIds, $startDate, $endDate);
            
            // Health trends
            $healthTrends = $this->getHealthTrends($patientIds);
            
            // Outlet comparison
            $outletComparison = $this->getOutletHealthComparison($patientIds);
            
            // Disease analytics
            $diseaseAnalytics = $this->getDiseaseAnalytics($patientIds, $startDate, $endDate);
            
            // Export data if requested
            if ($request->filled('export')) {
                return $this->exportHealthReport($company, $patientIds, $request->export);
            }

            return view('companies.reports', compact(
                'company',
                'monthlyReports',
                'healthTrends',
                'outletComparison',
                'diseaseAnalytics',
                'startDate',
                'endDate'
            ));

        } catch (\Exception $e) {
            Log::error('Company reports error: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'company_id' => $company->id ?? null
            ]);
            
            return redirect()->route('company.dashboard')
                ->with('error', 'Terjadi kesalahan saat memuat laporan.');
        }
    }

    /**
     * Show company profile with enhanced information
     */
    public function profile()
    {
        try {
            $user = Auth::user();
            $company = $this->getUserCompany();
            
            if (!$company) {
                return redirect()->route('company.dashboard')
                    ->with('error', 'Company tidak ditemukan.');
            }

            // Get company statistics for profile display
            $patientIds = $this->getCompanyPatients($company)->pluck('id');
            $profileStats = $this->getProfileStats($patientIds);

            return view('companies.profile', compact('user', 'company', 'profileStats'));

        } catch (\Exception $e) {
            Log::error('Company profile error: ' . $e->getMessage(), [
                'user_id' => Auth::id()
            ]);
            
            return redirect()->route('company.dashboard')
                ->with('error', 'Terjadi kesalahan saat memuat profil.');
        }
    }

    /**
     * Get company associated with current user with improved matching
     */
    private function getUserCompany()
    {
        $user = Auth::user();
        
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
     * Get patients belonging to company with better performance
     */
    private function getCompanyPatients(Company $company)
    {
        return Patient::where('company_id', $company->id)
            ->where('is_active', true)
            ->latest();
    }

    /**
     * Get outlets serving company patients
     */
    private function getCompanyOutlets(Company $company)
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
    private function calculateCompanyStats(Company $company, $patientIds)
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
     * Get enhanced monthly chart data
     */
    private function getMonthlyChartData($patientIds)
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
     * Get recent health check results with more details
     */
    private function getRecentResults($patientIds)
    {
        return Result::whereIn('patient_id', $patientIds)
            ->with(['patient', 'doctor.user', 'outlet', 'medicalDiagnosis'])
            ->latest()
            ->limit(15)
            ->get();
    }

    /**
     * Get enhanced health status summary
     */
    private function getHealthStatusSummary($patientIds)
    {
        $total = Result::whereIn('patient_id', $patientIds)->count();
        $skb = Result::whereIn('patient_id', $patientIds)->where('type', 'skb')->count();
        $mc = Result::whereIn('patient_id', $patientIds)->where('type', 'mc')->count();

        // Get current month data
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
     * Get top outlets with enhanced metrics
     */
    private function getTopOutlets($patientIds)
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
            ->limit(10)
            ->get();
    }

    /**
     * Get monthly health reports with date range
     */
    private function getMonthlyHealthReports($patientIds, $startDate = null, $endDate = null)
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
            ->limit(24)
            ->get();
    }

    /**
     * Get enhanced health trends
     */
    private function getHealthTrends($patientIds)
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
    private function getOutletHealthComparison($patientIds)
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
    private function getDiseaseAnalytics($patientIds, $startDate, $endDate)
    {
        return DB::table('results')
            ->join('medical_diagnoses', 'results.medical_diagnosis_id', '=', 'medical_diagnoses.id')
            ->whereIn('results.patient_id', $patientIds)
            ->where('results.type', 'mc')
            ->whereBetween('results.created_at', [$startDate, $endDate])
            ->select(
                'medical_diagnoses.name as disease_name',
                DB::raw('COUNT(*) as case_count'),
                DB::raw('ROUND((COUNT(*) / (SELECT COUNT(*) FROM results WHERE patient_id IN (' . $patientIds->implode(',') . ') AND type = "mc" AND created_at BETWEEN "' . $startDate . '" AND "' . $endDate . '")) * 100, 2) as percentage')
            )
            ->groupBy('medical_diagnoses.id', 'medical_diagnoses.name')
            ->orderByDesc('case_count')
            ->limit(10)
            ->get();
    }

    /**
     * Get profile statistics
     */
    private function getProfileStats($patientIds)
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
     * Show specific patient details with comprehensive history
     */
    public function showPatient(Request $request, $patientId)
    {
        try {
            $company = $this->getUserCompany();
            
            if (!$company) {
                return redirect()->route('company.dashboard')
                    ->with('error', 'Company tidak ditemukan.');
            }

            $patient = Patient::where('id', $patientId)
                ->where('company_id', $company->id)
                ->with(['user', 'outlet', 'company'])
                ->firstOrFail();

            $healthHistory = Result::where('patient_id', $patient->id)
                ->with(['doctor.user', 'outlet', 'medicalDiagnosis'])
                ->orderBy('created_at', 'desc')
                ->paginate(15);

            // Patient statistics
            $patientStats = [
                'total_checkups' => $healthHistory->total(),
                'healthy_count' => Result::where('patient_id', $patient->id)->where('type', 'skb')->count(),
                'sick_count' => Result::where('patient_id', $patient->id)->where('type', 'mc')->count(),
                'last_checkup' => Result::where('patient_id', $patient->id)->latest()->first(),
            ];

            if ($request->ajax()) {
                return response()->json([
                    'patient' => $patient,
                    'health_history' => $healthHistory,
                    'stats' => $patientStats
                ]);
            }

            return view('companies.patient-detail', compact(
                'company', 
                'patient', 
                'healthHistory', 
                'patientStats'
            ));

        } catch (\Exception $e) {
            Log::error('Patient detail error: ' . $e->getMessage(), [
                'patient_id' => $patientId,
                'company_id' => $company->id ?? null
            ]);
            
            if ($request->ajax()) {
                return response()->json(['error' => 'Data tidak ditemukan'], 404);
            }
            
            return redirect()->route('company.patients')
                ->with('error', 'Data karyawan tidak ditemukan.');
        }
    }

    /**
     * Export patients data with various formats
     */
    public function exportPatients(Request $request)
    {
        try {
            $company = $this->getUserCompany();
            
            if (!$company) {
                return response()->json(['error' => 'Company tidak ditemukan'], 404);
            }

            $format = $request->get('format', 'csv');
            $filters = $request->only(['search', 'gender', 'outlet_id']);

            $patientsQuery = $this->getCompanyPatients($company)
                ->with(['outlet', 'results']);

            // Apply filters
            if (!empty($filters['search'])) {
                $search = $filters['search'];
                $patientsQuery->where(function($query) use ($search) {
                    $query->where('full_name', 'like', "%{$search}%")
                          ->orWhere('nik', 'like', "%{$search}%")
                          ->orWhere('identity', 'like', "%{$search}%");
                });
            }

            if (!empty($filters['gender'])) {
                $patientsQuery->where('gender', $filters['gender']);
            }

            if (!empty($filters['outlet_id'])) {
                $patientsQuery->where('outlet_id', $filters['outlet_id']);
            }

            $patients = $patientsQuery->get();

            // Generate export based on format
            switch ($format) {
                case 'csv':
                    return $this->exportToCsv($patients, $company);
                case 'excel':
                    return $this->exportToExcel($patients, $company);
                case 'pdf':
                    return $this->exportToPdf($patients, $company);
                default:
                    return response()->json(['error' => 'Format tidak didukung'], 400);
            }

        } catch (\Exception $e) {
            Log::error('Export patients error: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal mengexport data'], 500);
        }
    }

    /**
     * API: Get dashboard stats with caching
     */
    public function getDashboardStats(): JsonResponse
    {
        try {
            $company = $this->getUserCompany();
            
            if (!$company) {
                return response()->json(['error' => 'Company tidak ditemukan'], 404);
            }

            $cacheKey = "dashboard_stats_{$company->id}";
            
            $stats = Cache::remember($cacheKey, 15, function () use ($company) {
                $patientIds = $this->getCompanyPatients($company)->pluck('id');
                return $this->calculateCompanyStats($company, $patientIds);
            });

            return response()->json($stats);

        } catch (\Exception $e) {
            Log::error('Dashboard stats API error: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal memuat statistik'], 500);
        }
    }

    /**
     * API: Get patient health data
     */
    public function getPatientHealth($patientId): JsonResponse
    {
        try {
            $company = $this->getUserCompany();
            
            if (!$company) {
                return response()->json(['error' => 'Company tidak ditemukan'], 404);
            }

            $patient = Patient::where('id', $patientId)
                ->where('company_id', $company->id)
                ->firstOrFail();

            $healthData = Result::where('patient_id', $patient->id)
                ->with(['doctor.user', 'outlet', 'medicalDiagnosis'])
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();

            return response()->json([
                'patient' => $patient,
                'health_history' => $healthData,
                'stats' => [
                    'total_checkups' => Result::where('patient_id', $patient->id)->count(),
                    'healthy_count' => Result::where('patient_id', $patient->id)->where('type', 'skb')->count(),
                    'sick_count' => Result::where('patient_id', $patient->id)->where('type', 'mc')->count(),
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Patient health API error: ' . $e->getMessage(), ['patient_id' => $patientId]);
            return response()->json(['error' => 'Data tidak ditemukan'], 404);
        }
    }

    /**
     * API: Get chart data with enhanced metrics
     */
    public function getChartData(Request $request): JsonResponse
    {
        try {
            $company = $this->getUserCompany();
            
            if (!$company) {
                return response()->json(['error' => 'Company tidak ditemukan'], 404);
            }

            $type = $request->get('type', 'monthly');
            $patientIds = $this->getCompanyPatients($company)->pluck('id');
            
            switch ($type) {
                case 'monthly':
                    $chartData = $this->getMonthlyChartData($patientIds);
                    break;
                case 'weekly':
                    $chartData = $this->getWeeklyChartData($patientIds);
                    break;
                case 'yearly':
                    $chartData = $this->getYearlyChartData($patientIds);
                    break;
                default:
                    $chartData = $this->getMonthlyChartData($patientIds);
            }

            return response()->json($chartData);

        } catch (\Exception $e) {
            Log::error('Chart data API error: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal memuat data chart'], 500);
        }
    }

    /**
     * Get weekly chart data
     */
    private function getWeeklyChartData($patientIds)
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
    private function getYearlyChartData($patientIds)
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
     * Monthly health report with advanced filtering
     */
    public function monthlyReport(Request $request)
    {
        try {
            $company = $this->getUserCompany();
            
            if (!$company) {
                return redirect()->route('company.dashboard')
                    ->with('error', 'Company tidak ditemukan.');
            }

            $month = $request->get('month', now()->month);
            $year = $request->get('year', now()->year);
            $outletId = $request->get('outlet_id');
            
            $patientIds = $this->getCompanyPatients($company)->pluck('id');
            
            $monthlyQuery = Result::whereIn('patient_id', $patientIds)
                ->whereMonth('created_at', $month)
                ->whereYear('created_at', $year)
                ->with(['patient', 'doctor.user', 'outlet', 'medicalDiagnosis']);

            if ($outletId) {
                $monthlyQuery->whereHas('patient', function($query) use ($outletId) {
                    $query->where('outlet_id', $outletId);
                });
            }

            $monthlyData = $monthlyQuery->orderBy('created_at', 'desc')->paginate(25);

            $monthlyStats = [
                'total_checkups' => $monthlyData->total(),
                'healthy_count' => Result::whereIn('patient_id', $patientIds)
                    ->whereMonth('created_at', $month)
                    ->whereYear('created_at', $year)
                    ->where('type', 'skb')
                    ->count(),
                'sick_count' => Result::whereIn('patient_id', $patientIds)
                    ->whereMonth('created_at', $month)
                    ->whereYear('created_at', $year)
                    ->where('type', 'mc')
                    ->count(),
                'unique_patients' => Result::whereIn('patient_id', $patientIds)
                    ->whereMonth('created_at', $month)
                    ->whereYear('created_at', $year)
                    ->distinct('patient_id')
                    ->count(),
            ];

            // Get outlets for filter
            $outlets = $this->getCompanyOutlets($company);

            return view('companies.monthly-report', compact(
                'company', 
                'monthlyData', 
                'monthlyStats', 
                'month', 
                'year',
                'outlets'
            ));

        } catch (\Exception $e) {
            Log::error('Monthly report error: ' . $e->getMessage());
            return redirect()->route('company.dashboard')
                ->with('error', 'Terjadi kesalahan saat memuat laporan bulanan.');
        }
    }

    /**
     * Company statistics page with comprehensive analytics
     */
    public function statistics()
    {
        try {
            $company = $this->getUserCompany();
            
            if (!$company) {
                return redirect()->route('company.dashboard')
                    ->with('error', 'Company tidak ditemukan.');
            }

            $patientIds = $this->getCompanyPatients($company)->pluck('id');
            
            // Detailed statistics
            $stats = $this->calculateDetailedStats($patientIds);
            
            // Outlet performance
            $outletPerformance = $this->getOutletPerformance($patientIds);
            
            // Monthly trends
            $monthlyTrends = $this->getDetailedMonthlyTrends($patientIds);
            
            // Age group analysis
            $ageGroupAnalysis = $this->getAgeGroupAnalysis($patientIds);
            
            // Disease trends
            $diseaseTrends = $this->getDiseaseTrends($patientIds);

            return view('companies.statistics', compact(
                'company',
                'stats',
                'outletPerformance',
                'monthlyTrends',
                'ageGroupAnalysis',
                'diseaseTrends'
            ));

        } catch (\Exception $e) {
            Log::error('Statistics page error: ' . $e->getMessage());
            return redirect()->route('company.dashboard')
                ->with('error', 'Terjadi kesalahan saat memuat statistik.');
        }
    }

    /**
     * Edit company profile
     */
    public function editProfile()
    {
        try {
            $user = Auth::user();
            $company = $this->getUserCompany();
            
            if (!$company) {
                return redirect()->route('company.dashboard')
                    ->with('error', 'Company tidak ditemukan.');
            }

            return view('companies.edit-profile', compact('user', 'company'));

        } catch (\Exception $e) {
            Log::error('Edit profile error: ' . $e->getMessage());
            return redirect()->route('company.profile.show')
                ->with('error', 'Terjadi kesalahan saat memuat halaman edit profil.');
        }
    }

    /**
     * Update company profile with validation
     */
    public function updateProfile(Request $request)
    {
        try {
            $company = $this->getUserCompany();
            
            if (!$company) {
                return redirect()->route('company.dashboard')
                    ->with('error', 'Company tidak ditemukan.');
            }

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'industry_type' => 'nullable|string|max:100',
                'email' => 'required|email|max:255|unique:companies,email,' . $company->id,
                'phone' => 'nullable|string|max:20',
                'address' => 'nullable|string|max:500',
                'city' => 'nullable|string|max:100',
                'province' => 'nullable|string|max:100',
                'description' => 'nullable|string|max:1000',
                'website' => 'nullable|url|max:255',
            ]);

            $company->update($validated);

            // Clear cache after update
            Cache::forget("user_company_{$company->id}");
            Cache::forget("company_dashboard_{$company->id}");

            return redirect()->route('company.profile.show')
                ->with('success', 'Profil perusahaan berhasil diperbarui.');

        } catch (ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            Log::error('Update profile error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat memperbarui profil.')
                ->withInput();
        }
    }

    /**
     * Calculate detailed statistics
     */
    private function calculateDetailedStats($patientIds)
    {
        $baseStats = $this->calculateCompanyStats(null, $patientIds);
        
        // Add more detailed stats
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
     * Get outlet performance data with enhanced metrics
     */
    private function getOutletPerformance($patientIds)
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
    private function getDetailedMonthlyTrends($patientIds)
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
            ->limit(24)
            ->get();
    }

    /**
     * Get age group analysis
     */
    private function getAgeGroupAnalysis($patientIds)
    {
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
    private function getDiseaseTrends($patientIds)
    {
        return DB::table('results')
            ->join('medical_diagnoses', 'results.medical_diagnosis_id', '=', 'medical_diagnoses.id')
            ->whereIn('results.patient_id', $patientIds)
            ->where('results.type', 'mc')
            ->where('results.created_at', '>=', now()->subMonths(12))
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
     * Export to CSV format
     */
    private function exportToCsv($patients, $company)
    {
        $filename = "karyawan_{$company->name}_" . now()->format('Y-m-d') . ".csv";
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($patients) {
            $file = fopen('php://output', 'w');
            
            // CSV headers
            fputcsv($file, [
                'Nama Lengkap', 'NIK', 'ID Karyawan', 'Gender', 'Tanggal Lahir', 
                'Telepon', 'Alamat', 'Outlet', 'Total Check-up', 'Status Terakhir'
            ]);

            foreach ($patients as $patient) {
                $lastResult = $patient->results->first();
                
                fputcsv($file, [
                    $patient->full_name,
                    $patient->nik ?? '-',
                    $patient->identity ?? '-',
                    $patient->gender === 'L' ? 'Laki-laki' : 'Perempuan',
                    $patient->birth_date ? Carbon::parse($patient->birth_date)->format('d/m/Y') : '-',
                    $patient->phone ?? '-',
                    $patient->address ?? '-',
                    $patient->outlet->name ?? '-',
                    $patient->results->count(),
                    $lastResult ? ($lastResult->type === 'skb' ? 'Sehat' : 'Sakit') : 'Belum ada data'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export to Excel format (placeholder - implement with PhpSpreadsheet)
     */
    private function exportToExcel($patients, $company)
    {
        // Implementation would require PhpSpreadsheet library
        return response()->json([
            'message' => 'Excel export akan diimplementasi dengan PhpSpreadsheet',
            'data_count' => $patients->count()
        ]);
    }

    /**
     * Export to PDF format (placeholder - implement with DomPDF)
     */
    private function exportToPdf($patients, $company)
    {
        // Implementation would require DomPDF library
        return response()->json([
            'message' => 'PDF export akan diimplementasi dengan DomPDF',
            'data_count' => $patients->count()
        ]);
    }

    /**
     * Clear company cache
     */
    public function clearCache()
    {
        try {
            $company = $this->getUserCompany();
            
            if ($company) {
                Cache::forget("user_company_{$company->id}");
                Cache::forget("company_dashboard_{$company->id}");
                Cache::forget("dashboard_stats_{$company->id}");
            }

            return response()->json(['message' => 'Cache berhasil dibersihkan']);

        } catch (\Exception $e) {
            Log::error('Clear cache error: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal membersihkan cache'], 500);
        }
    }

    /**
     * Health report export
     */
    private function exportHealthReport($company, $patientIds, $format)
    {
        try {
            $results = Result::whereIn('patient_id', $patientIds)
                ->with(['patient', 'doctor.user', 'outlet', 'medicalDiagnosis'])
                ->orderBy('created_at', 'desc')
                ->get();

            switch ($format) {
                case 'csv':
                    return $this->exportHealthReportCsv($results, $company);
                case 'excel':
                    return $this->exportHealthReportExcel($results, $company);
                default:
                    return response()->json(['error' => 'Format tidak didukung'], 400);
            }

        } catch (\Exception $e) {
            Log::error('Health report export error: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal mengexport laporan kesehatan'], 500);
        }
    }

    /**
     * Export health report to CSV
     */
    private function exportHealthReportCsv($results, $company)
    {
        $filename = "laporan_kesehatan_{$company->name}_" . now()->format('Y-m-d') . ".csv";
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($results) {
            $file = fopen('php://output', 'w');
            
            // CSV headers
            fputcsv($file, [
                'Tanggal', 'Nama Karyawan', 'NIK', 'Jenis Surat', 'Diagnosa', 
                'Dokter', 'Outlet', 'Status'
            ]);

            foreach ($results as $result) {
                fputcsv($file, [
                    $result->created_at->format('d/m/Y H:i'),
                    $result->patient->full_name,
                    $result->patient->nik ?? '-',
                    $result->type === 'skb' ? 'Surat Sehat' : 'Surat Sakit',
                    $result->medicalDiagnosis->name ?? '-',
                    $result->doctor->user->name ?? '-',
                    $result->outlet->name ?? '-',
                    $result->type === 'skb' ? 'Sehat' : 'Sakit'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export health report to Excel (placeholder)
     */
    private function exportHealthReportExcel($results, $company)
    {
        return response()->json([
            'message' => 'Excel export untuk laporan kesehatan akan diimplementasi',
            'data_count' => $results->count()
        ]);
    }
}