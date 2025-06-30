<?php

namespace App\Http\Controllers;
use App\Models\DocumentQueue;
use App\Jobs\GenerateSuratSehatPDF;
use Illuminate\Http\Request;

class DocumentQueueController extends Controller
{
    public function index()
    {
        return view('outlets.queue.index');
    }

    public function fetchData()
    {
        $user = auth()->user();
        

        // Role outlet → hanya lihat data outlet miliknya
        if ($user->role === 'outlet') {
            $data = DocumentQueue::with('result')
                ->where('outlet_id', $user->outlet_id)
                ->latest()
                ->limit(50)
                ->get();

            return response()->json($data);
        }

        // Role superadmin dan admin → lihat semua data
        if (in_array($user->role, ['superadmin', 'admin'])) {
            $data = DocumentQueue::with('result')
                ->latest()
                ->limit(50)
                ->get();

            return response()->json($data);
        }

        // Role tidak diizinkan
        return response()->json([
            'message' => 'Akses ditolak. Anda tidak memiliki izin untuk melihat data ini.'
        ], 403);
    }
    public function retry($id)
    {
        $queue = DocumentQueue::findOrFail($id);
        if ($queue->status !== 'failed') return back();

        $queue->update([
            'status' => 'pending',
            'error_log' => null,
            'processed_at' => null,
            'completed_at' => null,
            'retry_count' => $queue->retry_count + 1,
        ]);

        GenerateSuratSehatPDF::dispatch($queue->result_id, $queue->id);

        return back()->with('success', 'Antrian sedang diproses ulang.');
    }
}
