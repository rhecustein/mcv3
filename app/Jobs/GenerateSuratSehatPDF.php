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
use Exception;
use Illuminate\Support\Str;
use Carbon\Carbon;

class GenerateSuratSehatPDF implements ShouldQueue
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

            if (!$result->patient || !$result->doctor || !$result->outlet) {
                throw new \Exception('Relasi tidak lengkap: outlet / doctor / patient null.');
            }

            // === Nama File Dinamis ===
            $typeLabel = 'Surat_Sehat';
            $name = Str::slug($result->patient->full_name ?? 'pasien', '_');
            $date = Carbon::parse($result->date ?? now())->format('Y-m-d');
            $code = strtoupper($result->unique_code ?? Str::random(6));
            $year = Carbon::parse($result->date ?? now())->format('Y');

            $filename = $this->customFilename ?: "{$typeLabel}_{$name}_{$date}_{$code}.pdf";
            $relativePath = "pdfs/skb/{$year}/{$filename}";
            $storagePath = "public/{$relativePath}";

            // === Pastikan Folder Ada
            Storage::disk('public')->makeDirectory("pdfs/skb/{$year}");

            // === Generate PDF
            $pdf = Pdf::loadView('pdf.surat_sehat', compact('result'));
            Storage::disk('public')->put($relativePath, $pdf->output());

            // === Update Result
            $result->update([
                'document_name' => $relativePath
            ]);

            // === Update Queue
            $queue->update([
                'status'         => 'success',
                'generated_file' => $relativePath,
                'completed_at'   => now(),
                'no_letters'     => $result->no_letters,
                'patient_name'   => $result->patient->full_name ?? null,
                'type_surat'     => $result->type,
                'start_date'     => $result->date,
                'expired_date'   => $result->date,
            ]);

        } catch (Exception $e) {
            Log::error('❌ Gagal generate PDF Surat Sehat', [
                'result_id' => $this->resultId,
                'queue_id' => $this->queueRecordId,
                'error' => $e->getMessage(),
            ]);

            $queue->update([
                'status'       => 'failed',
                'error_log'    => $e->getMessage(),
                'retry_count'  => $queue->retry_count + 1
            ]);
        }
    }
}
