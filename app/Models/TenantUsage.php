<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TenantUsage extends Model
{
    use HasFactory;

    protected $table = 'tenant_usage';

    protected $fillable = [
        'tenant_id',
        'period_start',
        'period_end',
        'period_type',
        'documents_generated',
        'mcu_bookings',
        'storage_used_mb',
        'api_calls',
        'active_users',
        'estimated_cost',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'documents_generated' => 'integer',
        'mcu_bookings' => 'integer',
        'storage_used_mb' => 'decimal:2',
        'api_calls' => 'integer',
        'active_users' => 'integer',
        'estimated_cost' => 'decimal:2',
    ];

    /**
     * Relationships
     */
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Scopes
     */
    public function scopeForPeriod($query, $start, $end)
    {
        return $query->where('period_start', '>=', $start)
            ->where('period_end', '<=', $end);
    }

    public function scopeCurrentMonth($query)
    {
        return $query->where('period_start', now()->startOfMonth())
            ->where('period_end', now()->endOfMonth());
    }

    /**
     * Helpers
     */
    public static function recordUsage(int $tenantId, array $metrics): self
    {
        $now = now();

        return static::updateOrCreate(
            [
                'tenant_id' => $tenantId,
                'period_start' => $now->copy()->startOfMonth(),
                'period_end' => $now->copy()->endOfMonth(),
                'period_type' => 'monthly',
            ],
            $metrics
        );
    }

    public static function incrementMetric(int $tenantId, string $metric, $value = 1): void
    {
        $usage = static::recordUsage($tenantId, []);
        $usage->increment($metric, $value);
    }
}
