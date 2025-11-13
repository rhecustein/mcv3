<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TenantSubscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'subscription_plan_id',
        'starts_at',
        'ends_at',
        'billing_cycle',
        'price',
        'currency',
        'status',
        'auto_renew',
        'payment_method',
        'payment_reference',
        'paid_at',
        'is_trial',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'paid_at' => 'datetime',
        'price' => 'decimal:2',
        'auto_renew' => 'boolean',
        'is_trial' => 'boolean',
    ];

    /**
     * Relationships
     */
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function plan()
    {
        return $this->belongsTo(SubscriptionPlan::class, 'subscription_plan_id');
    }

    /**
     * Status Checks
     */
    public function isActive(): bool
    {
        return $this->status === 'active'
            && $this->starts_at->isPast()
            && $this->ends_at->isFuture();
    }

    public function isExpired(): bool
    {
        return $this->ends_at->isPast();
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    public function daysRemaining(): int
    {
        if ($this->isExpired()) {
            return 0;
        }

        return max(0, now()->diffInDays($this->ends_at, false));
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
            ->where('starts_at', '<=', now())
            ->where('ends_at', '>', now());
    }

    public function scopeExpired($query)
    {
        return $query->where('ends_at', '<', now());
    }

    public function scopeExpiringSoon($query, int $days = 7)
    {
        return $query->where('status', 'active')
            ->whereBetween('ends_at', [now(), now()->addDays($days)]);
    }
}
