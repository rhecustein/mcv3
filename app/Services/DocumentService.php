<?php

namespace App\Services;

use App\Models\Result;
use App\Models\DocumentQueue;
use Carbon\Carbon;

/**
 * Document Service
 * Handles PDF generation queueing and document management
 */
class DocumentService
{
    /**
     * Queue PDF generation for result
     *
     * @param Result $result
     * @param string $type
     * @return DocumentQueue
     */
    public function queuePDFGeneration(Result $result, string $type): DocumentQueue
    {
        // Generate letter number if not exists
        if (empty($result->no_letters)) {
            $result->update([
                'no_letters' => $this->generateLetterNumber($result->outlet_id, $type)
            ]);
        }

        // Create document queue
        $queue = DocumentQueue::create([
            'result_id' => $result->id,
            'outlet_id' => $result->outlet_id,
            'no_letters' => $result->no_letters,
            'patient_name' => $result->patient->full_name,
            'type_surat' => $type,
            'outlet_name' => $result->outlet->name ?? null,
            'start_date' => $type === 'mc' ? $result->start_date : $result->date,
            'expired_date' => $type === 'mc' ? $result->end_date : null,
            'status' => 'pending',
            'triggered_by' => 'auto',
        ]);

        \Log::info('PDF generation queued', [
            'queue_id' => $queue->id,
            'result_id' => $result->id,
            'type' => $type,
        ]);

        return $queue;
    }

    /**
     * Generate letter number for result
     *
     * @param int $outletId
     * @param string $type
     * @return string
     */
    public function generateLetterNumber(int $outletId, string $type): string
    {
        $prefix = $type === 'skb' ? 'SKB' : 'MC';
        $year = now()->year;
        $month = now()->format('m');

        // Count this month's letters of this type for this outlet
        $count = Result::where('outlet_id', $outletId)
            ->where('type', $type)
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->whereNotNull('no_letters')
            ->count();

        $number = str_pad($count + 1, 4, '0', STR_PAD_LEFT);

        // Format: SKB/0001/OUTLET/XII/2026
        $monthRoman = $this->toRoman((int) $month);
        return "{$prefix}/{$number}/OUT{$outletId}/{$monthRoman}/{$year}";
    }

    /**
     * Convert number to Roman numerals
     *
     * @param int $number
     * @return string
     */
    protected function toRoman(int $number): string
    {
        $map = [
            12 => 'XII', 11 => 'XI', 10 => 'X',
            9 => 'IX', 8 => 'VIII', 7 => 'VII',
            6 => 'VI', 5 => 'V', 4 => 'IV',
            3 => 'III', 2 => 'II', 1 => 'I'
        ];

        return $map[$number] ?? (string) $number;
    }

    /**
     * Regenerate PDF for existing result
     *
     * @param Result $result
     * @return DocumentQueue
     */
    public function regeneratePDF(Result $result): DocumentQueue
    {
        \Log::info('Regenerating PDF', [
            'result_id' => $result->id,
            'unique_code' => $result->unique_code,
        ]);

        return $this->queuePDFGeneration($result, $result->type);
    }

    /**
     * Get queue status for result
     *
     * @param int $resultId
     * @return DocumentQueue|null
     */
    public function getQueueStatus(int $resultId): ?DocumentQueue
    {
        return DocumentQueue::where('result_id', $resultId)
            ->latest()
            ->first();
    }

    /**
     * Mark queue as completed
     *
     * @param int $queueId
     * @param string $filePath
     * @return DocumentQueue
     */
    public function markQueueCompleted(int $queueId, string $filePath): DocumentQueue
    {
        $queue = DocumentQueue::findOrFail($queueId);

        $queue->update([
            'status' => 'success',
            'generated_file' => $filePath,
            'completed_at' => now(),
        ]);

        // Update result with document name
        if ($queue->result) {
            $queue->result->update([
                'document_name' => basename($filePath),
            ]);
        }

        \Log::info('Queue marked as completed', [
            'queue_id' => $queueId,
            'file_path' => $filePath,
        ]);

        return $queue;
    }

    /**
     * Mark queue as failed
     *
     * @param int $queueId
     * @param string $errorMessage
     * @return DocumentQueue
     */
    public function markQueueFailed(int $queueId, string $errorMessage): DocumentQueue
    {
        $queue = DocumentQueue::findOrFail($queueId);

        $queue->update([
            'status' => 'failed',
            'error_log' => $errorMessage,
            'retry_count' => $queue->retry_count + 1,
        ]);

        \Log::error('Queue marked as failed', [
            'queue_id' => $queueId,
            'error' => $errorMessage,
            'retry_count' => $queue->retry_count,
        ]);

        return $queue;
    }
}
