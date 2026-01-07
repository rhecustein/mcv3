# 📁 PDF Storage Management - Superadmin Feature

Complete PDF storage management system for superadmin with auto-cleanup, archiving, and monitoring.

**Version**: 1.0
**Date**: 2026-01-07
**Project**: SehatCert

---

## 🎯 Features

### **For Superadmin**
1. ✅ **Auto-Delete Old PDFs** (configurable: 1 month, 3 months, 6 months, 1 year)
2. ✅ **Archive Before Delete** (to S3/Glacier for compliance)
3. ✅ **Storage Usage Dashboard** (per tenant, total)
4. ✅ **Manual Cleanup** (bulk delete by tenant/date)
5. ✅ **Cleanup History Log** (audit trail)
6. ✅ **Storage Alerts** (when usage > threshold)
7. ✅ **Restore from Archive** (restore deleted PDFs)
8. ✅ **Storage Optimization** (compress old PDFs)

---

## 📊 Database Migrations

### **1. Add PDF Tracking Columns to `results` table**

```php
<?php
// database/migrations/2026_01_07_create_pdf_tracking_columns.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('results', function (Blueprint $table) {
            // PDF tracking
            $table->string('pdf_path')->nullable()->after('qrcode');
            $table->timestamp('pdf_generated_at')->nullable();
            $table->timestamp('pdf_deleted_at')->nullable();
            $table->boolean('pdf_archived')->default(false);
            $table->string('pdf_archive_path')->nullable();
            $table->boolean('pdf_generation_failed')->default(false);
            $table->text('pdf_error')->nullable();
            $table->bigInteger('pdf_size_bytes')->nullable();

            // Indexes
            $table->index('pdf_generated_at');
            $table->index('pdf_deleted_at');
            $table->index(['tenant_id', 'pdf_generated_at']);
        });
    }

    public function down(): void
    {
        Schema::table('results', function (Blueprint $table) {
            $table->dropColumn([
                'pdf_path',
                'pdf_generated_at',
                'pdf_deleted_at',
                'pdf_archived',
                'pdf_archive_path',
                'pdf_generation_failed',
                'pdf_error',
                'pdf_size_bytes',
            ]);
        });
    }
};
```

### **2. PDF Cleanup Logs Table**

```php
<?php
// database/migrations/2026_01_07_create_pdf_cleanup_logs_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pdf_cleanup_logs', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id')->nullable(); // null = all tenants
            $table->integer('days_old'); // PDFs older than X days
            $table->integer('archived_count')->default(0);
            $table->integer('deleted_count')->default(0);
            $table->integer('error_count')->default(0);
            $table->bigInteger('freed_bytes')->default(0); // Storage freed
            $table->boolean('archive_enabled')->default(true);
            $table->string('triggered_by')->nullable(); // 'schedule', 'manual', 'user_id'
            $table->text('notes')->nullable();
            $table->timestamp('executed_at');
            $table->timestamps();

            $table->index('executed_at');
            $table->index(['tenant_id', 'executed_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pdf_cleanup_logs');
    }
};
```

### **3. PDF Storage Settings Table**

```php
<?php
// database/migrations/2026_01_07_create_pdf_storage_settings_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pdf_storage_settings', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id')->nullable(); // null = global default
            $table->integer('auto_delete_days')->default(90); // 3 months
            $table->boolean('auto_delete_enabled')->default(true);
            $table->boolean('archive_before_delete')->default(true);
            $table->string('archive_storage')->default('s3'); // s3, local, glacier
            $table->integer('compression_days')->default(30); // Compress PDFs older than 30 days
            $table->boolean('compression_enabled')->default(false);
            $table->bigInteger('storage_quota_bytes')->nullable(); // Max storage per tenant
            $table->boolean('alert_enabled')->default(true);
            $table->integer('alert_threshold_percent')->default(80); // Alert at 80%
            $table->string('alert_email')->nullable();
            $table->timestamps();

            $table->index('tenant_id');
            $table->unique('tenant_id');
        });

        // Insert default global settings
        DB::table('pdf_storage_settings')->insert([
            'tenant_id' => null,
            'auto_delete_days' => 90,
            'auto_delete_enabled' => true,
            'archive_before_delete' => true,
            'archive_storage' => 's3',
            'compression_days' => 30,
            'compression_enabled' => false,
            'storage_quota_bytes' => null,
            'alert_enabled' => true,
            'alert_threshold_percent' => 80,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('pdf_storage_settings');
    }
};
```

---

## 🏗️ Models

### **PdfCleanupLog Model**

```php
<?php
// app/Models/PdfCleanupLog.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PdfCleanupLog extends Model
{
    protected $fillable = [
        'tenant_id',
        'days_old',
        'archived_count',
        'deleted_count',
        'error_count',
        'freed_bytes',
        'archive_enabled',
        'triggered_by',
        'notes',
        'executed_at',
    ];

    protected $casts = [
        'executed_at' => 'datetime',
        'archive_enabled' => 'boolean',
        'freed_bytes' => 'integer',
    ];

    /**
     * Get human-readable size
     */
    public function getFreedSizeAttribute(): string
    {
        return $this->formatBytes($this->freed_bytes);
    }

    /**
     * Format bytes to human-readable size
     */
    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes > 1024 && $i < 4; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }
}
```

### **PdfStorageSetting Model**

```php
<?php
// app/Models/PdfStorageSetting.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PdfStorageSetting extends Model
{
    protected $fillable = [
        'tenant_id',
        'auto_delete_days',
        'auto_delete_enabled',
        'archive_before_delete',
        'archive_storage',
        'compression_days',
        'compression_enabled',
        'storage_quota_bytes',
        'alert_enabled',
        'alert_threshold_percent',
        'alert_email',
    ];

    protected $casts = [
        'auto_delete_enabled' => 'boolean',
        'archive_before_delete' => 'boolean',
        'compression_enabled' => 'boolean',
        'alert_enabled' => 'boolean',
        'storage_quota_bytes' => 'integer',
    ];

    /**
     * Get settings for tenant (or global default)
     */
    public static function forTenant(?string $tenantId = null): self
    {
        return static::firstOrCreate(
            ['tenant_id' => $tenantId],
            [
                'auto_delete_days' => 90,
                'auto_delete_enabled' => true,
                'archive_before_delete' => true,
            ]
        );
    }

    /**
     * Get global default settings
     */
    public static function globalDefaults(): self
    {
        return static::where('tenant_id', null)->firstOrFail();
    }
}
```

---

## 🎮 Controllers

### **Superadmin PDF Storage Controller**

```php
<?php
// app/Http/Controllers/Superadmin/PdfStorageController.php

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
            ->get();

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
            ->with('tenant')
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
            'archive' => 'required|boolean',
        ]);

        // Dispatch cleanup job
        CleanupOldPdfsJob::dispatch(
            $validated['days_old'],
            $validated['archive'],
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
     * Storage statistics API
     */
    public function statistics()
    {
        $stats = [
            'total' => [
                'pdfs' => Result::whereNotNull('pdf_path')->count(),
                'size_bytes' => Result::sum('pdf_size_bytes'),
                'size_formatted' => $this->formatBytes(Result::sum('pdf_size_bytes')),
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
```

---

## 🎨 Views (Blade Templates)

### **Dashboard View**

```blade
{{-- resources/views/superadmin/pdf-storage/index.blade.php --}}

@extends('layouts.admin')

@section('title', 'PDF Storage Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h1 class="h3 mb-4">📁 PDF Storage Management</h1>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-primary">
                <div class="card-body">
                    <h5 class="card-title text-muted">Total PDFs</h5>
                    <h2 class="mb-0">{{ number_format($totalPdfs) }}</h2>
                    <small class="text-muted">Stored certificates</small>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-success">
                <div class="card-body">
                    <h5 class="card-title text-muted">Total Size</h5>
                    <h2 class="mb-0">{{ formatBytes($totalSize) }}</h2>
                    <small class="text-muted">Disk usage</small>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-info">
                <div class="card-body">
                    <h5 class="card-title text-muted">Archived</h5>
                    <h2 class="mb-0">{{ number_format($archivedCount) }}</h2>
                    <small class="text-muted">In cold storage</small>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-warning">
                <div class="card-body">
                    <h5 class="card-title text-muted">Auto-Delete</h5>
                    <h2 class="mb-0">{{ $globalSettings->auto_delete_days }} days</h2>
                    <small class="text-muted">
                        <span class="badge {{ $globalSettings->auto_delete_enabled ? 'bg-success' : 'bg-secondary' }}">
                            {{ $globalSettings->auto_delete_enabled ? 'Enabled' : 'Disabled' }}
                        </span>
                    </small>
                </div>
            </div>
        </div>
    </div>

    {{-- Storage by Tenant --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Storage by Tenant</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Tenant ID</th>
                                    <th class="text-end">PDF Count</th>
                                    <th class="text-end">Total Size</th>
                                    <th class="text-end">Oldest PDF</th>
                                    <th class="text-end">Latest PDF</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($storageByTenant as $storage)
                                <tr>
                                    <td>
                                        <code>{{ $storage->tenant_id }}</code>
                                    </td>
                                    <td class="text-end">{{ number_format($storage->pdf_count) }}</td>
                                    <td class="text-end">
                                        <strong>{{ $storage->total_size }}</strong>
                                    </td>
                                    <td class="text-end">
                                        @if($storage->oldest_pdf_days)
                                            <span class="badge bg-secondary">{{ $storage->oldest_pdf_days }} days ago</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if($storage->latest_pdf_days !== null)
                                            <span class="badge bg-info">{{ $storage->latest_pdf_days }} days ago</span>
                                        @endif
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-danger" onclick="cleanupTenant('{{ $storage->tenant_id }}')">
                                            <i class="bi bi-trash"></i> Cleanup
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted">No data available</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Manual Cleanup --}}
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-danger text-white">
                    <h5 class="card-title mb-0">🗑️ Manual Cleanup</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('superadmin.pdf-storage.cleanup') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Delete PDFs older than</label>
                            <select name="days_old" class="form-select" required>
                                <option value="30">30 days (1 month)</option>
                                <option value="60">60 days (2 months)</option>
                                <option value="90" selected>90 days (3 months)</option>
                                <option value="180">180 days (6 months)</option>
                                <option value="365">365 days (1 year)</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tenant (Optional)</label>
                            <input type="text" name="tenant_id" class="form-control" placeholder="Leave empty for all tenants">
                        </div>

                        <div class="form-check mb-3">
                            <input type="checkbox" name="archive" value="1" class="form-check-input" id="archiveCheck" checked>
                            <label class="form-check-label" for="archiveCheck">
                                Archive before delete (recommended)
                            </label>
                        </div>

                        <button type="submit" class="btn btn-danger w-100" onclick="return confirm('Are you sure you want to cleanup old PDFs?')">
                            <i class="bi bi-trash"></i> Run Cleanup Now
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Recent Cleanups --}}
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">📜 Recent Cleanup Logs</h5>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        @forelse($recentCleanups as $log)
                        <div class="list-group-item px-0">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>{{ $log->executed_at->format('Y-m-d H:i') }}</strong>
                                    <br>
                                    <small class="text-muted">
                                        {{ $log->deleted_count }} deleted • {{ $log->archived_count }} archived
                                        @if($log->tenant_id)
                                            • Tenant: <code>{{ $log->tenant_id }}</code>
                                        @else
                                            • All tenants
                                        @endif
                                    </small>
                                </div>
                                <span class="badge bg-success">{{ $log->freed_size }}</span>
                            </div>
                        </div>
                        @empty
                        <p class="text-muted text-center py-3">No cleanup logs yet</p>
                        @endforelse
                    </div>

                    <a href="{{ route('superadmin.pdf-storage.logs') }}" class="btn btn-sm btn-outline-primary w-100 mt-3">
                        View All Logs
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">⚡ Quick Actions</h5>
                    <div class="btn-group" role="group">
                        <a href="{{ route('superadmin.pdf-storage.settings') }}" class="btn btn-outline-primary">
                            <i class="bi bi-gear"></i> Settings
                        </a>
                        <a href="{{ route('superadmin.pdf-storage.logs') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-file-earmark-text"></i> View Logs
                        </a>
                        <a href="{{ route('superadmin.pdf-storage.statistics') }}" class="btn btn-outline-info">
                            <i class="bi bi-graph-up"></i> Statistics
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function cleanupTenant(tenantId) {
    if (confirm(`Cleanup old PDFs for tenant ${tenantId}?`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("superadmin.pdf-storage.cleanup") }}';

        form.innerHTML = `
            @csrf
            <input type="hidden" name="tenant_id" value="${tenantId}">
            <input type="hidden" name="days_old" value="90">
            <input type="hidden" name="archive" value="1">
        `;

        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endpush
@endsection
```

---

## 🛣️ Routes

```php
<?php
// routes/web.php

use App\Http\Controllers\Superadmin\PdfStorageController;

Route::middleware(['auth', 'superadmin'])->prefix('superadmin')->name('superadmin.')->group(function () {

    Route::prefix('pdf-storage')->name('pdf-storage.')->group(function () {
        Route::get('/', [PdfStorageController::class, 'index'])->name('index');
        Route::get('/settings', [PdfStorageController::class, 'settings'])->name('settings');
        Route::post('/settings', [PdfStorageController::class, 'updateSettings'])->name('update-settings');
        Route::post('/cleanup', [PdfStorageController::class, 'cleanup'])->name('cleanup');
        Route::get('/logs', [PdfStorageController::class, 'logs'])->name('logs');
        Route::post('/restore', [PdfStorageController::class, 'restore'])->name('restore');
        Route::get('/statistics', [PdfStorageController::class, 'statistics'])->name('statistics');
    });

});
```

---

## 📅 Scheduled Commands

```php
<?php
// app/Console/Kernel.php

protected function schedule(Schedule $schedule): void
{
    // Daily cleanup at 2 AM
    $schedule->call(function () {
        $settings = \App\Models\PdfStorageSetting::globalDefaults();

        if ($settings->auto_delete_enabled) {
            \App\Jobs\Maintenance\CleanupOldPdfsJob::dispatch(
                $settings->auto_delete_days,
                $settings->archive_before_delete
            );
        }
    })->dailyAt('02:00')->name('auto-cleanup-pdfs')->onOneServer();

    // Weekly storage report (Mondays at 8 AM)
    $schedule->command('pdf:storage-report')->weeklyOn(1, '08:00');

    // Monthly deep archive (very old PDFs to Glacier)
    $schedule->call(function () {
        \App\Jobs\Maintenance\ArchiveToGlacierJob::dispatch(365); // 1 year old
    })->monthlyOn(1, '03:00')->name('monthly-glacier-archive');
}
```

---

## 🎯 Summary

**PDF Storage Management Features**:

✅ **Auto-Delete**: Configurable (7 days - 10 years)
✅ **Archive Before Delete**: S3/Glacier integration
✅ **Storage Dashboard**: Real-time usage monitoring
✅ **Manual Cleanup**: Bulk delete by tenant/date
✅ **Cleanup Logs**: Complete audit trail
✅ **Restore**: Recover archived PDFs
✅ **Statistics**: Usage trends and insights
✅ **Alerts**: Email notifications for storage limits

**Database Tables**:
1. `results` table (updated with PDF tracking columns)
2. `pdf_cleanup_logs` (cleanup history)
3. `pdf_storage_settings` (global + per-tenant settings)

**Next Steps**:
1. Run migrations
2. Update `.env` with S3 credentials
3. Configure Supervisor for queue workers
4. Access: `/superadmin/pdf-storage`
