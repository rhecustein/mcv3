<?php

namespace App\Models;

use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;

class PromoCode extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'code',
        'name',
        'description',
        'discount_type',
        'discount_value',
        'max_discount_amount',
        'min_purchase_amount',
        'usage_limit',
        'usage_limit_per_user',
        'usage_count',
        'valid_from',
        'valid_until',
        'applicable_to',
        'applicable_ids',
        'user_eligibility',
        'is_active',
        'is_public',
        'created_by',
    ];

    protected $casts = [
        'discount_value' => 'decimal:2',
        'max_discount_amount' => 'decimal:2',
        'min_purchase_amount' => 'decimal:2',
        'valid_from' => 'datetime',
        'valid_until' => 'datetime',
        'applicable_ids' => 'array',
        'is_active' => 'boolean',
        'is_public' => 'boolean',
    ];

    /**
     * Relationships
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function usages(): HasMany
    {
        return $this->hasMany(PromoCodeUsage::class);
    }

    /**
     * Generate unique promo code
     */
    public static function generateCode(int $length = 8): string
    {
        $characters = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        $code = '';

        for ($i = 0; $i < $length; $i++) {
            $code .= $characters[rand(0, strlen($characters) - 1)];
        }

        // Check if code already exists
        if (self::where('code', $code)->exists()) {
            return self::generateCode($length);
        }

        return $code;
    }

    /**
     * Check if promo code is valid
     */
    public function isValid(?User $user = null, ?float $amount = null, ?int $packageId = null): array
    {
        // Check if active
        if (!$this->is_active) {
            return ['valid' => false, 'message' => 'This promo code is not active.'];
        }

        // Check validity period
        $now = now();
        if ($this->valid_from && $now->isBefore($this->valid_from)) {
            return ['valid' => false, 'message' => 'This promo code is not yet valid.'];
        }
        if ($this->valid_until && $now->isAfter($this->valid_until)) {
            return ['valid' => false, 'message' => 'This promo code has expired.'];
        }

        // Check usage limit
        if ($this->usage_limit && $this->usage_count >= $this->usage_limit) {
            return ['valid' => false, 'message' => 'This promo code has reached its usage limit.'];
        }

        // Check user-specific conditions
        if ($user) {
            // Check per-user usage limit
            $userUsageCount = $this->usages()->where('user_id', $user->id)->count();
            if ($userUsageCount >= $this->usage_limit_per_user) {
                return ['valid' => false, 'message' => 'You have already used this promo code the maximum number of times.'];
            }

            // Check user eligibility (new vs existing)
            if ($this->user_eligibility === 'new') {
                $hasBookings = McuBooking::where('patient_email', $user->email)->exists();
                if ($hasBookings) {
                    return ['valid' => false, 'message' => 'This promo code is only for new users.'];
                }
            } elseif ($this->user_eligibility === 'existing') {
                $hasBookings = McuBooking::where('patient_email', $user->email)->exists();
                if (!$hasBookings) {
                    return ['valid' => false, 'message' => 'This promo code is only for existing users.'];
                }
            }
        }

        // Check minimum purchase amount
        if ($amount && $amount < $this->min_purchase_amount) {
            return [
                'valid' => false,
                'message' => sprintf(
                    'Minimum purchase of Rp %s is required to use this promo code.',
                    number_format($this->min_purchase_amount, 0, ',', '.')
                )
            ];
        }

        // Check applicability (packages, providers, categories)
        if ($packageId && $this->applicable_to !== 'all') {
            $package = McuPackage::find($packageId);
            if (!$package) {
                return ['valid' => false, 'message' => 'Invalid package.'];
            }

            $isApplicable = match ($this->applicable_to) {
                'packages' => in_array($packageId, $this->applicable_ids ?? []),
                'providers' => in_array($package->provider_id, $this->applicable_ids ?? []),
                'categories' => in_array($package->category, $this->applicable_ids ?? []),
                default => true,
            };

            if (!$isApplicable) {
                return ['valid' => false, 'message' => 'This promo code is not applicable to this package.'];
            }
        }

        return ['valid' => true, 'message' => 'Promo code is valid.'];
    }

    /**
     * Calculate discount amount
     */
    public function calculateDiscount(float $amount): float
    {
        if ($this->discount_type === 'fixed') {
            return min($this->discount_value, $amount);
        }

        // Percentage discount
        $discount = ($amount * $this->discount_value) / 100;

        // Apply max discount limit if set
        if ($this->max_discount_amount) {
            $discount = min($discount, $this->max_discount_amount);
        }

        return round($discount, 2);
    }

    /**
     * Apply promo code and record usage
     */
    public function apply(?User $user, McuBooking $booking, float $originalAmount): array
    {
        // Validate promo code
        $validation = $this->isValid($user, $originalAmount, $booking->package_id);
        if (!$validation['valid']) {
            return [
                'success' => false,
                'message' => $validation['message'],
            ];
        }

        // Calculate discount
        $discountAmount = $this->calculateDiscount($originalAmount);
        $finalAmount = max(0, $originalAmount - $discountAmount);

        // Record usage
        PromoCodeUsage::create([
            'promo_code_id' => $this->id,
            'user_id' => $user?->id,
            'booking_id' => $booking->id,
            'discount_amount' => $discountAmount,
            'original_amount' => $originalAmount,
            'final_amount' => $finalAmount,
        ]);

        // Increment usage count
        $this->increment('usage_count');

        return [
            'success' => true,
            'discount_amount' => $discountAmount,
            'final_amount' => $finalAmount,
            'message' => sprintf(
                'Promo code applied! You saved Rp %s',
                number_format($discountAmount, 0, ',', '.')
            ),
        ];
    }

    /**
     * Get discount description for display
     */
    public function getDiscountDescriptionAttribute(): string
    {
        if ($this->discount_type === 'fixed') {
            return sprintf('Rp %s OFF', number_format($this->discount_value, 0, ',', '.'));
        }

        $text = sprintf('%s%% OFF', $this->discount_value);

        if ($this->max_discount_amount) {
            $text .= sprintf(' (max Rp %s)', number_format($this->max_discount_amount, 0, ',', '.'));
        }

        return $text;
    }

    /**
     * Get remaining usage count
     */
    public function getRemainingUsageAttribute(): ?int
    {
        if (!$this->usage_limit) {
            return null; // Unlimited
        }

        return max(0, $this->usage_limit - $this->usage_count);
    }

    /**
     * Check if promo code is expired
     */
    public function isExpired(): bool
    {
        return $this->valid_until && now()->isAfter($this->valid_until);
    }

    /**
     * Check if promo code is not yet valid
     */
    public function isNotYetValid(): bool
    {
        return $this->valid_from && now()->isBefore($this->valid_from);
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    public function scopeValid($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('valid_from')
                  ->orWhere('valid_from', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('valid_until')
                  ->orWhere('valid_until', '>=', now());
            })
            ->where(function ($q) {
                $q->whereNull('usage_limit')
                  ->orWhereRaw('usage_count < usage_limit');
            });
    }

    public function scopeForPackage($query, int $packageId)
    {
        return $query->where(function ($q) use ($packageId) {
            $q->where('applicable_to', 'all')
              ->orWhere(function ($sq) use ($packageId) {
                  $sq->where('applicable_to', 'packages')
                     ->whereJsonContains('applicable_ids', $packageId);
              });
        });
    }
}
