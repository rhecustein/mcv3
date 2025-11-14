<?php

namespace App\Models;

use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Psychologist extends Model
{
    use BelongsToTenant, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'user_id',
        'name',
        'slug',
        'email',
        'phone',
        'bio',
        'photo',
        'license_number',
        'str_number',
        'str_valid_until',
        'degree',
        'specialization',
        'certifications',
        'years_of_experience',
        'practice_address',
        'city',
        'province',
        'languages',
        'expertise',
        'approaches',
        'available_days',
        'available_from',
        'available_until',
        'accepts_emergency',
        'offers_video',
        'offers_audio',
        'offers_chat',
        'offers_onsite',
        'price_per_session',
        'price_video',
        'price_audio',
        'price_chat',
        'session_duration_minutes',
        'commission_percentage',
        'minimum_payout',
        'bank_name',
        'bank_account_number',
        'bank_account_name',
        'total_sessions',
        'completed_sessions',
        'cancelled_sessions',
        'rating',
        'total_reviews',
        'total_earnings',
        'pending_payout',
        'is_verified',
        'is_active',
        'is_available',
        'is_featured',
        'verified_at',
        'verified_by',
    ];

    protected $casts = [
        'str_valid_until' => 'date',
        'certifications' => 'array',
        'languages' => 'array',
        'expertise' => 'array',
        'approaches' => 'array',
        'available_days' => 'array',
        'price_per_session' => 'decimal:2',
        'price_video' => 'decimal:2',
        'price_audio' => 'decimal:2',
        'price_chat' => 'decimal:2',
        'rating' => 'decimal:2',
        'total_earnings' => 'decimal:2',
        'pending_payout' => 'decimal:2',
        'accepts_emergency' => 'boolean',
        'offers_video' => 'boolean',
        'offers_audio' => 'boolean',
        'offers_chat' => 'boolean',
        'offers_onsite' => 'boolean',
        'is_verified' => 'boolean',
        'is_active' => 'boolean',
        'is_available' => 'boolean',
        'is_featured' => 'boolean',
        'verified_at' => 'datetime',
    ];

    /**
     * Relationships
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(PsychologySession::class);
    }

    public function completedSessions(): HasMany
    {
        return $this->sessions()->where('status', 'completed');
    }

    public function notes(): HasMany
    {
        return $this->hasMany(PsychologyNote::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class, 'provider_id')
            ->where('provider_type', 'psychologist');
    }

    /**
     * Get price for session type
     */
    public function getPriceForSessionType(string $type): float
    {
        return match($type) {
            'video' => $this->price_video ?? $this->price_per_session,
            'audio' => $this->price_audio ?? $this->price_per_session,
            'chat' => $this->price_chat ?? $this->price_per_session,
            default => $this->price_per_session,
        };
    }

    /**
     * Check if psychologist is available on specific day
     */
    public function isAvailableOn(string $day): bool
    {
        return in_array($day, $this->available_days ?? []);
    }

    /**
     * Check if psychologist offers session type
     */
    public function offersSessionType(string $type): bool
    {
        return match($type) {
            'video' => $this->offers_video,
            'audio' => $this->offers_audio,
            'chat' => $this->offers_chat,
            'onsite' => $this->offers_onsite,
            default => false,
        };
    }

    /**
     * Calculate completion rate
     */
    public function getCompletionRateAttribute(): float
    {
        if ($this->total_sessions === 0) {
            return 0;
        }
        return ($this->completed_sessions / $this->total_sessions) * 100;
    }

    /**
     * Check if STR is expired
     */
    public function isStrExpired(): bool
    {
        return $this->str_valid_until && now()->isAfter($this->str_valid_until);
    }

    /**
     * Get average rating
     */
    public function getAverageRating(): float
    {
        return $this->reviews()->approved()->avg('rating') ?? 0;
    }

    /**
     * Scopes
     */
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeAvailable($query)
    {
        return $query->where('is_available', true)
            ->where('is_active', true)
            ->where('is_verified', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeByCity($query, string $city)
    {
        return $query->where('city', $city);
    }

    public function scopeByExpertise($query, string $expertise)
    {
        return $query->whereJsonContains('expertise', $expertise);
    }

    public function scopeBySpecialization($query, string $specialization)
    {
        return $query->where('specialization', $specialization);
    }

    public function scopeAcceptsEmergency($query)
    {
        return $query->where('accepts_emergency', true);
    }

    /**
     * Update statistics
     */
    public function updateStatistics(): void
    {
        $this->total_sessions = $this->sessions()->count();
        $this->completed_sessions = $this->sessions()->where('status', 'completed')->count();
        $this->cancelled_sessions = $this->sessions()->where('status', 'cancelled')->count();
        $this->rating = $this->getAverageRating();
        $this->total_reviews = $this->reviews()->approved()->count();
        $this->save();
    }

    /**
     * Add earnings
     */
    public function addEarnings(float $amount): void
    {
        $commission = $amount * ($this->commission_percentage / 100);
        $psychologistEarning = $amount - $commission;

        $this->total_earnings += $psychologistEarning;
        $this->pending_payout += $psychologistEarning;
        $this->save();
    }

    /**
     * Process payout
     */
    public function processPayout(float $amount): void
    {
        if ($amount > $this->pending_payout) {
            throw new \Exception('Payout amount exceeds pending amount');
        }

        $this->pending_payout -= $amount;
        $this->save();
    }
}
