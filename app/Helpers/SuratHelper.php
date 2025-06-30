<?php
namespace App\Helpers;

use Illuminate\Support\Str;
use App\Models\Result;

class SuratHelper
{
    public static function generateFilename(Result $result)
    {
        $typeLabel = $result->type === 'mc' ? 'Surat_Sakit' : 'Surat_Sehat';
        $name = Str::slug($result->patient->full_name ?? 'pasien', '_');
        $date = $result->start_date ?? $result->date ?? now()->format('Y-m-d');
        $code = strtoupper($result->unique_code);

        return "{$typeLabel}_{$name}_{$date}_{$code}.pdf";
    }
}
