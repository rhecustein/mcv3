<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Result;
use App\Services\CompanyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

/**
 * Company Patient Controller
 * Handles company employee/patient management
 */
class CompanyPatientController extends Controller
{
    public function __construct(
        protected CompanyService $companyService
    ) {}

    /**
     * Show company patients list with filtering
     */
    public function index(Request $request)
    {
        try {
            $company = $this->companyService->getUserCompany();

            if (!$company) {
                return redirect()->route('company.dashboard')
                    ->with('error', 'Company tidak ditemukan.');
            }

            $patientsQuery = $this->companyService->getCompanyPatients($company)
                ->with(['user', 'outlet', 'results' => function($query) {
                    $query->latest()->limit(5);
                }]);

            // Search functionality
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

            // Sorting
            $sortBy = $request->get('sort_by', 'full_name');
            $sortOrder = $request->get('sort_order', 'asc');

            if (in_array($sortBy, ['full_name', 'nik', 'created_at'])) {
                $patientsQuery->orderBy($sortBy, $sortOrder);
            }

            $perPage = $request->get('per_page', 25);
            $patients = $patientsQuery->paginate($perPage);

            // Get outlets for filter
            $outlets = $this->companyService->getCompanyOutlets($company);

            // Statistics
            $allPatientIds = $this->companyService->getCompanyPatients($company)->pluck('id');
            $totalPatients = $allPatientIds->count();
            $totalMale = Patient::whereIn('id', $allPatientIds)->where('gender', 'L')->count();
            $totalFemale = Patient::whereIn('id', $allPatientIds)->where('gender', 'P')->count();
            $totalOutlets = $outlets->count();

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
                'company_id' => $company->id ?? null
            ]);

            if ($request->ajax()) {
                return response()->json(['error' => 'Terjadi kesalahan saat memuat data'], 500);
            }

            return redirect()->route('company.dashboard')
                ->with('error', 'Terjadi kesalahan saat memuat data karyawan.');
        }
    }

    /**
     * Show specific patient details
     */
    public function show(Request $request, $patientId)
    {
        try {
            $company = $this->companyService->getUserCompany();

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

            return redirect()->route('company.patients.index')
                ->with('error', 'Data karyawan tidak ditemukan.');
        }
    }

    /**
     * Export patients data
     */
    public function export(Request $request)
    {
        try {
            $company = $this->companyService->getUserCompany();

            if (!$company) {
                return response()->json(['error' => 'Company tidak ditemukan'], 404);
            }

            $format = $request->get('format', 'csv');
            $filters = $request->only(['search', 'gender', 'outlet_id']);

            $patientsQuery = $this->companyService->getCompanyPatients($company)
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

            if ($format === 'csv') {
                return $this->exportToCsv($patients, $company);
            }

            return response()->json(['error' => 'Format tidak didukung'], 400);

        } catch (\Exception $e) {
            Log::error('Export patients error: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal mengexport data'], 500);
        }
    }

    /**
     * Export to CSV
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
}
