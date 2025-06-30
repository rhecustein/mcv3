<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Result;
use App\Helpers\QrEncryptHelper;

class PublicResultController extends Controller
{
    /**
     * Verifikasi surat sehat/sakit via QR code
     * URL format: /verify/{encrypted_code}
     */
    public function verify($code)
    {
        // Dekripsi kode
        $uniqueCode = QrEncryptHelper::decrypt($code);

        if (!$uniqueCode) {
            return response()->view('verify.invalid', [], 404);
        }

        // Ambil result berdasarkan kode unik
        $result = Result::where('unique_code', $uniqueCode)
            ->with([
                'patient',
                'doctor.user',
                'outlet',
                'diagnosis.icd'
            ])
            ->first();

        if (!$result) {
            return response()->view('verify.invalid', [], 404);
        }

        return view('verify.result', compact('result'));
    }
}
