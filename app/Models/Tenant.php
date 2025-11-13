<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Tenant extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'domain',
        'logo',
        'subscription_plan',
        'subscription_status',
        'trial_ends_at',
        'subscription_ends_at',
        'max_users',
        'max_documents_per_month',
        'max_storage_mb',
        'current_users',
        'current_documents_this_month',
        'current_storage_mb',
        'settings',
        'enabled_features',
        'contact_email',
        'contact_phone',
        'is_active',
    ];

    protected $casts = [
        'settings' => 'array',
        'enabled_features' => 'array',
        'trial_ends_at' => 'datetime',
        'subscription_ends_at' => 'datetime',
        'is_active' => 'boolean',
        'max_users' => 'integer',
        'max_documents_per_month' => 'integer',
        'max_storage_mb' => 'integer',
        'current_users' => 'integer',
        'current_documents_this_month' => 'integer',
        'current_storage_mb' => 'decimal:2',
    ];

    /**
     * Relationships
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(TenantSubscription::class);
    }

    public function currentSubscription()
    {
        return $this->hasOne(TenantSubscription::class)
            ->where('status', 'active')
            ->latest();
    }

    public function usage()
    {
        return $this->hasMany(TenantUsage::class);
    }

    public function invoices()
    {
        return $this->hasMany(TenantInvoice::class);
    }

    /**
     * Trial Management
     */
    public function isTrialing(): bool
    {
        return $this->subscription_status === 'trial'
            && $this->trial_ends_at
            && $this->trial_ends_at->isFuture();
    }

    public function trialDaysRemaining(): int
    {
        if (!$this->isTrialing()) {
            return 0;
        }

        return max(0, now()->diffInDays($this->trial_ends_at, false));
    }

    public function isTrialExpired(): bool
    {
        return $this->subscription_status === 'trial'
            && $this->trial_ends_at
            && $this->trial_ends_at->isPast();
    }

    /**
     * Subscription Status
     */
    public function isSubscribed(): bool
    {
        return in_array($this->subscription_status, ['trial', 'active']);
    }

    public function isActive(): bool
    {
        return $this->is_active && $this->isSubscribed();
    }

    public function isSuspended(): bool
    {
        return $this->subscription_status === 'suspended';
    }

    public function isCancelled(): bool
    {
        return $this->subscription_status === 'cancelled';
    }

    /**
     * Quota Management
     */
    public function canCreateUser(): bool
    {
        return $this->current_users < $this->max_users;
    }

    public function canGenerateDocument(): bool
    {
        return $this->current_documents_this_month < $this->max_documents_per_month;
    }

    public function hasStorageSpace(float $requiredMb): bool
    {
        return ($this->current_storage_mb + $requiredMb) <= $this->max_storage_mb;
    }

    public function getRemainingDocuments(): int
    {
        return max(0, $this->max_documents_per_month - $this->current_documents_this_month);
    }

    public function getRemainingStorage(): float
    {
        return max(0, $this->max_storage_mb - $this->current_storage_mb);
    }

    public function getUsagePercentage(string $type): float
    {
        return match($type) {
            'users' => ($this->current_users / max($this->max_users, 1)) * 100,
            'documents' => ($this->current_documents_this_month / max($this->max_documents_per_month, 1)) * 100,
            'storage' => (floatval($this->current_storage_mb) / max($this->max_storage_mb, 1)) * 100,
            default => 0,
        };
    }

    /**
     * Usage Tracking
     */
    public function incrementDocumentCount(): void
    {
        $this->increment('current_documents_this_month');
    }

    public function incrementStorageUsage(float $mb): void
    {
        $this->increment('current_storage_mb', $mb);
    }

    public function decrementStorageUsage(float $mb): void
    {
        $this->decrement('current_storage_mb', max(0, $mb));
    }

    public function updateUserCount(): void
    {
        $this->update([
            'current_users' => $this->users()->count()
        ]);
    }

    public function resetMonthlyUsage(): void
    {
        $this->update([
            'current_documents_this_month' => 0,
        ]);
    }

    /**
     * Plan Features
     */
    public function hasFeature(string $feature): bool
    {
        if (!$this->enabled_features) {
            return false;
        }

        return in_array($feature, $this->enabled_features);
    }

    public function getPlanName(): string
    {
        return match($this->subscription_plan) {
            'starter' => 'Starter',
            'professional' => 'Professional',
            'enterprise' => 'Enterprise',
            default => 'Unknown',
        };
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->whereIn('subscription_status', ['trial', 'active']);
    }

    public function scopeTrialing($query)
    {
        return $query->where('subscription_status', 'trial')
            ->where('trial_ends_at', '>', now());
    }

    public function scopeSuspended($query)
    {
        return $query->where('subscription_status', 'suspended');
    }

    /**
     * Boot
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($tenant) {
            // Set default trial period (14 days)
            if (!$tenant->trial_ends_at) {
                $tenant->trial_ends_at = now()->addDays(14);
            }

            // Set default subscription status
            if (!$tenant->subscription_status) {
                $tenant->subscription_status = 'trial';
            }
        });
    }
}
