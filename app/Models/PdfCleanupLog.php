<?php

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
        'archived_count' => 'integer',
        'deleted_count' => 'integer',
        'error_count' => 'integer',
    ];

    /**
     * Get human-readable freed size
     */
    public function getFreedSizeAttribute(): string
    {
        return $this->formatBytes($this->freed_bytes);
    }

    /**
     * Format bytes to human-readable size
     */
    private function formatBytes(?int $bytes): string
    {
        if ($bytes === null || $bytes === 0) {
            return '0 B';
        }

        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes > 1024 && $i < 4; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Scope: Get logs for specific tenant
     */
    public function scopeForTenant($query, ?string $tenantId)
    {
        if ($tenantId) {
            return $query->where('tenant_id', $tenantId);
        }

        return $query;
    }

    /**
     * Scope: Recent logs
     */
    public function scopeRecent($query, int $limit = 10)
    {
        return $query->orderByDesc('executed_at')->limit($limit);
    }
}
