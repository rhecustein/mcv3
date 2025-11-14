<?php

namespace App\Models;

use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PsychologyPackage extends Model
{
    use BelongsToTenant, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'name',
        'slug',
        'code',
        'description',
        'type',
        'total_sessions',
        'session_duration_minutes',
        'includes_video',
        'includes_audio',
        'includes_chat',
        'includes_emergency_support',
        'price',
        'price_per_employee',
        'discounted_price',
        'discount_percentage',
        'billing_period',
        'validity_days',
        'features',
        'includes_mood_tracker',
        'includes_self_assessment',
        'includes_webinar',
        'includes_onsite_psychologist',
        'includes_hr_dashboard',
        'minimum_employees',
        'maximum_employees',
        'requires_contract',
        'max_sessions_per_month',
        'max_emergency_calls',
        'is_active',
        'is_featured',
        'is_public',
        'image',
        'popularity_score',
        'total_subscriptions',
    ];

    protected $casts = [
        'includes_video' => 'boolean',
        'includes_audio' => 'boolean',
        'includes_chat' => 'boolean',
        'includes_emergency_support' => 'boolean',
        'price' => 'decimal:2',
        'price_per_employee' => 'decimal:2',
        'discounted_price' => 'decimal:2',
        'features' => 'array',
        'includes_mood_tracker' => 'boolean',
        'includes_self_assessment' => 'boolean',
        'includes_webinar' => 'boolean',
        'includes_onsite_psychologist' => 'boolean',
        'includes_hr_dashboard' => 'boolean',
        'requires_contract' => 'boolean',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'is_public' => 'boolean',
    ];

    /**
     * Relationships
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(PsychologySubscription::class, 'package_id');
    }

    public function activeSubscriptions(): HasMany
    {
        return $this->subscriptions()->where('status', 'active');
    }

    /**
     * Get final price (considering discount)
     */
    public function getFinalPriceAttribute(): float
    {
        return $this->discounted_price ?? $this->price;
    }

    /**
     * Check if package has discount
     */
    public function hasDiscount(): bool
    {
        return $this->discounted_price && $this->discounted_price < $this->price;
    }

    /**
     * Calculate discount amount
     */
    public function getDiscountAmountAttribute(): float
    {
        if (!$this->hasDiscount()) {
            return 0;
        }
        return $this->price - $this->discounted_price;
    }

    /**
     * Check if package is B2B
     */
    public function isB2B(): bool
    {
        return $this->type === 'b2b';
    }

    /**
     * Check if package is B2C
     */
    public function isB2C(): bool
    {
        return $this->type === 'b2c';
    }

    /**
     * Check if package is recurring
     */
    public function isRecurring(): bool
    {
        return in_array($this->billing_period, ['monthly', 'quarterly', 'yearly']);
    }

    /**
     * Get price per session
     */
    public function getPricePerSessionAttribute(): float
    {
        if ($this->total_sessions === 0) {
            return 0;
        }
        return $this->final_price / $this->total_sessions;
    }

    /**
     * Calculate price for number of employees (B2B)
     */
    public function calculatePriceForEmployees(int $employeeCount): float
    {
        if (!$this->isB2B()) {
            return $this->final_price;
        }

        if ($this->minimum_employees && $employeeCount < $this->minimum_employees) {
            throw new \Exception("Minimum {$this->minimum_employees} employees required");
        }

        if ($this->maximum_employees && $employeeCount > $this->maximum_employees) {
            throw new \Exception("Maximum {$this->maximum_employees} employees allowed");
        }

        return $this->price_per_employee * $employeeCount;
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    public function scopeB2B($query)
    {
        return $query->where('type', 'b2b');
    }

    public function scopeB2C($query)
    {
        return $query->where('type', 'b2c');
    }

    public function scopeRecurring($query)
    {
        return $query->whereIn('billing_period', ['monthly', 'quarterly', 'yearly']);
    }

    public function scopeOneTime($query)
    {
        return $query->where('billing_period', 'one_time');
    }

    /**
     * Increment subscription count
     */
    public function incrementSubscriptions(): void
    {
        $this->increment('total_subscriptions');
        $this->increment('popularity_score', 5);
    }
}
