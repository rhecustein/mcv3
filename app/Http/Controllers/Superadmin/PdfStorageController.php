<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Result;
use App\Models\PdfCleanupLog;
use App\Models\PdfStorageSetting;
use App\Jobs\Maintenance\CleanupOldPdfsJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PdfStorageController extends Controller
{
    /**
     * Dashboard - Storage overview
     */
    public function index()
    {
        // Total storage usage
        $totalPdfs = Result::whereNotNull('pdf_path')->count();
        $totalSize = Result::whereNotNull('pdf_size_bytes')->sum('pdf_size_bytes');
        $archivedCount = Result::where('pdf_archived', true)->count();

        // Storage by tenant
        $storageByTenant = Result::select('tenant_id')
            ->whereNotNull('pdf_path')
            ->selectRaw('COUNT(*) as pdf_count')
            ->selectRaw('SUM(pdf_size_bytes) as total_bytes')
            ->selectRaw('MAX(pdf_generated_at) as latest_pdf')
            ->selectRaw('MIN(pdf_generated_at) as oldest_pdf')
            ->groupBy('tenant_id')
            ->orderByDesc('total_bytes')
            ->get()
            ->map(function ($item) {
                $item->total_size = $this->formatBytes($item->total_bytes ?? 0);
                $item->latest_pdf_days = $item->latest_pdf ? now()->diffInDays($item->latest_pdf) : null;
                $item->oldest_pdf_days = $item->oldest_pdf ? now()->diffInDays($item->oldest_pdf) : null;
                return $item;
            });

        // Recent cleanup logs
        $recentCleanups = PdfCleanupLog::orderByDesc('executed_at')
            ->limit(10)
            ->get()
            ->map(function ($log) {
                $log->freed_size = $this->formatBytes($log->freed_bytes);
                return $log;
            });

        // Global settings
        $globalSettings = PdfStorageSetting::globalDefaults();

        return view('superadmin.pdf-storage.index', compact(
            'totalPdfs',
            'totalSize',
            'archivedCount',
            'storageByTenant',
            'recentCleanups',
            'globalSettings'
        ));
    }

    /**
     * Settings page
     */
    public function settings()
    {
        $globalSettings = PdfStorageSetting::globalDefaults();

        $tenantSettings = PdfStorageSetting::whereNotNull('tenant_id')
            ->get();

        return view('superadmin.pdf-storage.settings', compact(
            'globalSettings',
            'tenantSettings'
        ));
    }

    /**
     * Update global settings
     */
    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'auto_delete_days' => 'required|integer|min:7|max:3650',
            'auto_delete_enabled' => 'boolean',
            'archive_before_delete' => 'boolean',
            'archive_storage' => 'required|in:s3,local,glacier',
            'compression_days' => 'nullable|integer|min:1',
            'compression_enabled' => 'boolean',
            'alert_enabled' => 'boolean',
            'alert_threshold_percent' => 'required|integer|min:1|max:100',
            'alert_email' => 'nullable|email',
        ]);

        $settings = PdfStorageSetting::globalDefaults();
        $settings->update($validated);

        return redirect()
            ->route('superadmin.pdf-storage.settings')
            ->with('success', 'Settings updated successfully');
    }

    /**
     * Trigger manual cleanup
     */
    public function cleanup(Request $request)
    {
        $validated = $request->validate([
            'days_old' => 'required|integer|min:7',
            'tenant_id' => 'nullable|string',
            'archive' => 'nullable|boolean',
        ]);

        // Dispatch cleanup job
        CleanupOldPdfsJob::dispatch(
            $validated['days_old'],
            $validated['archive'] ?? true,
            $validated['tenant_id'] ?? null
        );

        return redirect()
            ->route('superadmin.pdf-storage.index')
            ->with('success', 'Cleanup job queued successfully');
    }

    /**
     * View cleanup logs
     */
    public function logs()
    {
        $logs = PdfCleanupLog::orderByDesc('executed_at')
            ->paginate(50);

        $logs->getCollection()->transform(function ($log) {
            $log->freed_size = $this->formatBytes($log->freed_bytes);
            return $log;
        });

        return view('superadmin.pdf-storage.logs', compact('logs'));
    }

    /**
     * Restore archived PDF
     */
    public function restore(Request $request)
    {
        $validated = $request->validate([
            'result_id' => 'required|exists:results,id',
        ]);

        $result = Result::findOrFail($validated['result_id']);

        if (!$result->pdf_archived || !$result->pdf_archive_path) {
            return back()->with('error', 'PDF not archived or archive path missing');
        }

        try {
            // Restore from archive
            $archiveContent = Storage::disk('s3')->get($result->pdf_archive_path);

            // Save back to local storage
            $localPath = "certificates/{$result->tenant_id}/{$result->unique_code}.pdf";
            Storage::disk('public')->put($localPath, $archiveContent);

            // Update result
            $result->update([
                'pdf_path' => $localPath,
                'pdf_deleted_at' => null,
                'pdf_archived' => false,
            ]);

            return back()->with('success', 'PDF restored successfully');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to restore PDF: ' . $e->getMessage());
        }
    }

    /**
     * Statistics page with charts
     */
    public function statisticsPage()
    {
        // Total statistics
        $totalPdfs = Result::whereNotNull('pdf_path')->whereNull('pdf_deleted_at')->count();
        $totalSize = Result::whereNotNull('pdf_size_bytes')->sum('pdf_size_bytes');

        // Compression stats
        $compressedCount = Result::where('pdf_compressed', true)->count();
        $originalSize = Result::where('pdf_compressed', true)->sum('pdf_original_size_bytes');
        $compressedSize = Result::where('pdf_compressed', true)->sum('pdf_size_bytes');
        $avgRatio = $originalSize > 0 ? round((($originalSize - $compressedSize) / $originalSize) * 100) : 0;

        // Cost calculation (AWS S3 pricing: $0.023/GB/month)
        $monthlyCost = ($totalSize / 1024 / 1024 / 1024) * 0.023;

        $stats = [
            'total' => [
                'pdfs' => $totalPdfs,
                'size_bytes' => $totalSize,
                'size_formatted' => formatBytes($totalSize),
                'archived' => Result::where('pdf_archived', true)->count(),
            ],
            'compression' => [
                'compressed_count' => $compressedCount,
                'original_size' => $originalSize,
                'compressed_size' => $compressedSize,
                'savings' => $originalSize - $compressedSize,
                'avg_ratio' => $avgRatio,
            ],
            'cost' => [
                'monthly' => $monthlyCost,
                'yearly' => $monthlyCost * 12,
                'with_compression' => ($compressedSize / 1024 / 1024 / 1024) * 0.023,
            ],
            'top_tenants' => Result::select('tenant_id')
                ->whereNotNull('pdf_path')
                ->whereNull('pdf_deleted_at')
                ->selectRaw('COUNT(*) as pdf_count')
                ->selectRaw('SUM(pdf_size_bytes) as total_bytes')
                ->selectRaw('SUM(CASE WHEN pdf_compressed = 1 THEN 1 ELSE 0 END) as compressed_count')
                ->selectRaw('SUM(CASE WHEN pdf_compressed = 1 THEN pdf_original_size_bytes - pdf_size_bytes ELSE 0 END) as total_savings')
                ->selectRaw('(SUM(pdf_size_bytes) / 1024 / 1024 / 1024 * 0.023) as monthly_cost')
                ->groupBy('tenant_id')
                ->orderByDesc('total_bytes')
                ->limit(10)
                ->get(),
        ];

        // Chart data
        $monthlyData = Result::whereNotNull('pdf_generated_at')
            ->selectRaw('DATE_FORMAT(pdf_generated_at, "%Y-%m") as month')
            ->selectRaw('COUNT(*) as count')
            ->selectRaw('SUM(pdf_size_bytes) as size_bytes')
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->limit(12)
            ->get();

        $tenantData = Result::select('tenant_id')
            ->whereNotNull('pdf_path')
            ->whereNull('pdf_deleted_at')
            ->selectRaw('SUM(pdf_size_bytes) as total_bytes')
            ->groupBy('tenant_id')
            ->orderByDesc('total_bytes')
            ->limit(8)
            ->get();

        $chartData = [
            'monthly' => [
                'labels' => $monthlyData->pluck('month')->toArray(),
                'count' => $monthlyData->pluck('count')->toArray(),
                'storage' => $monthlyData->pluck('size_bytes')->map(fn($b) => round($b / 1024 / 1024 / 1024, 2))->toArray(),
                'generated' => $monthlyData->pluck('count')->toArray(),
            ],
            'tenants' => [
                'labels' => $tenantData->pluck('tenant_id')->toArray(),
                'sizes' => $tenantData->pluck('total_bytes')->map(fn($b) => round($b / 1024 / 1024, 2))->toArray(),
            ],
            'compression' => [
                'original_size' => round($originalSize / 1024 / 1024 / 1024, 2),
                'compressed_size' => round($compressedSize / 1024 / 1024 / 1024, 2),
                'savings' => round(($originalSize - $compressedSize) / 1024 / 1024 / 1024, 2),
            ],
        ];

        return view('superadmin.pdf-storage.statistics', compact('stats', 'chartData'));
    }

    /**
     * Storage statistics API (JSON)
     */
    public function statistics()
    {
        $stats = [
            'total' => [
                'pdfs' => Result::whereNotNull('pdf_path')->count(),
                'size_bytes' => Result::sum('pdf_size_bytes'),
                'size_formatted' => formatBytes(Result::sum('pdf_size_bytes')),
                'archived' => Result::where('pdf_archived', true)->count(),
            ],
            'by_month' => Result::whereNotNull('pdf_generated_at')
                ->selectRaw('DATE_FORMAT(pdf_generated_at, "%Y-%m") as month')
                ->selectRaw('COUNT(*) as count')
                ->selectRaw('SUM(pdf_size_bytes) as size_bytes')
                ->groupBy('month')
                ->orderByDesc('month')
                ->limit(12)
                ->get(),
            'by_type' => Result::whereNotNull('pdf_path')
                ->selectRaw('type')
                ->selectRaw('COUNT(*) as count')
                ->selectRaw('SUM(pdf_size_bytes) as size_bytes')
                ->groupBy('type')
                ->get(),
        ];

        return response()->json($stats);
    }

    /**
     * Archives management page
     */
    public function archives(Request $request)
    {
        // Base query for archived PDFs
        $query = Result::where('pdf_archived', true)
            ->whereNotNull('pdf_archive_path');

        // Apply filters
        if ($request->filled('unique_code')) {
            $query->where('unique_code', 'like', '%' . $request->unique_code . '%');
        }

        if ($request->filled('tenant_id')) {
            $query->where('tenant_id', $request->tenant_id);
        }

        if ($request->filled('archived_from')) {
            $query->where('pdf_deleted_at', '>=', $request->archived_from);
        }

        if ($request->filled('archived_to')) {
            $query->where('pdf_deleted_at', '<=', $request->archived_to . ' 23:59:59');
        }

        // Paginate results
        $archivedPdfs = $query->orderByDesc('pdf_deleted_at')->paginate(50);

        // Archive statistics
        $totalArchived = Result::where('pdf_archived', true)->count();
        $totalSize = Result::where('pdf_archived', true)->sum('pdf_size_bytes');
        $restoreable = Result::where('pdf_archived', true)
            ->whereNotNull('pdf_archive_path')
            ->count();

        // Glacier pricing: $0.004/GB/month
        $monthlyCost = ($totalSize / 1024 / 1024 / 1024) * 0.004;

        $archiveStats = [
            'total_count' => $totalArchived,
            'total_size' => $totalSize,
            'monthly_cost' => $monthlyCost,
            'restoreable' => $restoreable,
        ];

        return view('superadmin.pdf-storage.archives', compact('archivedPdfs', 'archiveStats'));
    }

    /**
     * Bulk restore archived PDFs
     */
    public function bulkRestore(Request $request)
    {
        $validated = $request->validate([
            'result_ids' => 'required|array',
            'result_ids.*' => 'required|exists:results,id',
        ]);

        $successCount = 0;
        $errorCount = 0;
        $errors = [];

        foreach ($validated['result_ids'] as $resultId) {
            $result = Result::find($resultId);

            if (!$result || !$result->pdf_archived || !$result->pdf_archive_path) {
                $errorCount++;
                $errors[] = "Result ID {$resultId}: Not archived or no archive path";
                continue;
            }

            try {
                // Restore from archive
                $archiveContent = Storage::disk('s3')->get($result->pdf_archive_path);

                // Save back to local storage
                $localPath = "certificates/{$result->tenant_id}/{$result->unique_code}.pdf";
                Storage::disk('public')->put($localPath, $archiveContent);

                // Update result
                $result->update([
                    'pdf_path' => $localPath,
                    'pdf_deleted_at' => null,
                    'pdf_archived' => false,
                ]);

                $successCount++;

            } catch (\Exception $e) {
                $errorCount++;
                $errors[] = "Result ID {$resultId}: " . $e->getMessage();
            }
        }

        $message = "Restored {$successCount} PDFs successfully.";
        if ($errorCount > 0) {
            $message .= " {$errorCount} failed.";
        }

        if ($errorCount > 0 && $successCount === 0) {
            return back()->with('error', $message . ' Errors: ' . implode(', ', array_slice($errors, 0, 3)));
        }

        return back()->with('success', $message);
    }

    /**
     * Helper: Format bytes to human-readable
     */
    private function formatBytes(?int $bytes): string
    {
        if ($bytes === null || $bytes === 0) return '0 B';

        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes > 1024 && $i < 4; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }
}
