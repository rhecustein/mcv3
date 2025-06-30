<?php

namespace App\Jobs;

use App\Models\Result;
use App\Models\DocumentQueue;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Str;

class GenerateSuratSakitPDF implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public int $resultId,
        public int $queueRecordId,
        public ?string $customFilename = null
    ) {}

    public function handle(): void
    {
        $queue = DocumentQueue::find($this->queueRecordId);
        if (!$queue) return;

        $queue->update([
            'status' => 'processing',
            'processed_at' => now()
        ]);

        try {
            $result = Result::with(['patient', 'doctor.user', 'outlet'])->findOrFail($this->resultId);

            if (!$result->outlet || !$result->patient || !$result->doctor) {
                throw new \Exception('Relasi tidak lengkap: outlet / doctor / patient null.');
            }

            // Penentuan nama file
            $typeLabel = 'Surat_Sakit';
            $patientName = Str::slug($result->patient->full_name ?? 'pasien', '_');
            $date = Carbon::parse($result->start_date ?? now())->format('Y-m-d');
            $uniqueCode = strtoupper($result->unique_code ?? Str::random(8));
            $year = Carbon::parse($result->start_date ?? now())->format('Y');

            $filename = $this->customFilename ?: "{$typeLabel}_{$patientName}_{$date}_{$uniqueCode}.pdf";
            $relativePath = "pdfs/mc/{$year}/{$filename}";
            $storagePath = "public/{$relativePath}";

            Storage::disk('public')->makeDirectory("pdfs/mc/{$year}");

            // Generate PDF
            $pdf = Pdf::loadView('pdf.surat_sakit', compact('result'));
            Storage::disk('public')->put($relativePath, $pdf->output());

            $result->update([
                'document_name' => $relativePath,
            ]);

            $queue->update([
                'status'         => 'success',
                'generated_file' => $relativePath,
                'completed_at'   => now(),
                'no_letters'     => $result->no_letters,
                'patient_name'   => $result->patient->full_name,
                'type_surat'     => $result->type,
                'start_date'     => $result->start_date,
                'expired_date'   => $result->end_date ?? (
                    $result->start_date && $result->duration
                        ? Carbon::parse($result->start_date)->addDays($result->duration - 1)
                        : null
                ),
            ]);
        } catch (Exception $e) {
            Log::error('❌ Gagal generate PDF Surat Sakit', [
                'result_id' => $this->resultId,
                'queue_id' => $this->queueRecordId,
                'error' => $e->getMessage(),
            ]);

            $queue->update([
                'status'      => 'failed',
                'error_log'   => $e->getMessage(),
                'retry_count' => $queue->retry_count + 1
            ]);
        }
    }
}
