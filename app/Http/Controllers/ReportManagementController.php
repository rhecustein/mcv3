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
use Illuminate\Support\Facades\Response;
use App\Models\ActivityLog;
use App\Exports\GenericArrayExport;
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
        return view('outlets.reports.index', [
            'reportTypes' => $this->reportTypes,
            'outlets' => \App\Models\Outlet::select('id', 'name')->orderBy('name')->get(),
            'doctors' => \App\Models\Doctor::with('user:id,name')->select('id', 'user_id')->get(),
            'companies' => \App\Models\Company::select('id', 'name')->orderBy('name')->get(),
        ]);
    }

    public function previewData(Request $request)
    {
        try {
            return response()->json($this->resolveData($request));
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Gagal memuat data.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function export(Request $request)
    {
        $format = $request->input('format', 'pdf');
        $type = $request->input('type');
        $data = $this->resolveData($request);

        $titles = [
            'rekap_surat' => ['Tanggal Input', 'Kode', 'No Surat', 'Tipe', 'Durasi', 'Tgl Mulai', 'Tgl Selesai', 'Tgl SKB', 'Jam SKB', 'Verifikasi', 'Print', 'Tanda Tangan', 'Value TTD', 'Pasien', 'ID Pegawai', 'Dokter', 'Klinik', 'Perusahaan', 'Diagnosa', 'Notif WA', 'Notif Email', 'Edit'],
            'aktivitas_dokter' => ['Dokter', 'Total Surat', 'MC', 'SKB'],
            'aktivitas_perusahaan' => ['Perusahaan', 'Total Surat', 'MC', 'SKB'],
            'statistik_penyakit' => ['Diagnosa', 'Jumlah'],
            'feedback_pasien' => ['Pasien', 'Klinik', 'Rating', 'Komentar', 'Tanggal'],
            'log_pengguna' => ['User', 'Role', 'Aksi', 'Waktu'],
            'rekap_pasien' => ['Nama Pasien', 'Perusahaan', 'Total Surat'],
        ];

        $fileName = $type . '.' . ($format === 'excel' ? 'xlsx' : 'pdf');
        $title = $this->reportTypes[$type] ?? 'Laporan';

        return $format === 'excel'
            ? Excel::download(new GenericArrayExport($data, $titles[$type] ?? []), $fileName)
            : Pdf::loadView('exports.default_pdf', ['data' => $data, 'title' => $title])->download($fileName);
    }

    protected function resolveData(Request $request): array
    {
        $type = $request->input('type');
        $start = Carbon::parse($request->start_date)->startOfDay();
        $end = Carbon::parse($request->end_date)->endOfDay();

        return match ($type) {
            'rekap_surat' => $this->getRekapSurat($request, $start, $end),
            'aktivitas_dokter' => $this->getAktivitasDokter($start, $end),
            'aktivitas_perusahaan' => $this->getAktivitasPerusahaan($start, $end),
            'statistik_penyakit' => $this->getStatistikPenyakit($start, $end),
            'feedback_pasien' => $this->getFeedbackPasien($start, $end),
            'log_pengguna' => $this->getLogPengguna($start, $end),
            'rekap_pasien' => $this->getRekapPasien($start, $end),
            default => []
        };
    }

    protected function getRekapSurat(Request $request, $start, $end): array
    {
        return Result::with(['patient', 'doctor.user', 'outlet', 'company', 'medicalDiagnosis'])
            ->whereBetween('created_at', [$start, $end])
            ->when($request->outlet_id, fn($q) => $q->where('outlet_id', $request->outlet_id))
            ->when($request->doctor_id, fn($q) => $q->where('doctor_id', $request->doctor_id))
            ->when($request->company_id, fn($q) => $q->where('company_id', $request->company_id))
            ->when($request->type, fn($q) => $q->where('type', $request->type))
            ->get()
            ->map(fn($r) => [
                'tanggal_input' => $r->created_at->format('Y-m-d H:i'),
                'unique_code' => $r->unique_code,
                'no_surat' => $r->no_letters,
                'tipe' => strtoupper($r->type),
                'durasi_hari' => $r->duration,
                'tanggal_mulai' => $r->start_date,
                'tanggal_selesai' => $r->end_date,
                'tanggal_skb' => $r->date,
                'jam_skb' => $r->time,
                'verifikasi' => $r->verification_date,
                'print' => $r->print_date,
                'tanda_tangan' => $r->sign_type,
                'value_ttd' => $r->sign_value,
                'pasien' => $r->patient->full_name ?? '-',
                'employee_idcard' => $r->employee_idcard,
                'dokter' => $r->doctor->user->name ?? '-',
                'klinik' => $r->outlet->name ?? '-',
                'perusahaan' => $r->company->name ?? '-',
                'diagnosa' => $r->medicalDiagnosis->name ?? '-',
                'notif_wa' => $r->notif_wa ? '✔️' : '❌',
                'notif_email' => $r->notif_email ? '✔️' : '❌',
                'diubah' => $r->edit,
            ])
            ->toArray();
    }

    protected function getAktivitasDokter($start, $end): array
    {
        return Result::with('doctor.user')
            ->whereBetween('created_at', [$start, $end])
            ->get()
            ->groupBy('doctor_id')
            ->map(fn($g) => [
                'dokter' => $g->first()->doctor->user->name ?? '-',
                'total_surat' => $g->count(),
                'mc' => $g->where('type', 'mc')->count(),
                'skb' => $g->where('type', 'skb')->count(),
            ])->values()->toArray();
    }

    protected function getAktivitasPerusahaan($start, $end): array
    {
        return Result::with('company')
            ->whereBetween('created_at', [$start, $end])
            ->whereNotNull('company_id')
            ->get()
            ->groupBy('company_id')
            ->map(fn($g) => [
                'perusahaan' => $g->first()->company->name ?? '-',
                'total_surat' => $g->count(),
                'mc' => $g->where('type', 'mc')->count(),
                'skb' => $g->where('type', 'skb')->count(),
            ])->values()->toArray();
    }

    protected function getStatistikPenyakit($start, $end): array
    {
        return Result::with('medicalDiagnosis')
            ->whereBetween('created_at', [$start, $end])
            ->whereNotNull('medical_diagnosis_id')
            ->get()
            ->groupBy('medical_diagnosis_id')
            ->map(fn($g) => [
                'diagnosa' => $g->first()->medicalDiagnosis->name ?? '-',
                'jumlah' => $g->count()
            ])->values()->toArray();
    }

    protected function getFeedbackPasien($start, $end): array
    {
        return []; // implementasi jika model tersedia
    }

    protected function getLogPengguna($start, $end): array
    {
        return ActivityLog::with('causer.roles')
            ->whereBetween('created_at', [$start, $end])
            ->get()
            ->map(fn($log) => [
                'user' => $log->causer->name ?? '-',
                'role' => $log->causer->roles->pluck('name')->implode(', ') ?? '-',
                'aksi' => $log->description,
                'waktu' => $log->created_at->format('Y-m-d H:i:s')
            ])->toArray();
    }

    protected function getRekapPasien($start, $end): array
    {
        return Patient::with('company')
            ->withCount(['results as total_surat' => fn($q) => $q->whereBetween('created_at', [$start, $end])])
            ->get()
            ->map(fn($p) => [
                'nama_pasien' => $p->full_name,
                'perusahaan' => $p->company->name ?? '-',
                'total_surat' => $p->total_surat
            ])
            ->toArray();
    }

    public function indexOld()
    {
        $types = ['mc' => 'MC (Medical Certificate)', 'skb' => 'SKB (Surat Keterangan Bebas)'];
        return view('outlets.reports.old.index', compact('types'));
    }

    public function formOld($type)
    {
        if (!in_array($type, ['mc', 'skb'])) {
            abort(404);
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

        $perPage = 100;
        $page = $request->get('page', 1);

        $query = ResultOld::where('type', $request->type)
            ->whereBetween('created_at', [
                now()->parse($request->start_date)->startOfDay(),
                now()->parse($request->end_date)->endOfDay()
            ])
            ->orderBy('created_at', 'asc');

        $results = $query->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'data' => $results->items(),
            'current_page' => $results->currentPage(),
            'last_page' => $results->lastPage(),
            'total' => $results->total(),
            'per_page' => $results->perPage(),
        ]);
    }


    public function exportOld(Request $request)
    {
        $request->validate([
            'type' => 'required|in:mc,skb',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date'
        ]);

        $results = ResultOld::where('type', $request->type)
            ->whereBetween('created_at', [
                now()->parse($request->start_date)->startOfDay(),
                now()->parse($request->end_date)->endOfDay()
            ])
            ->orderBy('created_at', 'asc')
            ->get();

        $filename = 'Laporan_' . strtoupper($request->type) . '_' . now()->format('Ymd_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($results) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, [
                'Tanggal Dibuat',
                'No Surat',
                'Pasien',
                'Dokter',
                'Perusahaan',
                'Outlet',
                'Durasi',
            ]);

            foreach ($results as $r) {
                fputcsv($handle, [
                    optional($r->created_at)->format('Y-m-d H:i'),
                    $r->no_letters,
                    $r->patient,
                    $r->doctor,
                    $r->company,
                    $r->outlet,
                    $r->duration,
                ]);
            }

            fclose($handle);
        };

        return Response::stream($callback, 200, $headers);
    }
}
