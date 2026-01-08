<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Result;
use App\Models\PdfStorageSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PdfStorageController extends Controller
{
    /**
     * Get current tenant ID from authenticated user
     */
    private function getTenantId(): string
    {
        return Auth::user()->tenant_id;
    }

    /**
     * Dashboard - Storage overview for tenant
     */
    public function index()
    {
        $tenantId = $this->getTenantId();

        // Storage statistics for this tenant
        $totalPdfs = Result::where('tenant_id', $tenantId)
            ->whereNotNull('pdf_path')
            ->whereNull('pdf_deleted_at')
            ->count();

        $totalSize = Result::where('tenant_id', $tenantId)
            ->whereNotNull('pdf_size_bytes')
            ->sum('pdf_size_bytes');

        $compressedCount = Result::where('tenant_id', $tenantId)
            ->where('pdf_compressed', true)
            ->count();

        $archivedCount = Result::where('tenant_id', $tenantId)
            ->where('pdf_archived', true)
            ->count();

        // Recent PDFs (last 10)
        $recentPdfs = Result::where('tenant_id', $tenantId)
            ->whereNotNull('pdf_path')
            ->whereNull('pdf_deleted_at')
            ->orderByDesc('pdf_generated_at')
            ->limit(10)
            ->get();

        // Storage settings for this tenant
        $settings = PdfStorageSetting::where('tenant_id', $tenantId)->first()
            ?? PdfStorageSetting::globalDefaults();

        // Calculate quota usage (if quota is set)
        $quotaUsed = 0;
        $quotaLimit = $settings->storage_quota_gb ?? 0;
        if ($quotaLimit > 0) {
            $quotaUsed = round(($totalSize / 1024 / 1024 / 1024 / $quotaLimit) * 100, 1);
        }

        $stats = [
            'total_pdfs' => $totalPdfs,
            'total_size' => $totalSize,
            'compressed_count' => $compressedCount,
            'archived_count' => $archivedCount,
            'quota_used' => $quotaUsed,
            'quota_limit' => $quotaLimit,
        ];

        return view('admin.pdf-storage.dashboard', compact('stats', 'recentPdfs', 'settings'));
    }

    /**
     * Browse PDFs - List and search
     */
    public function browse(Request $request)
    {
        $tenantId = $this->getTenantId();

        // Base query
        $query = Result::where('tenant_id', $tenantId)
            ->whereNotNull('pdf_path')
            ->whereNull('pdf_deleted_at');

        // Search filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('unique_code', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%");
            });
        }

        if ($request->filled('date_from')) {
            $query->where('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('created_at', '<=', $request->date_to . ' 23:59:59');
        }

        if ($request->filled('compressed')) {
            $query->where('pdf_compressed', $request->compressed === '1');
        }

        // Paginate
        $pdfs = $query->orderByDesc('pdf_generated_at')->paginate(20);

        // Statistics
        $totalCount = Result::where('tenant_id', $tenantId)
            ->whereNotNull('pdf_path')
            ->whereNull('pdf_deleted_at')
            ->count();

        $totalSize = Result::where('tenant_id', $tenantId)
            ->whereNotNull('pdf_size_bytes')
            ->sum('pdf_size_bytes');

        $browsStats = [
            'total_count' => $totalCount,
            'total_size' => $totalSize,
            'showing_count' => $pdfs->total(),
        ];

        return view('admin.pdf-storage.browse', compact('pdfs', 'browsStats'));
    }

    /**
     * Download PDF
     */
    public function download($id)
    {
        $tenantId = $this->getTenantId();

        $result = Result::where('tenant_id', $tenantId)
            ->where('id', $id)
            ->whereNotNull('pdf_path')
            ->firstOrFail();

        if (!Storage::disk('public')->exists($result->pdf_path)) {
            return back()->with('error', 'PDF file not found');
        }

        $filename = "{$result->unique_code}.pdf";

        return Storage::disk('public')->download($result->pdf_path, $filename);
    }

    /**
     * View settings (read-only for tenant)
     */
    public function settings()
    {
        $tenantId = $this->getTenantId();

        // Get tenant-specific settings or global defaults
        $tenantSettings = PdfStorageSetting::where('tenant_id', $tenantId)->first();
        $globalSettings = PdfStorageSetting::globalDefaults();

        // Use tenant settings if exist, otherwise global
        $settings = $tenantSettings ?? $globalSettings;
        $usingGlobal = !$tenantSettings;

        return view('admin.pdf-storage.settings', compact('settings', 'usingGlobal'));
    }

    /**
     * Storage info API for mobile
     */
    public function storageInfo()
    {
        $tenantId = $this->getTenantId();

        $totalPdfs = Result::where('tenant_id', $tenantId)
            ->whereNotNull('pdf_path')
            ->whereNull('pdf_deleted_at')
            ->count();

        $totalSize = Result::where('tenant_id', $tenantId)
            ->whereNotNull('pdf_size_bytes')
            ->sum('pdf_size_bytes');

        $settings = PdfStorageSetting::where('tenant_id', $tenantId)->first()
            ?? PdfStorageSetting::globalDefaults();

        return response()->json([
            'total_pdfs' => $totalPdfs,
            'total_size' => $totalSize,
            'total_size_formatted' => formatBytes($totalSize),
            'quota_limit_gb' => $settings->storage_quota_gb ?? 0,
            'auto_delete_days' => $settings->auto_delete_days,
            'compression_enabled' => $settings->compression_enabled,
        ]);
    }
}
