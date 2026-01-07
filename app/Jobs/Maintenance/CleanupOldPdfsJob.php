<?php

namespace App\Jobs\Maintenance;

use App\Models\Result;
use App\Models\PdfCleanupLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class CleanupOldPdfsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 1; // Don't retry cleanup jobs
    public $timeout = 3600; // 1 hour for large cleanups

    /**
     * Create a new job instance.
     *
     * @param int $daysOld Delete PDFs older than this many days
     * @param bool $archive Archive before delete
     * @param string|null $tenantId Specific tenant or null for all
     */
    public function __construct(
        public int $daysOld = 30,
        public bool $archive = true,
        public ?string $tenantId = null
    ) {
        $this->onQueue('maintenance');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $startTime = now();
        $cutoffDate = Carbon::now()->subDays($this->daysOld);

        Log::info('Starting PDF cleanup job', [
            'days_old' => $this->daysOld,
            'cutoff_date' => $cutoffDate->toDateTimeString(),
            'tenant_id' => $this->tenantId ?? 'all',
            'archive' => $this->archive,
        ]);

        // Query old PDFs
        $query = Result::where('created_at', '<', $cutoffDate)
            ->whereNotNull('pdf_path')
            ->whereNull('pdf_deleted_at'); // Don't re-process already deleted PDFs

        if ($this->tenantId) {
            $query->where('tenant_id', $this->tenantId);
        }

        $results = $query->get();
        $totalResults = $results->count();

        Log::info("Found {$totalResults} PDFs to cleanup");

        $deletedCount = 0;
        $archivedCount = 0;
        $errorCount = 0;
        $totalFreedBytes = 0;

        foreach ($results as $result) {
            try {
                $fileSize = 0;

                // Get file size before deletion
                if (Storage::disk('public')->exists($result->pdf_path)) {
                    $fileSize = Storage::disk('public')->size($result->pdf_path);
                }

                // Archive if enabled
                if ($this->archive && $fileSize > 0) {
                    $archived = $this->archivePdf($result);
                    if ($archived) {
                        $archivedCount++;
                    }
                }

                // Delete from local storage
                if (Storage::disk('public')->exists($result->pdf_path)) {
                    Storage::disk('public')->delete($result->pdf_path);
                    $deletedCount++;
                    $totalFreedBytes += $fileSize;
                }

                // Update result record
                $result->update([
                    'pdf_path' => null,
                    'pdf_deleted_at' => now(),
                    'pdf_archived' => $this->archive,
                ]);

            } catch (\Exception $e) {
                Log::error('Failed to cleanup PDF', [
                    'result_id' => $result->id,
                    'unique_code' => $result->unique_code,
                    'error' => $e->getMessage(),
                ]);
                $errorCount++;
            }
        }

        $duration = now()->diffInSeconds($startTime);

        Log::info('PDF cleanup completed', [
            'total_processed' => $totalResults,
            'archived' => $archivedCount,
            'deleted' => $deletedCount,
            'errors' => $errorCount,
            'freed_bytes' => $totalFreedBytes,
            'freed_mb' => round($totalFreedBytes / 1024 / 1024, 2),
            'duration_seconds' => $duration,
        ]);

        // Create cleanup log entry
        PdfCleanupLog::create([
            'tenant_id' => $this->tenantId,
            'days_old' => $this->daysOld,
            'archived_count' => $archivedCount,
            'deleted_count' => $deletedCount,
            'error_count' => $errorCount,
            'freed_bytes' => $totalFreedBytes,
            'archive_enabled' => $this->archive,
            'triggered_by' => 'schedule', // Can be 'schedule', 'manual', or user_id
            'notes' => "Processed {$totalResults} PDFs in {$duration} seconds",
            'executed_at' => now(),
        ]);
    }

    /**
     * Archive PDF to S3/Glacier
     */
    private function archivePdf(Result $result): bool
    {
        try {
            // Archive path
            $archivePath = "archives/{$result->tenant_id}/certificates/{$result->unique_code}.pdf";

            // Get PDF content
            $pdfContent = Storage::disk('public')->get($result->pdf_path);

            // Upload to S3 with Glacier storage class (cheap long-term storage)
            Storage::disk('s3')->put($archivePath, $pdfContent, [
                'StorageClass' => 'GLACIER', // Or 'STANDARD_IA' for faster retrieval
                'visibility' => 'private',
            ]);

            // Update result with archive path
            $result->update([
                'pdf_archive_path' => $archivePath,
            ]);

            Log::debug('PDF archived successfully', [
                'result_id' => $result->id,
                'archive_path' => $archivePath,
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Failed to archive PDF', [
                'result_id' => $result->id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Handle job failure
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('PDF cleanup job failed completely', [
            'days_old' => $this->daysOld,
            'tenant_id' => $this->tenantId,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);

        // Create failed log entry
        PdfCleanupLog::create([
            'tenant_id' => $this->tenantId,
            'days_old' => $this->daysOld,
            'archived_count' => 0,
            'deleted_count' => 0,
            'error_count' => 1,
            'freed_bytes' => 0,
            'archive_enabled' => $this->archive,
            'triggered_by' => 'schedule',
            'notes' => 'Job failed: ' . $exception->getMessage(),
            'executed_at' => now(),
        ]);
    }
}
