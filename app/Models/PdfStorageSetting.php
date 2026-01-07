<?php

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
        'auto_delete_days' => 'integer',
        'compression_days' => 'integer',
        'alert_threshold_percent' => 'integer',
    ];

    /**
     * Get settings for tenant (or create with defaults)
     */
    public static function forTenant(?string $tenantId = null): self
    {
        return static::firstOrCreate(
            ['tenant_id' => $tenantId],
            [
                'auto_delete_days' => 90,
                'auto_delete_enabled' => true,
                'archive_before_delete' => true,
                'archive_storage' => 's3',
                'compression_enabled' => false,
                'compression_days' => 30,
                'alert_enabled' => true,
                'alert_threshold_percent' => 80,
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

    /**
     * Get human-readable quota
     */
    public function getQuotaFormattedAttribute(): ?string
    {
        if ($this->storage_quota_bytes === null) {
            return 'Unlimited';
        }

        return $this->formatBytes($this->storage_quota_bytes);
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
