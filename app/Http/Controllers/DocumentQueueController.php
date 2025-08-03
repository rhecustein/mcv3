<?php

namespace App\Http\Controllers;

use App\Models\DocumentQueue;
use App\Models\Result;
use App\Models\Outlet;
use App\Jobs\GenerateSuratSehatPDF;
use App\Jobs\GenerateSuratSakitPDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DocumentQueueController extends Controller
{
    public function index()
    {
        return view('outlets.queue.index');
    }

    public function fetchData(Request $request)
    {
        try {
            $user = Auth::user();
            $outlet = Outlet::where('user_id', $user->id)->first();

            if (!$outlet) {
                return response()->json([
                    'message' => 'Outlet tidak ditemukan untuk user ini.'
                ], 404);
            }

            // Build query berdasarkan outlet user
            $query = DocumentQueue::with(['result.patient', 'result.outlet'])
                ->whereHas('result', function($q) use ($outlet) {
                    $q->where('outlet_id', $outlet->id);
                });

            // Filter berdasarkan status jika ada
            if ($request->has('status') && !empty($request->status)) {
                $query->where('status', $request->status);
            }

            // Ambil data dengan ordering dan limit
            $queues = $query->orderBy('created_at', 'desc')
                           ->limit(100)
                           ->get();

            // Transform data untuk response yang lebih clean
            $data = $queues->map(function($queue) {
                return [
                    'id' => $queue->id,
                    'result_id' => $queue->result_id,
                    'status' => $queue->status,
                    'no_letters' => $queue->no_letters,
                    'patient_name' => $queue->patient_name,
                    'type_surat' => $queue->type_surat,
                    'outlet_name' => $queue->outlet_name,
                    'start_date' => $queue->start_date,
                    'expired_date' => $queue->expired_date,
                    'created_at' => $queue->created_at,
                    'processed_at' => $queue->processed_at,
                    'completed_at' => $queue->completed_at,
                    'retry_count' => $queue->retry_count ?? 0,
                    'error_log' => $queue->error_log,
                    'generated_file' => $queue->generated_file,
                    'triggered_by' => $queue->triggered_by ?? 'manual',
                ];
            });

            Log::info('Queue data fetched', [
                'user_id' => $user->id,
                'outlet_id' => $outlet->id,
                'queue_count' => $data->count(),
                'status_filter' => $request->status
            ]);

            return response()->json($data);

        } catch (\Throwable $e) {
            Log::error('Failed to fetch queue data', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id(),
                'request' => $request->all()
            ]);

            return response()->json([
                'message' => 'Gagal mengambil data queue.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    public function retry($id)
    {
        try {
            $user = Auth::user();
            $outlet = Outlet::where('user_id', $user->id)->first();

            if (!$outlet) {
                return back()->with('error', 'Outlet tidak ditemukan.');
            }

            $queue = DocumentQueue::with('result')
                ->whereHas('result', function($q) use ($outlet) {
                    $q->where('outlet_id', $outlet->id);
                })
                ->findOrFail($id);

            // Hanya queue yang failed yang bisa di-retry
            if ($queue->status !== 'failed') {
                return back()->with('error', 'Hanya queue dengan status failed yang bisa di-retry.');
            }

            // Update status queue
            $queue->update([
                'status' => 'pending',
                'error_log' => null,
                'processed_at' => null,
                'completed_at' => null,
                'retry_count' => ($queue->retry_count ?? 0) + 1,
            ]);

            // Dispatch job berdasarkan tipe surat
            if ($queue->type_surat === 'mc') {
                GenerateSuratSakitPDF::dispatch($queue->result_id, $queue->id);
            } else {
                GenerateSuratSehatPDF::dispatch($queue->result_id, $queue->id);
            }

            Log::info('Queue retry initiated', [
                'queue_id' => $queue->id,
                'result_id' => $queue->result_id,
                'type_surat' => $queue->type_surat,
                'retry_count' => $queue->retry_count,
                'user_id' => $user->id
            ]);

            return back()->with('success', 'Queue berhasil di-retry dan sedang diproses ulang.');

        } catch (\Throwable $e) {
            Log::error('Failed to retry queue', [
                'queue_id' => $id,
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);

            return back()->with('error', 'Gagal melakukan retry queue: ' . $e->getMessage());
        }
    }

    public function delete($id)
    {
        try {
            $user = Auth::user();
            $outlet = Outlet::where('user_id', $user->id)->first();

            if (!$outlet) {
                return response()->json(['message' => 'Outlet tidak ditemukan.'], 404);
            }

            $queue = DocumentQueue::with('result')
                ->whereHas('result', function($q) use ($outlet) {
                    $q->where('outlet_id', $outlet->id);
                })
                ->findOrFail($id);

            // Hanya queue pending yang bisa dihapus
            if ($queue->status !== 'pending') {
                return response()->json([
                    'message' => 'Hanya queue dengan status pending yang bisa dihapus.'
                ], 400);
            }

            $queue->delete();

            Log::info('Queue deleted', [
                'queue_id' => $id,
                'result_id' => $queue->result_id,
                'user_id' => $user->id
            ]);

            return response()->json(['message' => 'Queue berhasil dihapus.']);

        } catch (\Throwable $e) {
            Log::error('Failed to delete queue', [
                'queue_id' => $id,
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'message' => 'Gagal menghapus queue: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get queue statistics for dashboard widget
     */
    public function getStats()
    {
        try {
            $user = Auth::user();
            $outlet = Outlet::where('user_id', $user->id)->first();

            if (!$outlet) {
                return response()->json(['error' => 'Outlet tidak ditemukan'], 404);
            }

            $stats = DocumentQueue::whereHas('result', function($q) use ($outlet) {
                    $q->where('outlet_id', $outlet->id);
                })
                ->selectRaw('
                    COUNT(*) as total,
                    SUM(CASE WHEN status = "pending" THEN 1 ELSE 0 END) as pending,
                    SUM(CASE WHEN status = "processing" THEN 1 ELSE 0 END) as processing,
                    SUM(CASE WHEN status = "success" THEN 1 ELSE 0 END) as success,
                    SUM(CASE WHEN status = "failed" THEN 1 ELSE 0 END) as failed
                ')
                ->first();

            $response = [
                'total' => $stats->total ?? 0,
                'pending' => $stats->pending ?? 0,
                'processing' => $stats->processing ?? 0,
                'success' => $stats->success ?? 0,
                'failed' => $stats->failed ?? 0,
                'success_rate' => $stats->total > 0 ? 
                    round(($stats->success / $stats->total) * 100, 2) : 0,
            ];

            return response()->json($response);

        } catch (\Throwable $e) {
            Log::error('Failed to get queue stats', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'error' => 'Gagal mengambil statistik queue'
            ], 500);
        }
    }

    /**
     * Bulk retry failed queues
     */
    public function bulkRetry(Request $request)
    {
        try {
            $user = Auth::user();
            $outlet = Outlet::where('user_id', $user->id)->first();

            if (!$outlet) {
                return response()->json(['message' => 'Outlet tidak ditemukan.'], 404);
            }

            $failedQueues = DocumentQueue::with('result')
                ->whereHas('result', function($q) use ($outlet) {
                    $q->where('outlet_id', $outlet->id);
                })
                ->where('status', 'failed')
                ->get();

            if ($failedQueues->isEmpty()) {
                return response()->json(['message' => 'Tidak ada queue failed untuk di-retry.']);
            }

            $retryCount = 0;
            foreach ($failedQueues as $queue) {
                // Update status
                $queue->update([
                    'status' => 'pending',
                    'error_log' => null,
                    'processed_at' => null,
                    'completed_at' => null,
                    'retry_count' => ($queue->retry_count ?? 0) + 1,
                ]);

                // Dispatch appropriate job
                if ($queue->type_surat === 'mc') {
                    GenerateSuratSakitPDF::dispatch($queue->result_id, $queue->id);
                } else {
                    GenerateSuratSehatPDF::dispatch($queue->result_id, $queue->id);
                }

                $retryCount++;
            }

            Log::info('Bulk retry completed', [
                'retry_count' => $retryCount,
                'user_id' => $user->id,
                'outlet_id' => $outlet->id
            ]);

            return response()->json([
                'message' => "Berhasil retry {$retryCount} queue."
            ]);

        } catch (\Throwable $e) {
            Log::error('Failed to bulk retry queues', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'message' => 'Gagal melakukan bulk retry: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Clean completed queues older than specified days
     */
    public function cleanCompleted(Request $request)
    {
        try {
            $user = Auth::user();
            $outlet = Outlet::where('user_id', $user->id)->first();

            if (!$outlet) {
                return response()->json(['message' => 'Outlet tidak ditemukan.'], 404);
            }

            $days = $request->get('days', 7); // Default 7 days
            $cutoffDate = now()->subDays($days);

            $deletedCount = DocumentQueue::whereHas('result', function($q) use ($outlet) {
                    $q->where('outlet_id', $outlet->id);
                })
                ->where('status', 'success')
                ->where('completed_at', '<', $cutoffDate)
                ->delete();

            Log::info('Completed queues cleaned', [
                'deleted_count' => $deletedCount,
                'days' => $days,
                'user_id' => $user->id,
                'outlet_id' => $outlet->id
            ]);

            return response()->json([
                'message' => "Berhasil menghapus {$deletedCount} queue yang sudah selesai."
            ]);

        } catch (\Throwable $e) {
            Log::error('Failed to clean completed queues', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'message' => 'Gagal membersihkan queue: ' . $e->getMessage()
            ], 500);
        }
    }
}