<?php

namespace App\Http\Controllers;

use App\Models\Result;
use App\Models\ResultTrashView;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class LegacyResultController extends Controller
{
    /**
     * Display a listing of legacy results.
     */
    public function index(Request $request)
    {
        $query = Result::with(['patient', 'doctor.user', 'outlet'])
            ->whereNotNull('legacy_migrated_at') // Asumsi ada kolom untuk legacy
            ->orWhere('created_at', '<', Carbon::now()->subMonths(6)); // atau kriteria legacy lainnya

        // Filter berdasarkan pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('patient', function ($patient) use ($search) {
                    $patient->where('full_name', 'like', "%{$search}%")
                           ->orWhere('nik', 'like', "%{$search}%");
                })
                ->orWhere('unique_code', 'like', "%{$search}%")
                ->orWhereHas('doctor.user', function ($doctor) use ($search) {
                    $doctor->where('name', 'like', "%{$search}%");
                });
            });
        }

        // Filter berdasarkan type
        if ($request->filled('type') && in_array($request->type, ['mc', 'skb'])) {
            $query->where('type', $request->type);
        }

        // Filter berdasarkan outlet
        if ($request->filled('outlet_id')) {
            $query->where('outlet_id', $request->outlet_id);
        }

        // Filter berdasarkan tanggal
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Statistik
        $totalLegacyResults = (clone $query)->count();
        $totalMC = (clone $query)->where('type', 'mc')->count();
        $totalSKB = (clone $query)->where('type', 'skb')->count();
        $oldestResult = (clone $query)->oldest('created_at')->first();

        $results = $query->latest('created_at')->paginate(15);

        return view('legacy-results.index', compact(
            'results',
            'totalLegacyResults',
            'totalMC',
            'totalSKB',
            'oldestResult'
        ));
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $result = Result::with([
            'patient',
            'doctor.user',
            'outlet',
            'diagnosis.icd'
        ])->findOrFail($id);

        return view('legacy-results.show', compact('result'));
    }

    /**
     * Show legacy results trash/deleted items
     */
    public function trash(Request $request)
    {
        $query = ResultTrashView::with(['user', 'outlet']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($user) use ($search) {
                      $user->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('deleted_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('deleted_at', '<=', $request->date_to);
        }

        $trashedResults = $query->orderByDesc('deleted_at')->paginate(15);
        $totalTrashed = ResultTrashView::count();

        return view('legacy-results.trash', compact(
            'trashedResults',
            'totalTrashed'
        ));
    }

    /**
     * Restore a deleted result from trash
     */
    public function restore($id)
    {
        $trashed = ResultTrashView::findOrFail($id);
        $result = Result::withTrashed()->find($trashed->id);

        if ($result && $result->trashed()) {
            DB::transaction(function () use ($result, $trashed) {
                $result->restore();
                $trashed->delete(); // Remove from trash view
            });

            return redirect()->back()->with('success', '✅ Legacy result berhasil direstore.');
        }

        return redirect()->back()->with('error', '❌ Data tidak ditemukan atau sudah aktif.');
    }

    /**
     * Permanently delete a result from trash
     */
    public function forceDelete($id)
    {
        $trashed = ResultTrashView::findOrFail($id);
        $result = Result::withTrashed()->find($trashed->id);

        if ($result && $result->trashed()) {
            DB::transaction(function () use ($result, $trashed) {
                // Delete associated files if exist
                $this->deleteAssociatedFiles($result);
                
                $result->forceDelete();
                $trashed->delete();
            });

            return redirect()->back()->with('success', '✅ Legacy result berhasil dihapus permanen.');
        }

        return redirect()->back()->with('error', '❌ Data tidak ditemukan.');
    }

    /**
     * Bulk operations for legacy results
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:delete,archive,restore',
            'selected_ids' => 'required|array|min:1',
            'selected_ids.*' => 'integer|exists:results,id'
        ]);

        $ids = $request->selected_ids;
        $action = $request->action;

        DB::transaction(function () use ($ids, $action) {
            switch ($action) {
                case 'delete':
                    Result::whereIn('id', $ids)->delete();
                    break;
                    
                case 'archive':
                    Result::whereIn('id', $ids)->update([
                        'archived_at' => now(),
                        'archived_by' => Auth::id()
                    ]);
                    break;
                    
                case 'restore':
                    Result::withTrashed()->whereIn('id', $ids)->restore();
                    // Remove from trash view
                    ResultTrashView::whereIn('id', $ids)->delete();
                    break;
            }
        });

        $message = match($action) {
            'delete' => '✅ Results berhasil dihapus.',
            'archive' => '✅ Results berhasil diarsipkan.',
            'restore' => '✅ Results berhasil direstore.',
        };

        return redirect()->back()->with('success', $message);
    }

    /**
     * Export legacy results to CSV/Excel
     */
    public function export(Request $request)
    {
        $query = Result::with(['patient', 'doctor.user', 'outlet'])
            ->whereNotNull('legacy_migrated_at');

        // Apply same filters as index
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $results = $query->get();

        $filename = 'legacy_results_' . now()->format('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($results) {
            $file = fopen('php://output', 'w');
            
            // Headers
            fputcsv($file, [
                'ID',
                'Unique Code',
                'Type',
                'Patient Name',
                'Patient NIK',
                'Doctor Name',
                'Outlet Name',
                'Created At',
                'Status'
            ]);

            // Data
            foreach ($results as $result) {
                fputcsv($file, [
                    $result->id,
                    $result->unique_code,
                    strtoupper($result->type),
                    $result->patient->full_name ?? 'N/A',
                    $result->patient->nik ?? 'N/A',
                    $result->doctor->user->name ?? 'N/A',
                    $result->outlet->name ?? 'N/A',
                    $result->created_at->format('Y-m-d H:i:s'),
                    $result->deleted_at ? 'Deleted' : 'Active'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get statistics for legacy results
     */
    public function statistics()
    {
        $stats = [
            'total_legacy' => Result::whereNotNull('legacy_migrated_at')->count(),
            'total_mc' => Result::whereNotNull('legacy_migrated_at')->where('type', 'mc')->count(),
            'total_skb' => Result::whereNotNull('legacy_migrated_at')->where('type', 'skb')->count(),
            'total_trashed' => ResultTrashView::count(),
            'oldest_result' => Result::whereNotNull('legacy_migrated_at')->oldest('created_at')->first(),
            'newest_result' => Result::whereNotNull('legacy_migrated_at')->latest('created_at')->first(),
            'by_month' => Result::whereNotNull('legacy_migrated_at')
                ->selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, COUNT(*) as total')
                ->groupBy('year', 'month')
                ->orderBy('year', 'desc')
                ->orderBy('month', 'desc')
                ->limit(12)
                ->get(),
            'by_outlet' => Result::whereNotNull('legacy_migrated_at')
                ->join('outlets', 'results.outlet_id', '=', 'outlets.id')
                ->selectRaw('outlets.name as outlet_name, COUNT(results.id) as total')
                ->groupBy('outlets.name')
                ->orderByDesc('total')
                ->limit(10)
                ->get()
        ];

        return response()->json($stats);
    }

    /**
     * Delete associated files when permanently deleting a result
     */
    private function deleteAssociatedFiles($result)
    {
        // Implementation depends on your file storage structure
        // This is based on the download method in your existing controller
        $year = Carbon::parse($result->type === 'mc' ? $result->start_date : $result->date)->format('Y');
        $typeLabel = $result->type === 'mc' ? 'Surat_Sakit' : 'Surat_Sehat';
        $patientName = \Illuminate\Support\Str::slug($result->patient->full_name ?? 'pasien', '_');
        $date = Carbon::parse($result->type === 'mc' ? $result->start_date : $result->date)->format('Y-m-d');
        $code = strtoupper($result->unique_code ?? \Illuminate\Support\Str::random(6));
        $filename = "{$typeLabel}_{$patientName}_{$date}_{$code}.pdf";
        $path = "pdfs/{$result->type}/{$year}/{$filename}";

        if (\Illuminate\Support\Facades\Storage::disk('public')->exists($path)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($path);
        }
    }
}