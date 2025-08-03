<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Result;
use App\Models\ResultOld;
use App\Models\Patient;
use App\Models\Company;
use App\Models\Doctor;
use App\Models\User;
use App\Models\Outlet;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportManagementController extends Controller
{
    protected array $reportTypes = [
        'rekap_surat' => 'Rekap Surat Terbit',
        'aktivitas_dokter' => 'Aktivitas Dokter',
        'aktivitas_perusahaan' => 'Aktivitas Perusahaan',
        'statistik_penyakit' => 'Statistik Penyakit',
        'feedback_pasien' => 'Feedback & Rating Pasien',
        'log_pengguna' => 'Log Aktivitas Pengguna',
        'rekap_pasien' => 'Rekap Data Pasien',
    ];

    public function index()
    {
        $user = Auth::user();
        $outlet = Outlet::where('user_id', $user->id)->first();
        
        if (!$outlet) {
            return redirect()->route('outlet.dashboard')->with('error', 'Outlet tidak ditemukan untuk akun ini.');
        }

        return view('outlets.reports.index', [
            'reportTypes' => $this->reportTypes,
            'outlets' => collect([$outlet]), // Only show current outlet
            'doctors' => Doctor::where('outlet_id', $outlet->id)->with('user:id,name')->get(),
            'companies' => Company::where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function previewData(Request $request)
    {
        try {
            $request->validate([
                'type' => 'required|in:' . implode(',', array_keys($this->reportTypes)),
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
            ]);

            $data = $this->resolveData($request);
            
            // Debug logging untuk troubleshooting
            if ($request->type === 'rekap_surat' && !empty($data)) {
                $sampleRecord = $data[0] ?? null;
                Log::info('Rekap Surat Debug', [
                    'sample_record' => $sampleRecord,
                    'diagnosis_index' => 18, // Index ke-18 adalah diagnosa
                    'diagnosis_value' => $sampleRecord[18] ?? 'No diagnosis data',
                    'duration_index' => 4, // Index ke-4 adalah durasi
                    'duration_value' => $sampleRecord[4] ?? 'No duration data',
                    'type_value' => $sampleRecord[3] ?? 'No type data',
                ]);
                
                // Count MC vs SKB records for additional info
                $mcCount = collect($data)->filter(fn($row) => strtolower($row[3]) === 'mc')->count();
                $skbCount = collect($data)->filter(fn($row) => strtolower($row[3]) === 'skb')->count();
                
                Log::info('Report Type Distribution', [
                    'mc_count' => $mcCount,
                    'skb_count' => $skbCount,
                    'total_records' => count($data)
                ]);
            }
            
            Log::info('Report preview generated', [
                'type' => $request->type,
                'user_id' => Auth::id(),
                'record_count' => count($data)
            ]);

            return response()->json($data);
        } catch (\Throwable $e) {
            Log::error('Failed to generate report preview', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id(),
                'request' => $request->all()
            ]);

            return response()->json([
                'message' => 'Gagal memuat data.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    public function export(Request $request)
    {
        try {
            $request->validate([
                'type' => 'required|in:' . implode(',', array_keys($this->reportTypes)),
                'format' => 'required|in:pdf,excel',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
            ]);

            $format = $request->input('format');
            $type = $request->input('type');
            $data = $this->resolveData($request);

            if (empty($data)) {
                return back()->with('error', 'Tidak ada data untuk diekspor pada periode yang dipilih.');
            }

            $fileName = $this->generateFileName($type, $format, $request);
            $title = $this->reportTypes[$type] ?? 'Laporan';

            Log::info('Report export initiated', [
                'type' => $type,
                'format' => $format,
                'user_id' => Auth::id(),
                'record_count' => count($data),
                'file_name' => $fileName
            ]);

            if ($format === 'excel') {
                return $this->exportToExcel($data, $type, $fileName);
            } else {
                return $this->exportToPdf($data, $type, $title, $fileName, $request);
            }

        } catch (\Throwable $e) {
            Log::error('Failed to export report', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
                'request' => $request->all()
            ]);

            return back()->with('error', 'Gagal mengekspor laporan: ' . $e->getMessage());
        }
    }

    protected function exportToExcel($data, $type, $fileName)
    {
        $headers = $this->getReportHeaders($type);
        
        return Excel::download(new class($data, $headers) implements \Maatwebsite\Excel\Concerns\FromArray, \Maatwebsite\Excel\Concerns\WithHeadings {
            private $data;
            private $headers;

            public function __construct($data, $headers)
            {
                $this->data = $data;
                $this->headers = $headers;
            }

            public function array(): array
            {
                return $this->data;
            }

            public function headings(): array
            {
                return $this->headers;
            }
        }, $fileName);
    }

    protected function exportToPdf($data, $type, $title, $fileName, $request)
    {
        $outlet = Outlet::where('user_id', Auth::id())->first();
        
        $pdf = Pdf::loadview('outlets.reports.pdf.template', [
            'data' => $data,
            'title' => $title,
            'type' => $type,
            'headers' => $this->getReportHeaders($type),
            'outlet' => $outlet,
            'filters' => [
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'outlet_id' => $request->outlet_id,
                'doctor_id' => $request->doctor_id,
                'company_id' => $request->company_id,
            ],
            'generated_at' => now()->format('d M Y H:i:s'),
            'generated_by' => Auth::user()->name,
        ]);

        return $pdf->download($fileName);
    }

    protected function generateFileName($type, $format, $request)
    {
        $user = Auth::user();
        $outlet = Outlet::where('user_id', $user->id)->first();
        
        $datePart = Carbon::parse($request->start_date)->format('Ymd') . '_' . 
                   Carbon::parse($request->end_date)->format('Ymd');
        
        $extension = $format === 'excel' ? 'xlsx' : 'pdf';
        
        return sprintf(
            '%s_%s_%s_%s.%s',
            ucfirst($type),
            $outlet ? str_replace(' ', '_', $outlet->name) : 'Outlet',
            $datePart,
            now()->format('His'),
            $extension
        );
    }

    protected function getReportHeaders($type): array
    {
        return match ($type) {
            'rekap_surat' => [
                'Tanggal Input', 'Kode Unik', 'No Surat', 'Tipe', 'Durasi (Hari)', 
                'Tanggal Mulai', 'Tanggal Selesai', 'Tanggal SKB', 'Jam SKB', 
                'Verifikasi', 'Print', 'Tanda Tangan', 'Value TTD', 'Pasien', 
                'ID Pegawai', 'Dokter', 'Klinik', 'Perusahaan', 'Diagnosa', 
                'Notif WA', 'Notif Email', 'Status Edit'
            ],
            'aktivitas_dokter' => ['Dokter', 'Total Surat', 'Surat Sakit (MC)', 'Surat Sehat (SKB)'],
            'aktivitas_perusahaan' => ['Perusahaan', 'Total Surat', 'Surat Sakit (MC)', 'Surat Sehat (SKB)'],
            'statistik_penyakit' => ['Diagnosa', 'Jumlah Kasus'],
            'feedback_pasien' => ['Pasien', 'Klinik', 'Rating', 'Komentar', 'Tanggal'],
            'log_pengguna' => ['User', 'Role', 'Aksi', 'Waktu'],
            'rekap_pasien' => ['Nama Pasien', 'Perusahaan', 'Total Surat'],
            default => []
        };
    }

    protected function resolveData(Request $request): array
    {
        $type = $request->input('type');
        $start = Carbon::parse($request->start_date)->startOfDay();
        $end = Carbon::parse($request->end_date)->endOfDay();

        return match ($type) {
            'rekap_surat' => $this->getRekapSurat($request, $start, $end),
            'aktivitas_dokter' => $this->getAktivitasDokter($request, $start, $end),
            'aktivitas_perusahaan' => $this->getAktivitasPerusahaan($request, $start, $end),
            'statistik_penyakit' => $this->getStatistikPenyakit($request, $start, $end),
            'feedback_pasien' => $this->getFeedbackPasien($request, $start, $end),
            'log_pengguna' => $this->getLogPengguna($request, $start, $end),
            'rekap_pasien' => $this->getRekapPasien($request, $start, $end),
            default => []
        };
    }

    protected function getRekapSurat(Request $request, $start, $end): array
    {
        $user = Auth::user();
        $outlet = Outlet::where('user_id', $user->id)->first();
        
        if (!$outlet) {
            return [];
        }

        // Load with multiple possible relations for diagnosis
        $query = Result::with([
            'patient', 
            'doctor.user', 
            'outlet', 
            'company', 
            'diagnosis',
            'medicalDiagnosis',
            'icdDiagnosis'
        ])
        ->where('outlet_id', $outlet->id)
        ->whereBetween('created_at', [$start, $end]);

        // Apply additional filters
        if ($request->doctor_id) {
            $query->where('doctor_id', $request->doctor_id);
        }
        if ($request->company_id) {
            $query->where('company_id', $request->company_id);
        }
        if ($request->type && in_array($request->type, ['mc', 'skb'])) {
            $query->where('type', $request->type);
        }

        return $query->orderBy('created_at', 'desc')
            ->get()
            ->map(function($r) {
                // Enhanced duration handling
                $durasi = $r->getDurationText();
                
                // Additional check for MC type to ensure duration is always present
                if (strtolower($r->type) === 'mc' && $durasi === '-') {
                    // Try to get duration from raw duration field
                    if (!empty($r->duration)) {
                        $durasi = $r->duration . ' hari';
                    } 
                    // Calculate from dates if available
                    elseif ($r->start_date && $r->end_date) {
                        try {
                            $start = \Carbon\Carbon::parse($r->start_date);
                            $end = \Carbon\Carbon::parse($r->end_date);
                            $calculatedDays = $start->diffInDays($end) + 1;
                            $durasi = $calculatedDays . ' hari';
                        } catch (\Exception $e) {
                            $durasi = '1 hari'; // Default for MC
                        }
                    }
                    // Default fallback for MC
                    else {
                        $durasi = '1 hari';
                    }
                }

                return [
                    $r->formatted_created_at,
                    $r->unique_code ?? '-',
                    $r->no_letters ?? '-',
                    $r->type_upper,
                    $durasi, // Enhanced duration logic
                    $r->start_date ? $r->start_date->format('Y-m-d') : '-',
                    $r->end_date ? $r->end_date->format('Y-m-d') : '-',
                    $r->date ? $r->date->format('Y-m-d') : '-',
                    $r->time ?? '-',
                    $r->verification_date ? $r->verification_date->format('Y-m-d H:i') : '-',
                    $r->print_date ? $r->print_date->format('Y-m-d H:i') : '-',
                    $r->sign_type ?? '-',
                    $r->sign_value ?? '-',
                    $r->patient->full_name ?? '-',
                    $r->patient->identity ?? '-',
                    $r->doctor->user->name ?? '-',
                    $r->outlet->name ?? '-',
                    $r->company->name ?? '-',
                    $r->diagnosis_name, // Using the new accessor
                    $r->notif_wa_text,
                    $r->notif_email_text,
                    $r->edit ?? 'Tidak'
                ];
            })
            ->toArray();
    }

    protected function getAktivitasDokter(Request $request, $start, $end): array
    {
        $user = Auth::user();
        $outlet = Outlet::where('user_id', $user->id)->first();
        
        if (!$outlet) {
            return [];
        }

        $query = Result::with('doctor.user')
            ->where('outlet_id', $outlet->id)
            ->whereBetween('created_at', [$start, $end]);

        if ($request->doctor_id) {
            $query->where('doctor_id', $request->doctor_id);
        }

        return $query->get()
            ->groupBy('doctor_id')
            ->map(fn($group) => [
                $group->first()->doctor->user->name ?? 'Dokter Tidak Diketahui',
                $group->count(),
                $group->where('type', 'mc')->count(),
                $group->where('type', 'skb')->count(),
            ])
            ->values()
            ->toArray();
    }

    protected function getAktivitasPerusahaan(Request $request, $start, $end): array
    {
        $user = Auth::user();
        $outlet = Outlet::where('user_id', $user->id)->first();
        
        if (!$outlet) {
            return [];
        }

        $query = Result::with('company')
            ->where('outlet_id', $outlet->id)
            ->whereBetween('created_at', [$start, $end])
            ->whereNotNull('company_id');

        if ($request->company_id) {
            $query->where('company_id', $request->company_id);
        }

        return $query->get()
            ->groupBy('company_id')
            ->map(fn($group) => [
                $group->first()->company->name ?? 'Perusahaan Tidak Diketahui',
                $group->count(),
                $group->where('type', 'mc')->count(),
                $group->where('type', 'skb')->count(),
            ])
            ->values()
            ->toArray();
    }

    protected function getStatistikPenyakit(Request $request, $start, $end): array
    {
        $user = Auth::user();
        $outlet = Outlet::where('user_id', $user->id)->first();
        
        if (!$outlet) {
            return [];
        }

        $results = Result::with(['diagnosis', 'medicalDiagnosis', 'icdDiagnosis'])
            ->where('outlet_id', $outlet->id)
            ->whereBetween('created_at', [$start, $end])
            ->get()
            ->filter(function($result) {
                return $result->hasDiagnosis(); // Using model method
            });

        // Group by diagnosis and count
        $diagnosisStats = [];
        
        foreach ($results as $result) {
            $diagnosisName = $result->getBestDiagnosis(); // Using model method
            
            if (!isset($diagnosisStats[$diagnosisName])) {
                $diagnosisStats[$diagnosisName] = 0;
            }
            $diagnosisStats[$diagnosisName]++;
        }

        // Convert to array format and sort by count
        return collect($diagnosisStats)
            ->map(fn($count, $diagnosis) => [$diagnosis, $count])
            ->sortByDesc(1)
            ->values()
            ->toArray();
    }

    protected function getFeedbackPasien(Request $request, $start, $end): array
    {
        // Implementasi feedback pasien jika model tersedia
        return [];
    }

    protected function getLogPengguna(Request $request, $start, $end): array
    {
        // Implementasi log pengguna jika model ActivityLog tersedia
        // Untuk sementara return empty array
        return [];
    }

    protected function getRekapPasien(Request $request, $start, $end): array
    {
        $user = Auth::user();
        $outlet = Outlet::where('user_id', $user->id)->first();
        
        if (!$outlet) {
            return [];
        }

        $patients = Patient::with(['company'])
            ->where('outlet_id', $outlet->id)
            ->get();

        return $patients->map(function($patient) use ($start, $end) {
            $totalSurat = Result::where('patient_id', $patient->id)
                ->whereBetween('created_at', [$start, $end])
                ->count();

            return [
                $patient->full_name,
                $patient->company->name ?? 'Individual',
                $totalSurat
            ];
        })
        ->filter(fn($row) => $row[2] > 0) // Only patients with letters
        ->sortByDesc(2) // Sort by total letters desc
        ->values()
        ->toArray();
    }

    // Legacy methods for old reports
    public function indexOld()
    {
        $types = [
            'mc' => 'MC (Medical Certificate)', 
            'skb' => 'SKB (Surat Keterangan Bebas)'
        ];
        
        return view('outlets.reports.old.index', compact('types'));
    }

    public function formOld($type)
    {
        if (!in_array($type, ['mc', 'skb'])) {
            abort(404, 'Tipe laporan tidak valid');
        }

        return view('outlets.reports.old.form', [
            'type' => $type,
            'title' => strtoupper($type) . ' - Data Lama'
        ]);
    }

    public function previewDataOld(Request $request)
    {
        $request->validate([
            'type' => 'required|in:mc,skb',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'page' => 'nullable|integer|min:1'
        ]);

        try {
            $perPage = 100;
            $page = $request->get('page', 1);

            $query = ResultOld::where('type', $request->type)
                ->whereBetween('created_at', [
                    Carbon::parse($request->start_date)->startOfDay(),
                    Carbon::parse($request->end_date)->endOfDay()
                ])
                ->orderBy('created_at', 'desc');

            $results = $query->paginate($perPage, ['*'], 'page', $page);

            return response()->json([
                'data' => $results->items(),
                'current_page' => $results->currentPage(),
                'last_page' => $results->lastPage(),
                'total' => $results->total(),
                'per_page' => $results->perPage(),
            ]);

        } catch (\Throwable $e) {
            Log::error('Failed to preview old report data', [
                'error' => $e->getMessage(),
                'request' => $request->all()
            ]);

            return response()->json([
                'message' => 'Gagal memuat data',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    public function exportOld(Request $request)
    {
        $request->validate([
            'type' => 'required|in:mc,skb',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date'
        ]);

        try {
            $results = ResultOld::where('type', $request->type)
                ->whereBetween('created_at', [
                    Carbon::parse($request->start_date)->startOfDay(),
                    Carbon::parse($request->end_date)->endOfDay()
                ])
                ->orderBy('created_at', 'desc')
                ->get();

            if ($results->isEmpty()) {
                return back()->with('error', 'Tidak ada data untuk diekspor pada periode yang dipilih.');
            }

            $filename = sprintf(
                'Laporan_%s_%s_%s.csv',
                strtoupper($request->type),
                Carbon::parse($request->start_date)->format('Ymd'),
                Carbon::parse($request->end_date)->format('Ymd')
            );

            $headers = [
                'Content-Type' => 'text/csv; charset=utf-8',
                'Content-Disposition' => "attachment; filename=\"$filename\"",
                'Pragma' => 'no-cache',
                'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
                'Expires' => '0'
            ];

            $callback = function () use ($results) {
                $handle = fopen('php://output', 'w');
                
                // Add BOM for UTF-8
                fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
                
                // Headers
                fputcsv($handle, [
                    'Tanggal Dibuat',
                    'No Surat',
                    'Pasien',
                    'Dokter',
                    'Perusahaan',
                    'Outlet',
                    'Durasi (Hari)',
                    'Status'
                ]);

                // Data rows
                foreach ($results as $result) {
                    fputcsv($handle, [
                        $result->created_at ? $result->created_at->format('Y-m-d H:i:s') : '-',
                        $result->no_letters ?? '-',
                        $result->patient ?? '-',
                        $result->doctor ?? '-',
                        $result->company ?? '-',
                        $result->outlet ?? '-',
                        $result->duration ?? '-',
                        $result->status ?? 'Completed'
                    ]);
                }

                fclose($handle);
            };

            Log::info('Old report exported', [
                'type' => $request->type,
                'user_id' => Auth::id(),
                'record_count' => $results->count(),
                'filename' => $filename
            ]);

            return Response::stream($callback, 200, $headers);

        } catch (\Throwable $e) {
            Log::error('Failed to export old report', [
                'error' => $e->getMessage(),
                'request' => $request->all()
            ]);

            return back()->with('error', 'Gagal mengekspor laporan: ' . $e->getMessage());
        }
    }

    /**
     * Get report statistics for dashboard
     */
    public function getReportStats()
    {
        $user = Auth::user();
        $outlet = Outlet::where('user_id', $user->id)->first();
        
        if (!$outlet) {
            return response()->json(['error' => 'Outlet tidak ditemukan'], 404);
        }

        $currentMonth = now()->startOfMonth();
        $lastMonth = now()->subMonth()->startOfMonth();

        $stats = [
            'current_month' => [
                'total' => Result::where('outlet_id', $outlet->id)
                    ->whereBetween('created_at', [$currentMonth, now()])
                    ->count(),
                'mc' => Result::where('outlet_id', $outlet->id)
                    ->where('type', 'mc')
                    ->whereBetween('created_at', [$currentMonth, now()])
                    ->count(),
                'skb' => Result::where('outlet_id', $outlet->id)
                    ->where('type', 'skb')
                    ->whereBetween('created_at', [$currentMonth, now()])
                    ->count(),
            ],
            'last_month' => [
                'total' => Result::where('outlet_id', $outlet->id)
                    ->whereBetween('created_at', [$lastMonth, $lastMonth->copy()->endOfMonth()])
                    ->count(),
            ],
            'total_all_time' => Result::where('outlet_id', $outlet->id)->count(),
            'available_reports' => count($this->reportTypes),
        ];

        // Calculate growth percentage
        $stats['growth_percentage'] = $stats['last_month']['total'] > 0 
            ? round((($stats['current_month']['total'] - $stats['last_month']['total']) / $stats['last_month']['total']) * 100, 2)
            : ($stats['current_month']['total'] > 0 ? 100 : 0);

        return response()->json($stats);
    }

    /**
     * Get quick report data for specific type
     */
    public function getQuickReport(Request $request, $type)
    {
        $request->validate([
            'days' => 'nullable|integer|min:1|max:365'
        ]);

        if (!array_key_exists($type, $this->reportTypes)) {
            return response()->json(['error' => 'Tipe laporan tidak valid'], 400);
        }

        $days = $request->get('days', 30);
        $start = now()->subDays($days)->startOfDay();
        $end = now()->endOfDay();

        $mockRequest = new Request([
            'type' => $type,
            'start_date' => $start->toDateString(),
            'end_date' => $end->toDateString(),
        ]);

        try {
            $data = $this->resolveData($mockRequest);
            
            return response()->json([
                'type' => $type,
                'title' => $this->reportTypes[$type],
                'period' => "{$days} hari terakhir",
                'record_count' => count($data),
                'data' => array_slice($data, 0, 10), // First 10 records only
                'headers' => $this->getReportHeaders($type)
            ]);

        } catch (\Throwable $e) {
            Log::error('Failed to generate quick report', [
                'type' => $type,
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'error' => 'Gagal menggenerate quick report',
                'message' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }
}