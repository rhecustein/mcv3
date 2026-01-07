<?php

namespace App\Http\Controllers;

use App\Models\Result;
use App\Services\CompanyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class CompanyReportController extends Controller
{
    public function __construct(protected CompanyService $companyService) {}

    public function index(Request $request)
    {
        try {
            $company = $this->companyService->getUserCompany();
            if (!$company) {
                return redirect()->route('company.dashboard')->with('error', 'Company tidak ditemukan.');
            }
            $patientIds = $this->companyService->getCompanyPatients($company)->pluck('id');
            $startDate = $request->get('start_date', now()->subMonths(12)->format('Y-m-d'));
            $endDate = $request->get('end_date', now()->format('Y-m-d'));
            $monthlyReports = $this->companyService->getMonthlyHealthReports($patientIds, $startDate, $endDate);
            $healthTrends = $this->companyService->getHealthTrends($patientIds);
            $outletComparison = $this->companyService->getOutletHealthComparison($patientIds);
            $diseaseAnalytics = $this->companyService->getDiseaseAnalytics($patientIds, $startDate, $endDate);
            if ($request->filled('export')) {
                return $this->exportHealthReport($company, $patientIds, $request->export);
            }
            return view('companies.reports', compact('company', 'monthlyReports', 'healthTrends', 'outletComparison', 'diseaseAnalytics', 'startDate', 'endDate'));
        } catch (\Exception $e) {
            Log::error('Company reports error: ' . $e->getMessage(), ['user_id' => Auth::id(), 'company_id' => $company->id ?? null]);
            return redirect()->route('company.dashboard')->with('error', 'Terjadi kesalahan saat memuat laporan.');
        }
    }

    public function monthly(Request $request)
    {
        try {
            $company = $this->companyService->getUserCompany();
            if (!$company) {
                return redirect()->route('company.dashboard')->with('error', 'Company tidak ditemukan.');
            }
            $month = $request->get('month', now()->month);
            $year = $request->get('year', now()->year);
            $outletId = $request->get('outlet_id');
            $patientIds = $this->companyService->getCompanyPatients($company)->pluck('id');
            $monthlyQuery = Result::whereIn('patient_id', $patientIds)->whereMonth('created_at', $month)->whereYear('created_at', $year)->with(['patient', 'doctor.user', 'outlet', 'medicalDiagnosis']);
            if ($outletId) {
                $monthlyQuery->whereHas('patient', function($query) use ($outletId) {
                    $query->where('outlet_id', $outletId);
                });
            }
            $monthlyData = $monthlyQuery->orderBy('created_at', 'desc')->paginate(25);
            $monthlyStats = [
                'total_checkups' => $monthlyData->total(),
                'healthy_count' => Result::whereIn('patient_id', $patientIds)->whereMonth('created_at', $month)->whereYear('created_at', $year)->where('type', 'skb')->count(),
                'sick_count' => Result::whereIn('patient_id', $patientIds)->whereMonth('created_at', $month)->whereYear('created_at', $year)->where('type', 'mc')->count(),
                'unique_patients' => Result::whereIn('patient_id', $patientIds)->whereMonth('created_at', $month)->whereYear('created_at', $year)->distinct('patient_id')->count(),
            ];
            $outlets = $this->companyService->getCompanyOutlets($company);
            return view('companies.monthly-report', compact('company', 'monthlyData', 'monthlyStats', 'month', 'year', 'outlets'));
        } catch (\Exception $e) {
            Log::error('Monthly report error: ' . $e->getMessage());
            return redirect()->route('company.dashboard')->with('error', 'Terjadi kesalahan saat memuat laporan bulanan.');
        }
    }

    public function export(Request $request)
    {
        try {
            $company = $this->companyService->getUserCompany();
            if (!$company) {
                return response()->json(['error' => 'Company tidak ditemukan'], 404);
            }
            $format = $request->get('format', 'csv');
            $patientIds = $this->companyService->getCompanyPatients($company)->pluck('id');
            if ($format === 'csv') {
                return $this->exportHealthReport($company, $patientIds, $format);
            }
            return response()->json(['error' => 'Format tidak didukung'], 400);
        } catch (\Exception $e) {
            Log::error('Export report error: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal mengexport laporan'], 500);
        }
    }

    private function exportHealthReport($company, $patientIds, $format)
    {
        try {
            $results = Result::whereIn('patient_id', $patientIds)->with(['patient', 'doctor.user', 'outlet', 'medicalDiagnosis'])->orderBy('created_at', 'desc')->get();
            if ($format === 'csv') {
                return $this->exportHealthReportCsv($results, $company);
            }
            return response()->json(['error' => 'Format tidak didukung'], 400);
        } catch (\Exception $e) {
            Log::error('Health report export error: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal mengexport laporan kesehatan'], 500);
        }
    }

    private function exportHealthReportCsv($results, $company)
    {
        $filename = "laporan_kesehatan_{$company->name}_" . now()->format('Y-m-d') . ".csv";
        $headers = ['Content-Type' => 'text/csv', 'Content-Disposition' => "attachment; filename=\"{$filename}\""];
        $callback = function() use ($results) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Tanggal', 'Nama Karyawan', 'NIK', 'Jenis Surat', 'Diagnosa', 'Dokter', 'Outlet', 'Status']);
            foreach ($results as $result) {
                fputcsv($file, [$result->created_at->format('d/m/Y H:i'), $result->patient->full_name, $result->patient->nik ?? '-', $result->type === 'skb' ? 'Surat Sehat' : 'Surat Sakit', $result->medicalDiagnosis->name ?? '-', $result->doctor->user->name ?? '-', $result->outlet->name ?? '-', $result->type === 'skb' ? 'Sehat' : 'Sakit']);
            }
            fclose($file);
        };
        return response()->stream($callback, 200, $headers);
    }
}
