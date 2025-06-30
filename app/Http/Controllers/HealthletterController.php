<?php

namespace App\Http\Controllers;

use App\Helpers\QrEncryptHelper;
use App\Helpers\SuratHelper;
use App\Services\IP2LocationService;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Result;
use App\Models\Company;
use App\Models\IcdMaster;
use App\Models\MedicalDiagnosis;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Outlet;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\ResultTrashView;
use App\Jobs\GenerateSuratSakitPDF;
use App\Jobs\GenerateSuratSehatPDF;
use App\Models\DocumentQueue;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use Carbon\Carbon;
use Illuminate\Support\Str;


class HealthletterController extends Controller
{
  public function index(Request $request)
    {
        $user = auth()->user();
        $outlet = Outlet::where('email', $user->email)->first();

        if (!$outlet) {
            return redirect()->route('dashboard')->with('error', 'Outlet tidak ditemukan untuk user ini.');
        }

        $outletId = $outlet->id;

        $doctors = \App\Models\Doctor::with('user')
            ->where('outlet_id', $outletId)
            ->get();

        $results = Result::with(['patient', 'doctor.user', 'company', 'outlet'])
            ->where('outlet_id', $outletId)
            ->whereNull('deleted_at') // pastikan hanya yang belum terhapus
            ->when($request->keyword, fn($q) =>
                $q->whereHas('patient', fn($q) =>
                    $q->where('full_name', 'like', '%' . $request->keyword . '%')
                )
            )
            ->when($request->from, fn($q) =>
                $q->whereDate('date', '>=', $request->from)
            )
            ->when($request->to, fn($q) =>
                $q->whereDate('date', '<=', $request->to)
            )
            ->when($request->type, fn($q) =>
                $q->where('type', $request->type)
            )
            ->when($request->doctor_id, fn($q) =>
                $q->where('doctor_id', $request->doctor_id)
            )
            ->latest()
            ->paginate(25)
            ->appends($request->query());

        $data = [
            'results' => $results,
            'doctors' => $doctors,
            'totalLettersAllTime' => Result::where('outlet_id', $outletId)->whereNull('deleted_at')->count(),
            'totalMCAllTime' => Result::where('outlet_id', $outletId)->where('type', 'mc')->whereNull('deleted_at')->count(),
            'totalMCLAllTime' => Result::where('outlet_id', $outletId)->where('type', 'skb')->whereNull('deleted_at')->count(),
            'totalLettersThisMonth' => Result::where('outlet_id', $outletId)->whereMonth('created_at', now()->format('m'))->whereNull('deleted_at')->count(),
            'totalMCThisMonth' => Result::where('outlet_id', $outletId)->where('type', 'mc')->whereMonth('created_at', now()->format('m'))->whereNull('deleted_at')->count(),
            'totalMCLThisMonth' => Result::where('outlet_id', $outletId)->where('type', 'skb')->whereMonth('created_at', now()->format('m'))->whereNull('deleted_at')->count(),
        ];

        return view('outlets.healthletters.index', $data);
    }
    public function createSuratSehat()
{
    $user = auth()->user();
    $outlet = Outlet::where('email', $user->email)->first();

    if (!$outlet) {
        abort(403, 'Outlet tidak ditemukan.');
    }

    $data = [
        'type'       => 'skb',
        'title'      => 'Input Surat Sehat (SKB)',
        'outlet'     => $outlet,
        'companies'  => DB::table('companies')->orderBy('name')->get(),
        'doctors'    => Doctor::where('outlet_id', $outlet->id)->with('user')->get(),
        'templates'  => DB::table('template_results')->where('type', 'skb')->get(),
        'todayDate'  => now()->format('Y-m-d'),
        'nowTime'    => now()->format('H:i'),
    ];

    return view('outlets.results.skbcreate', $data);
}

    public function createSuratSakit()
    {
        $user = auth()->user();
        $outlet = Outlet::where('email', $user->email)->first();

        if (!$outlet) {
            abort(403, 'Outlet tidak ditemukan.');
        }

        $data = [
            'type'       => 'mc',
            'title'      => 'Input Surat Sakit (MC)',
            'outlet'     => $outlet,
            'companies'  => DB::table('companies')->orderBy('name')->get(),
            'doctors'    => Doctor::where('outlet_id', $outlet->id)->with('user')->get(),
            'templates'  => DB::table('template_results')->where('type', 'mc')->get(), // jika ada template MC
            'todayDate'  => now()->format('Y-m-d'),
            'nowTime'    => now()->format('H:i'),
        ];

        return view('outlets.results.mccreate', $data);
    }

    public function storeSuratSehat(Request $request)
    {
        $request->validate([
            'doctor_id'             => 'required|exists:doctors,id',
            'date'                  => 'required|date',
            'time'                  => 'required',
            'sign_type'             => 'nullable|in:qrcode,sign_virtual',
            'sign_value'            => 'nullable|string',
            'patient_id'            => 'nullable|exists:patients,id',
            'patient_name'          => 'required_without:patient_id|string|max:255',
            'dob'                   => 'required_without:patient_id|date',
            'gender'                => 'required_without:patient_id|in:L,P',
            'phone'                 => 'nullable|string|max:20',
            'nik'                   => 'nullable|string|max:50',
            'identity'              => 'nullable|string|max:50',
            'address'               => 'nullable|string|max:255',
            'company_id'            => 'nullable|exists:companies,id',
            'send_notif_wa'         => 'nullable|boolean',
            'send_notif_email'      => 'nullable|boolean',
            'icd_master_id'         => 'nullable|exists:icd_masters,id',
            'icd_name'              => 'nullable|string|max:255',
            'diagnosis_description' => 'nullable|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            $user = auth()->user();
            $outlet = Outlet::where('email', $user->email)->first();

            if (!$outlet) {
                return back()->with('error', 'Outlet tidak ditemukan.')->withInput();
            }

            // 📍 Ambil lokasi IP dari IP2Location
            $ip = $request->ip();
            $locationService = new IP2LocationService();
            $location = $locationService->getLocation($ip);

            $city      = $location['city'] ?? 'UNKNOWN';
            $latitude  = $location['latitude'] ?? null;
            $longitude = $location['longitude'] ?? null;

            // 📋 Buat atau update pasien
            if (!$request->filled('patient_id')) {
                $newUser = User::create([
                    'name'      => $request->patient_name,
                    'email'     => 'auto_' . uniqid() . '@mail.local',
                    'password'  => bcrypt('password123'),
                    'role_type' => 'patient',
                ]);

                $patient = Patient::create([
                    'user_id'      => $newUser->id,
                    'full_name'    => $request->patient_name,
                    'gender'       => $request->gender,
                    'birth_date'   => $request->dob,
                    'phone'        => $request->phone,
                    'nik'          => $request->nik,
                    'identity'     => $request->identity,
                    'address'      => $request->address,
                    'outlet_id'    => $outlet->id,
                    'company_id'   => $request->company_id,
                    'company_name' => $request->company_id ? Company::find($request->company_id)?->name : null,
                ]);
            } else {
                $patient = Patient::findOrFail($request->patient_id);
                $patient->update(array_filter([
                    'company_id' => $request->company_id !== $patient->company_id ? $request->company_id : null,
                    'phone'      => $request->phone !== $patient->phone ? $request->phone : null,
                    'nik'        => $request->nik !== $patient->nik ? $request->nik : null,
                    'identity'   => $request->identity !== $patient->identity ? $request->identity : null,
                    'address'    => $request->address !== $patient->address ? $request->address : null,
                ]));
            }

            // 🧾 Diagnosis
            $diagnosisId = null;
            if ($request->filled('icd_master_id')) {
                $icd = IcdMaster::find($request->icd_master_id);
                $diagnosis = MedicalDiagnosis::create([
                    'outlet_id'       => $outlet->id,
                    'patient_id'      => $patient->id,
                    'doctor_id'       => $request->doctor_id,
                    'icd_master_id'   => $icd->id,
                    'diagnosis_name'  => $request->icd_name ?? $icd->title,
                    'description'     => $request->diagnosis_description ?? $icd->description,
                    'diagnosed_at'    => now(),
                ]);
                $diagnosisId = $diagnosis->id;
            }

            // 🧾 Buat nomor surat & hasil
            $latestNumber = Result::where('outlet_id', $outlet->id)->count() + 1;
            $romanMonth = ['I','II','III','IV','V','VI','VII','VIII','IX','X','XI','XII'][now()->month - 1];
            $noLetters = sprintf("No. %06d/%s/KFD-SMT5/SKB/%s/%d", $latestNumber, $outlet->code, $romanMonth, now()->year);

            $uniqueCode = strtoupper(uniqid('SKB'));
            $qrcodeHash = md5(now() . rand());

            $result = Result::create([
                'outlet_id'         => $outlet->id,
                'patient_id'        => $patient->id,
                'doctor_id'         => $request->doctor_id,
                'company_id'        => $request->company_id,
                'medical_diagnosis_id' => $diagnosisId,
                'type'              => 'skb',
                'unique_code'       => $uniqueCode,
                'no_letters'        => $noLetters,
                'qrcode'            => $qrcodeHash,
                'date'              => $request->date,
                'time'              => $request->time,
                'verification_date' => now(),
                'print_date'        => now(),
                'sign_type'         => $request->sign_type,
                'sign_value'        => $request->sign_value,
                'send_notif_wa'     => $request->boolean('send_notif_wa'),
                'send_notif_email'  => $request->boolean('send_notif_email'),
                'created_ip'        => $ip,
                'created_city'      => $city,
                'created_latitude'  => $latitude,
                'created_longitude' => $longitude,
            ]);

            // 📤 Antrian PDF
            $encryptedCode = QrEncryptHelper::encrypt($uniqueCode);
            $typeLabel = 'Surat_Sehat';
            $sanitizedName = Str::slug($patient->full_name, '_');
            $date = $request->date ?? now()->format('Y-m-d');
            $year = \Carbon\Carbon::parse($request->date)->format('Y');
            $filename = "{$typeLabel}_{$sanitizedName}_{$date}_{$uniqueCode}.pdf";
            $relativePath = "pdfs/skb/{$year}/{$filename}";

            $queue = DocumentQueue::create([
                'result_id'     => $result->id,
                'status'        => 'pending',
                'triggered_by'  => 'auto',
                'no_letters'    => $noLetters,
                'patient_name'  => $patient->full_name,
                'type_surat'    => $result->type,
                'outlet_name'   => $outlet->name ?? $outlet->code,
                'start_date'    => $result->date,
                'expired_date'  => $result->date,
                'filename'      => $relativePath,
            ]);

            GenerateSuratSehatPDF::dispatch($result->id, $queue->id, $filename);

            DB::commit();

            return redirect()->route('outlet.healthletter.index')
                ->with('success', 'Surat Sehat berhasil disimpan & sedang diproses.');

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error($e);
            return back()->with('error', 'Gagal menyimpan surat sehat.')->withInput();
        }
    }
    public function storeSuratSakit(Request $request)
    {
        $request->validate([
            'patient_id'           => 'nullable|exists:patients,id',
            'doctor_id'            => 'required|exists:doctors,id',
            'medical_diagnosis_id' => 'nullable|exists:medical_diagnoses,id',
            'icd_master_id'        => 'nullable|exists:icd_masters,id',
            'icd_name'             => 'nullable|string',
            'diagnosis_description'=> 'nullable|string|max:500',
            'start_date'           => 'required|date',
            'end_date'             => 'nullable|date|after_or_equal:start_date',
            'duration'             => 'required|integer|min:1',
            'sign_type'            => 'nullable|in:qrcode,sign_virtual',
            'sign_value'           => 'nullable|string',
            'send_notif_wa'        => 'nullable|boolean',
            'send_notif_email'     => 'nullable|boolean',
            'company_id'           => 'nullable|exists:companies,id',
            'patient_name'         => 'required_without:patient_id|string|max:255',
            'gender'               => 'nullable|in:L,P',
            'dob'                  => 'nullable|date',
            'phone'                => 'nullable|string',
            'address'              => 'nullable|string',
            'identity'             => 'nullable|string',
            'nik'                  => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $user = auth()->user();
            $outlet = \App\Models\Outlet::where('email', $user->email)->first();

            if (!$outlet) {
                return back()->with('error', 'Outlet tidak ditemukan.')->withInput();
            }

            // Lokasi IP via IP2Location
            $ip = $request->ip();
            $location = (new IP2LocationService())->getLocation($ip);

            $city      = $location['city'] ?? 'UNKNOWN';
            $latitude  = $location['latitude'] ?? null;
            $longitude = $location['longitude'] ?? null;

            // ========== PASIEN ==========
            if (!$request->filled('patient_id')) {
                $user = \App\Models\User::create([
                    'name'      => $request->patient_name,
                    'email'     => 'auto_' . uniqid() . '@mail.local',
                    'password'  => bcrypt('password123'),
                    'role_type' => 'patient',
                ]);

                $patient = \App\Models\Patient::create([
                    'user_id'       => $user->id,
                    'full_name'     => $request->patient_name,
                    'gender'        => $request->gender,
                    'birth_date'    => $request->dob,
                    'phone'         => $request->phone,
                    'nik'           => $request->nik,
                    'identity'      => $request->identity,
                    'address'       => $request->address,
                    'outlet_id'     => $outlet->id,
                    'company_id'    => $request->company_id,
                    'company_name'  => $request->company_id ? \App\Models\Company::find($request->company_id)?->name : null,
                ]);
            } else {
                $patient = \App\Models\Patient::findOrFail($request->patient_id);
                $patient->update(array_filter([
                    'company_id' => $request->company_id !== $patient->company_id ? $request->company_id : null,
                    'phone'      => $request->phone !== $patient->phone ? $request->phone : null,
                    'nik'        => $request->nik !== $patient->nik ? $request->nik : null,
                    'identity'   => $request->identity !== $patient->identity ? $request->identity : null,
                    'address'    => $request->address !== $patient->address ? $request->address : null,
                ]));
            }

            // ========== DIAGNOSIS ==========
            $diagnosisId = null;
            if ($request->filled('icd_master_id')) {
                $icd = \App\Models\IcdMaster::find($request->icd_master_id);
                $diagnosis = \App\Models\MedicalDiagnosis::create([
                    'outlet_id'       => $outlet->id,
                    'patient_id'      => $patient->id,
                    'doctor_id'       => $request->doctor_id,
                    'icd_master_id'   => $icd->id,
                    'diagnosis_name'  => $request->icd_name ?? $icd->title,
                    'description'     => $request->diagnosis_description ?? $icd->description,
                    'diagnosed_at'    => now(),
                ]);
                $diagnosisId = $diagnosis->id;
            }

            // ========== NOMOR SURAT ==========
            $latestNumber = \App\Models\Result::where('outlet_id', $outlet->id)->count() + 1;
            $romanMonth = ['I','II','III','IV','V','VI','VII','VIII','IX','X','XI','XII'][now()->month - 1];
            $noLetters = sprintf("No. %06d/%s/KFD-SMT5/MC/%s/%d", $latestNumber, $outlet->code, $romanMonth, now()->year);

            $uniqueCode = strtoupper(uniqid('MC'));
            $qrcode = md5(now() . rand());

            // ========== SIMPAN RESULT ==========
            $result = \App\Models\Result::create([
                'outlet_id'            => $outlet->id,
                'patient_id'           => $patient->id,
                'doctor_id'            => $request->doctor_id,
                'company_id'           => $request->company_id,
                'medical_diagnosis_id' => $diagnosisId,
                'type'                 => 'mc',
                'unique_code'          => $uniqueCode,
                'qrcode'               => $qrcode,
                'start_date'           => $request->start_date,
                'end_date'             => $request->end_date,
                'duration'             => $request->duration,
                'sign_type'            => $request->sign_type,
                'sign_value'           => $request->sign_value,
                'send_notif_wa'        => $request->boolean('send_notif_wa'),
                'send_notif_email'     => $request->boolean('send_notif_email'),
                'no_letters'           => $noLetters,
                'created_ip'           => $ip,
                'created_city'         => $city,
                'created_latitude'     => $latitude,
                'created_longitude'    => $longitude,
            ]);

            // ========== PDF QUEUE ==========
            $encryptedCode = \App\Helpers\QrEncryptHelper::encrypt($uniqueCode);
            $typeLabel = 'Surat_Sakit';
            $sanitizedName = Str::slug($patient->full_name, '_');
            $date = $request->start_date ?? now()->format('Y-m-d');
            $year = \Carbon\Carbon::parse($request->start_date)->format('Y');
            $filename = "{$typeLabel}_{$sanitizedName}_{$date}_{$uniqueCode}.pdf";
            $relativePath = "pdfs/mc/{$year}/{$filename}";

            $queue = \App\Models\DocumentQueue::create([
                'result_id'     => $result->id,
                'status'        => 'pending',
                'triggered_by'  => 'auto',
                'no_letters'    => $noLetters,
                'patient_name'  => $patient->full_name,
                'outlet_name'   => $outlet->name ?? $outlet->code,
                'type_surat'    => 'mc',
                'start_date'    => $request->start_date,
                'expired_date'  => $request->end_date,
                'filename'      => $relativePath,
            ]);

            \App\Jobs\GenerateSuratSakitPDF::dispatch($result->id, $queue->id, $filename);

            DB::commit();
            return redirect()->route('outlet.healthletter.index')->with('success', 'Surat Sakit berhasil disimpan & sedang diproses.');

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error($e);
            return back()->with('error', 'Gagal menyimpan surat.')->withInput();
        }
    }

    public function show($uniqueCode)
    {
        $result = Result::where('unique_code', $uniqueCode)->firstOrFail();
        return response()->file(Storage::disk('public')->path('documents/' . $result->document_name));
    }

    public function delete($id)
    {
        $result = Result::findOrFail($id);

        DB::transaction(function () use ($result) {
            // Simpan data penghapusan ke tabel result_trash_view
            ResultTrashView::create([
                'id'                => $result->id,
                'deleted_at'        => now(),
                'deleted_ip'        => request()->ip(),
                'deleted_location'  => request()->header('X-Location') ?? '-', // Bisa juga ambil dari service lokasi
                'deleted_by'        => auth()->id(),
                'deleted_outlet_id' => auth()->user()->outlet_id ?? null,
            ]);

            // Soft delete record dari tabel results
            $result->delete();
        });

        return redirect()->route('outlet.healthletter.index')->with('success', 'Surat berhasil dihapus dan masuk ke trash.');
    }

    public function regenerateDocument($id)
    {
        $result = Result::findOrFail($id);
        $pdf = Pdf::loadView('pdf.surat_sehat', compact('result'));
        $filename = 'skb_' . $result->unique_code . '.pdf';
        Storage::disk('public')->put('documents/' . $filename, $pdf->output());
        $result->update(['document_name' => $filename]);

        return redirect()->route('outlet.healthletter.index')->with('success', 'Dokumen berhasil diregenerasi');
    }

    public function signConfirm(Request $request)
    {
        if ($request->sign_type === 'sign_virtual' && !auth()->user()->doctor->sign_virtual) {
            return back()->with('error', 'Setel tanda tangan digital terlebih dahulu.');
        }

        $result = Result::findOrFail($request->id);
        $result->update(['sign_type' => $request->sign_type, 'sign_value' => $request->sign_value]);

        return redirect()->route('outlet.healthletter.index')->with('success', 'Tanda tangan berhasil dikonfirmasi');
    }

    public function apiGetDoctor(Request $request)
    {
        if ($request->ajax()) {
            $doctors = Doctor::select('id', 'name')->where('outlet_id', $request->outlet_id)->get();
            return response()->json(['message' => 'success', 'data' => $doctors]);
        }
        return response()->json(['message' => 'only ajax requests allowed'], 400);
    }

    public function previewSuratSehat($encryptedId)
    {
        try {
            $id = Crypt::decrypt($encryptedId);
        } catch (DecryptException $e) {
            abort(403, 'Link tidak valid atau kadaluarsa.');
        }

        $result = Result::findOrFail($id);
        $filePath = $result->document_name;

        if (!$filePath || !Storage::disk('public')->exists($filePath)) {
            return back()->with('error', 'Dokumen belum tersedia atau belum digenerate.');
        }

        return response()->file(storage_path("app/public/{$filePath}"), [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . basename($filePath) . '"'
        ]);
    }

    public function previewSuratSakit($encryptedId)
    {
        try {
            $id = Crypt::decrypt($encryptedId);
        } catch (DecryptException $e) {
            abort(403, 'Link tidak valid atau kadaluarsa.');
        }

        $result = Result::findOrFail($id);
        $filePath = $result->document_name;

        if (!$filePath || !Storage::disk('public')->exists($filePath)) {
            abort(404, 'File dokumen tidak ditemukan.');
        }

        $fullPath = storage_path('app/public/' . $filePath);

        return response()->file($fullPath, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . basename($filePath) . '"'
        ]);
    }
    public function showSuratSehat($uniqueCode) {
        $result = Result::where('unique_code', $uniqueCode)->firstOrFail();
        return response()->file(Storage::disk('public')->path('documents/' . $result->document_name));
    }

    public function showSuratSakit($uniqueCode) {
        $result = Result::where('unique_code', $uniqueCode)->firstOrFail();
        return response()->file(Storage::disk('public')->path('documents/' . $result->document_name));
    }

    public function regenerateSuratSehat($id)
    {
        $result = Result::with(['patient', 'outlet'])->findOrFail($id);
        $outlet = $result->outlet;

        $patientName = $result->patient->full_name 
            ?? $result->patient->name 
            ?? 'Pasien Tanpa Nama';

        $queue = DocumentQueue::create([
            'result_id'     => $result->id,
            'status'        => 'pending',
            'triggered_by'  => 'manual',
            'no_letters'    => $result->no_letters,
            'patient_name'  => $patientName,
            'type_surat'    => $result->type,
            'start_date'    => $result->start_date,
            'outlet_name'   => $outlet?->name ?? $outlet?->code ?? 'Outlet Tidak Dikenali',
            'expired_date'  => $result->start_date && $result->duration
                                ? \Carbon\Carbon::parse($result->start_date)->addDays($result->duration)
                                : null,
        ]);

        GenerateSuratSehatPDF::dispatch($result->id, $queue->id);

        return redirect()->route('outlet.healthletter.index')
            ->with('success', 'Permintaan generate ulang surat sehat sedang diproses.');
    }

   public function regenerateSuratSakit($id)
    {
        $result = Result::with('patient')->findOrFail($id);
        $outlet = $result->outlet;
        
        $patientName = $result->patient->full_name 
            ?? $result->patient->name 
            ?? 'Pasien Tanpa Nama';

        // Buat antrian generate ulang
        $queue = DocumentQueue::create([
            'result_id'     => $result->id,
            'status'        => 'pending',
            'triggered_by'  => 'manual',

            // Snapshot data untuk monitoring
            'no_letters'    => $result->no_letters,
            'patient_name'  => $patientName,
            'type_surat'    => $result->type,
            'start_date'    => $result->start_date,
            'outlet_name'   => $outlet->name ?? $outlet->code,
            'expired_date'  => $result->start_date && $result->duration
                ? \Carbon\Carbon::parse($result->start_date)->addDays($result->duration)
                : null,
        ]);

        // Dispatch ke queue
        \App\Jobs\GenerateSuratSakitPDF::dispatch($result->id, $queue->id);

        return redirect()
            ->route('outlet.healthletter.index')
            ->with('success', 'Permintaan generate ulang surat sakit sedang diproses.');
    }
    public function editSuratSehat($id)
    {
        $result = Result::with(['patient', 'doctor.user', 'outlet'])->findOrFail($id);
        $doctors = Doctor::with('user')->get();

        return view('outlets.results.edit_skb', [
            'result' => $result,
            'doctors' => $doctors,
            'outlet' => $result->outlet,
        ]);
    }
    public function editSuratSakit($id) {
       $result = Result::with(['patient', 'doctor.user', 'outlet'])->findOrFail($id);
        $doctors = Doctor::with('user')->get();

        return view('outlets.results.edit_mc', [
            'result' => $result,
            'doctors' => $doctors,
            'outlet' => $result->outlet,
        ]);
    }
    public function updateSuratSehat(Request $request, $id)
    {
        $request->validate([
            'patient_name'     => 'required|string|max:255',
            'dob'              => 'nullable|date',
            'gender'           => 'nullable|in:L,P',
            'phone'            => 'nullable|string|max:20',
            'nik'              => 'nullable|string|max:50',
            'identity'         => 'nullable|string|max:50',
            'address'          => 'nullable|string|max:255',
            'date'             => 'required|date',
            'time'             => 'required',
            'doctor_id'        => 'required|exists:doctors,id',
            'send_notif_wa'    => 'nullable|boolean',
            'send_notif_email' => 'nullable|boolean',
        ]);

        try {
            DB::beginTransaction();

            // Ambil result dan pastikan milik outlet yang sesuai (opsional, jika perlu autentikasi outlet)
            $user = auth()->user();
            $outlet = Outlet::where('email', $user->email)->first();

            if (!$outlet) {
                return back()->with('error', 'Outlet tidak ditemukan.');
            }

            $result = Result::with('patient')->where('outlet_id', $outlet->id)->findOrFail($id);

            // Update data pasien
            if ($result->patient) {
                $result->patient->update([
                    'full_name' => $request->patient_name,
                    'birth_date' => $request->dob,
                    'gender' => $request->gender,
                    'phone' => $request->phone,
                    'nik' => $request->nik,
                    'identity' => $request->identity,
                    'address' => $request->address,
                ]);
            }

            // Update data surat sehat
            $result->update([
                'doctor_id' => $request->doctor_id,
                'date' => $request->date,
                'time' => $request->time,
                'send_notif_wa' => $request->boolean('send_notif_wa'),
                'send_notif_email' => $request->boolean('send_notif_email'),
                'edit' => 'yes',
            ]);

            DB::commit();

            return redirect()->route('outlet.healthletter.index')
                ->with('success', 'Surat SKB berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Update SKB Error: ' . $e->getMessage());

            return back()->with('error', 'Gagal memperbarui surat.')->withInput();
        }
    }
    public function updateSuratSakit(Request $request, $id)
    {
        $request->validate([
            'patient_id'           => 'nullable|exists:patients,id',
            'doctor_id'            => 'required|exists:doctors,id',
            'medical_diagnosis_id' => 'nullable|exists:medical_diagnoses,id',
            'icd_master_id'        => 'nullable|exists:icd_masters,id',
            'icd_name'             => 'nullable|string',
            'diagnosis_description'=> 'nullable|string|max:500',
            'start_date'           => 'required|date',
            'end_date'             => 'nullable|date|after_or_equal:start_date',
            'duration'             => 'required|integer|min:1',
            'sign_type'            => 'nullable|in:qrcode,sign_virtual',
            'sign_value'           => 'nullable|string',
            'send_notif_wa'        => 'nullable|boolean',
            'send_notif_email'     => 'nullable|boolean',
            'company_id'           => 'nullable|exists:companies,id',
            'patient_name'         => 'required_without:patient_id|string|max:255',
            'gender'               => 'nullable|in:L,P',
            'dob'                  => 'nullable|date',
            'phone'                => 'nullable|string',
            'address'              => 'nullable|string',
            'identity'             => 'nullable|string',
            'nik'                  => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $outlet = auth()->user()->outlet;
            $result = \App\Models\Result::where('id', $id)->where('outlet_id', $outlet->id)->firstOrFail();

            // ========== PASIEN ==========
            if (!$request->filled('patient_id')) {
                $user = \App\Models\User::create([
                    'name'      => $request->patient_name,
                    'email'     => 'auto_' . uniqid() . '@mail.local',
                    'password'  => bcrypt('password123'),
                    'role_type' => 'patient',
                ]);

                $patient = \App\Models\Patient::create([
                    'user_id'       => $user->id,
                    'full_name'     => $request->patient_name,
                    'gender'        => $request->gender,
                    'birth_date'    => $request->dob,
                    'phone'         => $request->phone,
                    'nik'           => $request->nik,
                    'identity'      => $request->identity,
                    'address'       => $request->address,
                    'outlet_id'     => $outlet->id,
                    'company_id'    => $request->company_id,
                    'company_name'  => $request->company_id ? \App\Models\Company::find($request->company_id)?->name : null,
                ]);
            } else {
                $patient = \App\Models\Patient::findOrFail($request->patient_id);
                $patient->update([
                    'full_name'     => $request->patient_name,
                    'gender'        => $request->gender,
                    'birth_date'    => $request->dob,
                    'phone'         => $request->phone,
                    'nik'           => $request->nik,
                    'identity'      => $request->identity,
                    'address'       => $request->address,
                    'company_id'    => $request->company_id,
                    'company_name'  => $request->company_id ? \App\Models\Company::find($request->company_id)?->name : null,
                ]);
            }

            // ========== DIAGNOSIS ==========
            $diagnosisId = $result->medical_diagnosis_id;

            if ($request->filled('icd_master_id')) {
                $icd = \App\Models\IcdMaster::find($request->icd_master_id);
                $diagnosis = \App\Models\MedicalDiagnosis::updateOrCreate(
                    ['id' => $diagnosisId],
                    [
                        'outlet_id'      => $outlet->id,
                        'patient_id'     => $patient->id,
                        'doctor_id'      => $request->doctor_id,
                        'icd_master_id'  => $icd->id,
                        'diagnosis_name' => $request->icd_name ?? $icd->title,
                        'description'    => $request->diagnosis_description ?? $icd->description,
                        'diagnosed_at'   => now(),
                    ]
                );
                $diagnosisId = $diagnosis->id;
            }

            // ========== UPDATE RESULT ==========
            $result->update([
                'patient_id'           => $patient->id,
                'doctor_id'            => $request->doctor_id,
                'company_id'           => $request->company_id,
                'medical_diagnosis_id' => $diagnosisId,
                'start_date'           => $request->start_date,
                'end_date'             => $request->end_date,
                'duration'             => $request->duration,
                'sign_type'            => $request->sign_type,
                'sign_value'           => $request->sign_value,
                'send_notif_wa'        => $request->boolean('send_notif_wa'),
                'send_notif_email'     => $request->boolean('send_notif_email'),
                'edit'                 => 'yes',
            ]);

            DB::commit();
            return redirect()->route('outlet.healthletter.index')->with('success', 'Surat Sakit berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
            return back()->with('error', 'Gagal memperbarui surat.')->withInput();
        }
    }


    public function deleteSuratSehat($id)
{
    $user = auth()->user();
    $outlet = Outlet::where('user_id', $user->id)->first();

    if (!$outlet) {
        return back()->with('error', '⚠️ Outlet tidak ditemukan. Silakan hubungi admin.');
    }

    try {
        $result = Result::where('id', $id)
            ->where('type', 'skb')
            ->where('outlet_id', $outlet->id)
            ->first();

        if (!$result) {
            Log::warning("Gagal hapus: Result tidak ditemukan. ID: $id, Outlet ID: {$outlet->id}");
            return back()->with('error', '⚠️ Surat tidak ditemukan untuk outlet ini.');
        }

        DB::transaction(function () use ($result, $outlet) {
            ResultTrashView::create([
                'id'                => $result->id,
                'deleted_at'        => now(),
                'deleted_ip'        => request()->ip(),
                'deleted_location'  => request()->header('X-Location') ?? '-',
                'deleted_by'        => auth()->id(),
                'deleted_outlet_id' => $outlet->id,
            ]);

            $result->delete();
        });

        return redirect()->route('outlet.healthletter.index')
            ->with('success', '✅ Surat SKB berhasil dihapus dan masuk trash.');
    } catch (\Exception $e) {
        Log::error("Gagal menghapus surat SKB ID $id: " . $e->getMessage(), [
            'user_id' => auth()->id(),
            'outlet_id' => $outlet->id ?? null,
            'trace' => $e->getTraceAsString(),
        ]);

        return back()->with('error', '❌ Terjadi kesalahan saat menghapus surat. Silakan coba lagi atau hubungi admin.');
    }
}

    public function deleteSuratSakit($id)
    {
        $user = auth()->user();
        $outlet = Outlet::where('user_id', $user->id)->first();

        if (!$outlet) {
            return back()->with('error', '⚠️ Outlet tidak ditemukan. Silakan hubungi admin.');
        }

        $result = Result::where('id', $id)
            ->where('type', 'mc')
            ->where('outlet_id', $outlet->id)
            ->firstOrFail();

        DB::transaction(function () use ($result, $outlet) {
            ResultTrashView::create([
                'id'                => $result->id,
                'deleted_at'        => now(),
                'deleted_ip'        => request()->ip(),
                'deleted_location'  => request()->header('X-Location') ?? '-',
                'deleted_by'        => auth()->id(),
                'deleted_outlet_id' => $outlet->id,
            ]);

            $result->delete();
        });

        return redirect()->route('outlet.healthletter.index')
            ->with('success', '✅ Surat MC berhasil dihapus dan masuk trash.');
    }
    public function signConfirmSuratSehat(Request $request) {
        // Tanda tangan surat sehat
    }

    public function signConfirmSuratSakit(Request $request) {
        // Tanda tangan surat sakit
    }

    public function bulkingRegenerateSuratSehat(Request $request) {
        // Logic bulk generate surat sehat
    }

    public function liveSearch(Request $request)
    {
        $q = $request->q;

        $patients = \App\Models\Patient::with('user')
            ->whereHas('user', fn ($query) => $query->where('name', 'like', "%$q%"))
            ->limit(10)
            ->get()
            ->map(function ($patient) {
                return [
                    'id' => $patient->id,
                    'name' => $patient->user->name,
                    'dob' => $patient->birth_date,
                    'gender' => $patient->gender,
                    'phone_number' => $patient->phone,
                    'address' => $patient->address,
                ];
            });

        return response()->json($patients);
    }

    // STORECOMAPNY
    public function storeCompany(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|unique:companies',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:20',
            'industry_type' => 'nullable|string|max:100',
            'registration_number' => 'nullable|string|max:100',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'province' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
        ]);

        Company::create($validated + [
            'is_active' => $request->has('is_active'),
        ]);

        return back()->with('success', 'Perusahaan berhasil ditambahkan.');
    }

    public function destroyQueue($id)
    {
        $queue = \App\Models\DocumentQueue::findOrFail($id);

        if ($queue->status !== 'pending') {
            return back()->with('error', 'Hanya queue dengan status pending yang dapat dihapus.');
        }

        $queue->delete();
        return back()->with('success', 'Queue berhasil dihapus.');
    }

   public function indexTrash()
    {
        $trashed = \App\Models\ResultTrashView::with(['user', 'outlet'])
            ->orderByDesc('deleted_at')
            ->paginate(20);

        return view('outlets.results.trash', compact('trashed'));
    }

    public function restore($id)
    {
        $trashed = ResultTrashView::findOrFail($id);
        $result = Result::withTrashed()->find($trashed->id);

        if ($result && $result->trashed()) {
            DB::transaction(function () use ($result, $trashed) {
                $result->restore(); // gunakan Eloquent restore
                $trashed->delete(); // hapus dari trash view
            });

            return redirect()->back()->with('success', '✅ Surat berhasil direstore.');
        }

        return redirect()->back()->with('error', '❌ Data tidak ditemukan atau sudah aktif.');
    }

}
