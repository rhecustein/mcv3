<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'price_monthly',
        'price_yearly',
        'currency',
        'max_users',
        'max_documents_per_month',
        'max_storage_mb',
        'max_api_calls_per_day',
        'features',
        'is_active',
        'is_featured',
        'sort_order',
    ];

    protected $casts = [
        'features' => 'array',
        'price_monthly' => 'decimal:2',
        'price_yearly' => 'decimal:2',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'max_users' => 'integer',
        'max_documents_per_month' => 'integer',
        'max_storage_mb' => 'integer',
        'max_api_calls_per_day' => 'integer',
        'sort_order' => 'integer',
    ];

    /**
     * Relationships
     */
    public function subscriptions()
    {
        return $this->hasMany(TenantSubscription::class);
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Helpers
     */
    public function getFormattedPriceMonthly(): string
    {
        return 'Rp ' . number_format($this->price_monthly, 0, ',', '.');
    }

    public function getFormattedPriceYearly(): string
    {
        if (!$this->price_yearly) {
            return '-';
        }
        return 'Rp ' . number_format($this->price_yearly, 0, ',', '.');
    }

    public function getYearlySavings(): float
    {
        if (!$this->price_yearly) {
            return 0;
        }

        $yearlyMonthly = $this->price_monthly * 12;
        return $yearlyMonthly - $this->price_yearly;
    }

    public function getYearlySavingsPercentage(): int
    {
        $savings = $this->getYearlySavings();
        if ($savings <= 0) {
            return 0;
        }

        $yearlyMonthly = $this->price_monthly * 12;
        return round(($savings / $yearlyMonthly) * 100);
    }

    public function hasFeature(string $feature): bool
    {
        if (!$this->features) {
            return false;
        }

        return in_array($feature, $this->features);
    }
}
