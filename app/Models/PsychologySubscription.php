<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

/**
 * Psychology Subscription Model
 * Manages patient subscriptions to psychology packages
 */
class PsychologySubscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'package_id',
        'status',
        'start_date',
        'end_date',
        'sessions_total',
        'sessions_used',
        'sessions_remaining',
        'amount_paid',
        'payment_method',
        'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'sessions_total' => 'integer',
        'sessions_used' => 'integer',
        'sessions_remaining' => 'integer',
        'amount_paid' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Patient who owns this subscription
     */
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Package this subscription is for
     */
    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    /**
     * Psychology sessions used in this subscription
     */
    public function sessions()
    {
        return $this->hasMany(PsychologySession::class, 'subscription_id');
    }

    /**
     * Scope: Only active subscriptions
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now());
    }

    /**
     * Scope: Expired subscriptions
     */
    public function scopeExpired($query)
    {
        return $query->where(function ($q) {
            $q->where('status', 'expired')
                ->orWhere('end_date', '<', now());
        });
    }

    /**
     * Scope: Subscriptions with remaining sessions
     */
    public function scopeWithSessions($query)
    {
        return $query->where('sessions_remaining', '>', 0);
    }

    /**
     * Check if subscription is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active'
            && $this->start_date <= now()
            && $this->end_date >= now();
    }

    /**
     * Check if subscription has sessions remaining
     */
    public function hasSessionsRemaining(): bool
    {
        return $this->sessions_remaining > 0;
    }

    /**
     * Check if subscription is expired
     */
    public function isExpired(): bool
    {
        return $this->status === 'expired' || $this->end_date < now();
    }

    /**
     * Use one session (decrement remaining)
     */
    public function useSession(): bool
    {
        if (!$this->hasSessionsRemaining()) {
            return false;
        }

        $this->decrement('sessions_remaining');
        $this->increment('sessions_used');

        if ($this->sessions_remaining === 0) {
            $this->update(['status' => 'completed']);
        }

        return true;
    }

    /**
     * Get days remaining until expiration
     */
    public function getDaysRemainingAttribute(): int
    {
        if ($this->isExpired()) {
            return 0;
        }

        return now()->diffInDays($this->end_date);
    }

    /**
     * Get usage percentage
     */
    public function getUsagePercentageAttribute(): float
    {
        if ($this->sessions_total === 0) {
            return 0;
        }

        return round(($this->sessions_used / $this->sessions_total) * 100, 2);
    }
}
