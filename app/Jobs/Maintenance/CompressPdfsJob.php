<?php

namespace App\Jobs\Maintenance;

use App\Models\Result;
use App\Models\PdfStorageSetting;
use App\Services\PdfCompressionService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class CompressPdfsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 1;
    public $timeout = 3600; // 1 hour

    /**
     * Create a new job instance.
     *
     * @param int $daysOld Compress PDFs older than this many days
     * @param string|null $tenantId Specific tenant or null for all
     * @param string|null $quality Compression quality (null = auto-determine based on age)
     * @param int $limit Max PDFs to compress per run
     */
    public function __construct(
        public int $daysOld = 30,
        public ?string $tenantId = null,
        public ?string $quality = null,
        public int $limit = 1000
    ) {
        $this->onQueue('maintenance');
    }

    /**
     * Execute the job.
     */
    public function handle(PdfCompressionService $compressionService): void
    {
        $startTime = now();
        $cutoffDate = Carbon::now()->subDays($this->daysOld);

        Log::info('Starting PDF compression job', [
            'days_old' => $this->daysOld,
            'cutoff_date' => $cutoffDate->toDateTimeString(),
            'tenant_id' => $this->tenantId ?? 'all',
            'quality' => $this->quality ?? 'auto',
            'limit' => $this->limit,
        ]);

        // Query old PDFs that haven't been compressed yet
        $query = Result::where('created_at', '<', $cutoffDate)
            ->whereNotNull('pdf_path')
            ->where('pdf_compressed', false)
            ->whereNull('pdf_deleted_at');

        if ($this->tenantId) {
            $query->where('tenant_id', $this->tenantId);
        }

        $results = $query->limit($this->limit)->get();
        $totalResults = $results->count();

        Log::info("Found {$totalResults} PDFs to compress");

        $compressedCount = 0;
        $errorCount = 0;
        $totalSavedBytes = 0;
        $totalOriginalBytes = 0;

        foreach ($results as $result) {
            try {
                // Determine quality level
                $quality = $this->quality ?? $compressionService->getRecommendedQuality(
                    now()->diffInDays($result->created_at)
                );

                // Compress PDF in-place
                $compressionResult = $compressionService->compressPdfInPlace(
                    $result->pdf_path,
                    $quality
                );

                if ($compressionResult['success']) {
                    $originalSize = $compressionResult['original_size'];
                    $compressedSize = $compressionResult['compressed_size'];
                    $saved = $originalSize - $compressedSize;

                    // Update result record
                    $result->update([
                        'pdf_compressed' => true,
                        'pdf_compressed_at' => now(),
                        'pdf_original_size_bytes' => $originalSize,
                        'pdf_size_bytes' => $compressedSize,
                        'pdf_compression_ratio' => $compressionResult['ratio'],
                        'pdf_compression_method' => $compressionResult['method'],
                    ]);

                    $compressedCount++;
                    $totalSavedBytes += $saved;
                    $totalOriginalBytes += $originalSize;

                    Log::debug('PDF compressed successfully', [
                        'result_id' => $result->id,
                        'original_size' => $originalSize,
                        'compressed_size' => $compressedSize,
                        'ratio' => $compressionResult['ratio'] . '%',
                    ]);
                } else {
                    Log::warning('PDF compression failed or not beneficial', [
                        'result_id' => $result->id,
                        'error' => $compressionResult['error'] ?? 'Unknown error',
                    ]);
                    $errorCount++;
                }

            } catch (\Exception $e) {
                Log::error('Failed to compress PDF', [
                    'result_id' => $result->id,
                    'unique_code' => $result->unique_code,
                    'error' => $e->getMessage(),
                ]);
                $errorCount++;
            }
        }

        $duration = now()->diffInSeconds($startTime);
        $avgRatio = $totalOriginalBytes > 0
            ? round((($totalOriginalBytes - ($totalOriginalBytes - $totalSavedBytes)) / $totalOriginalBytes) * 100)
            : 0;

        Log::info('PDF compression completed', [
            'total_processed' => $totalResults,
            'compressed' => $compressedCount,
            'errors' => $errorCount,
            'total_saved_bytes' => $totalSavedBytes,
            'total_saved_mb' => round($totalSavedBytes / 1024 / 1024, 2),
            'avg_compression_ratio' => $avgRatio . '%',
            'duration_seconds' => $duration,
        ]);

        // Log to database (reuse cleanup logs or create new compression_logs table)
        \DB::table('pdf_cleanup_logs')->insert([
            'tenant_id' => $this->tenantId,
            'days_old' => $this->daysOld,
            'archived_count' => 0, // Not archiving, just compressing
            'deleted_count' => 0,
            'error_count' => $errorCount,
            'freed_bytes' => $totalSavedBytes, // Storage saved
            'archive_enabled' => false,
            'triggered_by' => 'compression_job',
            'notes' => "Compressed {$compressedCount} PDFs, saved " . round($totalSavedBytes / 1024 / 1024, 2) . " MB",
            'executed_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Handle job failure
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('PDF compression job failed completely', [
            'days_old' => $this->daysOld,
            'tenant_id' => $this->tenantId,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);
    }
}
