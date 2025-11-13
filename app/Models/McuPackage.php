<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class McuPackage extends Model
{
    protected $fillable = [
        'provider_id',
        'name',
        'slug',
        'code',
        'description',
        'inclusions',
        'exclusions',
        'preparation_instructions',
        'price',
        'discounted_price',
        'discount_percentage',
        'category',
        'gender_target',
        'min_age',
        'max_age',
        'duration_minutes',
        'validity_days',
        'image',
        'images',
        'is_featured',
        'is_active',
        'booking_count',
        'stock',
    ];

    protected $casts = [
        'inclusions' => 'array',
        'exclusions' => 'array',
        'preparation_instructions' => 'array',
        'images' => 'array',
        'price' => 'decimal:2',
        'discounted_price' => 'decimal:2',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Provider relationship
     */
    public function provider(): BelongsTo
    {
        return $this->belongsTo(McuProvider::class, 'provider_id');
    }

    /**
     * Bookings for this package
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(McuBooking::class, 'package_id');
    }

    /**
     * Reviews for this package
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class, 'package_id');
    }

    /**
     * Approved reviews only
     */
    public function approvedReviews(): HasMany
    {
        return $this->reviews()->where('status', 'approved');
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
     * Scope for active packages
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for featured packages
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope by category
     */
    public function scopeCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope by gender
     */
    public function scopeForGender($query, string $gender)
    {
        return $query->where(function($q) use ($gender) {
            $q->where('gender_target', 'all')
              ->orWhere('gender_target', $gender);
        });
    }

    /**
     * Increment booking count
     */
    public function incrementBookingCount(): void
    {
        $this->increment('booking_count');
    }
}
