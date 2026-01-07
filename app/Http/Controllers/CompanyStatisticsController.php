<?php

namespace App\Http\Controllers;

use App\Services\CompanyService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CompanyStatisticsController extends Controller
{
    public function __construct(protected CompanyService $companyService) {}

    public function index()
    {
        try {
            $company = $this->companyService->getUserCompany();
            if (!$company) {
                return redirect()->route('company.dashboard')->with('error', 'Company tidak ditemukan.');
            }
            $patientIds = $this->companyService->getCompanyPatients($company)->pluck('id');
            $stats = $this->companyService->calculateDetailedStats($patientIds);
            $outletPerformance = $this->companyService->getOutletPerformance($patientIds);
            $monthlyTrends = $this->companyService->getDetailedMonthlyTrends($patientIds);
            $ageGroupAnalysis = $this->companyService->getAgeGroupAnalysis($patientIds);
            $diseaseTrends = $this->companyService->getDiseaseTrends($patientIds);
            return view('companies.statistics', compact('company', 'stats', 'outletPerformance', 'monthlyTrends', 'ageGroupAnalysis', 'diseaseTrends'));
        } catch (\Exception $e) {
            Log::error('Statistics page error: ' . $e->getMessage());
            return redirect()->route('company.dashboard')->with('error', 'Terjadi kesalahan saat memuat statistik.');
        }
    }

    public function getDashboardStats(): JsonResponse
    {
        try {
            $company = $this->companyService->getUserCompany();
            if (!$company) {
                return response()->json(['error' => 'Company tidak ditemukan'], 404);
            }
            $cacheKey = "dashboard_stats_{$company->id}";
            $stats = Cache::remember($cacheKey, 15, function () use ($company) {
                $patientIds = $this->companyService->getCompanyPatients($company)->pluck('id');
                return $this->companyService->calculateCompanyStats($company, $patientIds);
            });
            return response()->json($stats);
        } catch (\Exception $e) {
            Log::error('Dashboard stats API error: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal memuat statistik'], 500);
        }
    }

    public function getPatientHealth($patientId): JsonResponse
    {
        try {
            $company = $this->companyService->getUserCompany();
            if (!$company) {
                return response()->json(['error' => 'Company tidak ditemukan'], 404);
            }
            $patient = \App\Models\Patient::where('id', $patientId)->where('company_id', $company->id)->firstOrFail();
            $healthData = \App\Models\Result::where('patient_id', $patient->id)->with(['doctor.user', 'outlet', 'medicalDiagnosis'])->orderBy('created_at', 'desc')->limit(10)->get();
            return response()->json(['patient' => $patient, 'health_history' => $healthData, 'stats' => ['total_checkups' => \App\Models\Result::where('patient_id', $patient->id)->count(), 'healthy_count' => \App\Models\Result::where('patient_id', $patient->id)->where('type', 'skb')->count(), 'sick_count' => \App\Models\Result::where('patient_id', $patient->id)->where('type', 'mc')->count()]]);
        } catch (\Exception $e) {
            Log::error('Patient health API error: ' . $e->getMessage(), ['patient_id' => $patientId]);
            return response()->json(['error' => 'Data tidak ditemukan'], 404);
        }
    }

    public function getChartData(Request $request): JsonResponse
    {
        try {
            $company = $this->companyService->getUserCompany();
            if (!$company) {
                return response()->json(['error' => 'Company tidak ditemukan'], 404);
            }
            $type = $request->get('type', 'monthly');
            $patientIds = $this->companyService->getCompanyPatients($company)->pluck('id');
            switch ($type) {
                case 'monthly':
                    $chartData = $this->companyService->getMonthlyChartData($patientIds);
                    break;
                case 'weekly':
                    $chartData = $this->companyService->getWeeklyChartData($patientIds);
                    break;
                case 'yearly':
                    $chartData = $this->companyService->getYearlyChartData($patientIds);
                    break;
                default:
                    $chartData = $this->companyService->getMonthlyChartData($patientIds);
            }
            return response()->json($chartData);
        } catch (\Exception $e) {
            Log::error('Chart data API error: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal memuat data chart'], 500);
        }
    }
}
