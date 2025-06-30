<?php

namespace App\Http\Controllers;

use App\Models\Result;
use Carbon\Carbon;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class ResultController extends Controller
{

    public function download($encryptedId)
    {
        $id = Crypt::decrypt($encryptedId);
        $result = Result::with('patient')->findOrFail($id);

        $type = $result->type; // 'mc' atau 'skb'

        // Validasi type
        if (!in_array($type, ['mc', 'skb'])) {
            abort(404, 'Jenis dokumen tidak dikenali.');
        }

        // Buat ulang nama file & path sesuai struktur yang kamu berikan
        $year = Carbon::parse($type === 'mc' ? $result->start_date : $result->date)->format('Y');
        $typeLabel = $type === 'mc' ? 'Surat_Sakit' : 'Surat_Sehat';
        $patientName = Str::slug($result->patient->full_name ?? 'pasien', '_');
        $date = Carbon::parse($type === 'mc' ? $result->start_date : $result->date)->format('Y-m-d');
        $code = strtoupper($result->unique_code ?? Str::random(6));
        $filename = "{$typeLabel}_{$patientName}_{$date}_{$code}.pdf";

        $path = "pdfs/{$type}/{$year}/{$filename}";

        // Cek apakah file ada di disk 'public'
        if (!Storage::disk('public')->exists($path)) {
            abort(404, 'File tidak ditemukan di penyimpanan.');
        }

        return Storage::disk('public')->download($path, $filename);
    }
        /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Result $result)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Result $result)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Result $result)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Result $result)
    {
        //
    }
}
