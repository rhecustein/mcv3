<?php

namespace App\Models;

use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class McuProvider extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'name',
        'slug',
        'code',
        'description',
        'logo',
        'email',
        'phone',
        'address',
        'city',
        'province',
        'postal_code',
        'latitude',
        'longitude',
        'operating_hours',
        'facilities',
        'certifications',
        'is_verified',
        'is_active',
        'rating',
        'total_reviews',
    ];

    protected $casts = [
        'operating_hours' => 'array',
        'facilities' => 'array',
        'certifications' => 'array',
        'is_verified' => 'boolean',
        'is_active' => 'boolean',
        'rating' => 'decimal:2',
        'total_reviews' => 'integer',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
    ];

    /**
     * Packages offered by this provider
     */
    public function packages(): HasMany
    {
        return $this->hasMany(McuPackage::class, 'provider_id');
    }

    /**
     * Bookings for this provider
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(McuBooking::class, 'provider_id');
    }

    /**
     * Active packages
     */
    public function activePackages(): HasMany
    {
        return $this->packages()->where('is_active', true);
    }

    /**
     * Scope for verified providers
     */
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    /**
     * Scope for active providers
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope by city
     */
    public function scopeInCity($query, string $city)
    {
        return $query->where('city', $city);
    }

    /**
     * Update rating
     */
    public function updateRating(float $newRating): void
    {
        $totalReviews = $this->total_reviews;
        $currentRating = $this->rating;

        $newTotal = $totalReviews + 1;
        $newAverage = (($currentRating * $totalReviews) + $newRating) / $newTotal;

        $this->update([
            'rating' => round($newAverage, 2),
            'total_reviews' => $newTotal,
        ]);
    }
}
