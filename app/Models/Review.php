<?php

namespace App\Models;

use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Review extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'booking_id',
        'package_id',
        'provider_id',
        'user_id',
        'rating',
        'title',
        'comment',
        'service_rating',
        'cleanliness_rating',
        'staff_rating',
        'value_rating',
        'is_verified',
        'is_anonymous',
        'status',
        'moderation_notes',
        'moderated_by',
        'moderated_at',
        'provider_response',
        'provider_responded_at',
        'helpful_count',
        'not_helpful_count',
    ];

    protected $casts = [
        'is_verified' => 'boolean',
        'is_anonymous' => 'boolean',
        'moderated_at' => 'datetime',
        'provider_responded_at' => 'datetime',
    ];

    /**
     * Relationships
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(McuBooking::class, 'booking_id');
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(McuPackage::class, 'package_id');
    }

    public function provider(): BelongsTo
    {
        return $this->belongsTo(McuProvider::class, 'provider_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function moderatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'moderated_by');
    }

    public function votes(): HasMany
    {
        return $this->hasMany(ReviewVote::class);
    }

    /**
     * Get average of detailed ratings
     */
    public function getDetailedRatingAverageAttribute(): ?float
    {
        $ratings = array_filter([
            $this->service_rating,
            $this->cleanliness_rating,
            $this->staff_rating,
            $this->value_rating,
        ]);

        if (empty($ratings)) {
            return null;
        }

        return round(array_sum($ratings) / count($ratings), 1);
    }

    /**
     * Check if user found this review helpful
     */
    public function isHelpfulFor(?User $user): ?bool
    {
        if (!$user) {
            return null;
        }

        $vote = $this->votes()->where('user_id', $user->id)->first();
        return $vote ? $vote->is_helpful : null;
    }

    /**
     * Get helpfulness percentage
     */
    public function getHelpfulnessPercentageAttribute(): int
    {
        $totalVotes = $this->helpful_count + $this->not_helpful_count;

        if ($totalVotes === 0) {
            return 0;
        }

        return (int) round(($this->helpful_count / $totalVotes) * 100);
    }

    /**
     * Scope for approved reviews
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope for pending reviews
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for verified reviews
     */
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    /**
     * Scope for reviews with responses
     */
    public function scopeWithResponse($query)
    {
        return $query->whereNotNull('provider_response');
    }

    /**
     * Scope for reviews by rating
     */
    public function scopeByRating($query, int $rating)
    {
        return $query->where('rating', $rating);
    }

    /**
     * Approve the review
     */
    public function approve(?User $moderator = null): bool
    {
        $this->status = 'approved';
        $this->moderated_by = $moderator?->id;
        $this->moderated_at = now();

        return $this->save();
    }

    /**
     * Reject the review
     */
    public function reject(string $reason, ?User $moderator = null): bool
    {
        $this->status = 'rejected';
        $this->moderation_notes = $reason;
        $this->moderated_by = $moderator?->id;
        $this->moderated_at = now();

        return $this->save();
    }

    /**
     * Add provider response
     */
    public function addProviderResponse(string $response): bool
    {
        $this->provider_response = $response;
        $this->provider_responded_at = now();

        return $this->save();
    }

    /**
     * Mark as helpful by user
     */
    public function markHelpful(User $user): void
    {
        $vote = ReviewVote::updateOrCreate(
            ['review_id' => $this->id, 'user_id' => $user->id],
            ['is_helpful' => true]
        );

        // Recalculate counts
        $this->recalculateHelpfulness();
    }

    /**
     * Mark as not helpful by user
     */
    public function markNotHelpful(User $user): void
    {
        $vote = ReviewVote::updateOrCreate(
            ['review_id' => $this->id, 'user_id' => $user->id],
            ['is_helpful' => false]
        );

        // Recalculate counts
        $this->recalculateHelpfulness();
    }

    /**
     * Recalculate helpfulness counts
     */
    public function recalculateHelpfulness(): void
    {
        $this->helpful_count = $this->votes()->where('is_helpful', true)->count();
        $this->not_helpful_count = $this->votes()->where('is_helpful', false)->count();
        $this->save();
    }

    /**
     * Get star rating as array for display
     */
    public function getStarsAttribute(): array
    {
        return [
            'full' => floor($this->rating),
            'half' => ($this->rating - floor($this->rating)) >= 0.5 ? 1 : 0,
            'empty' => 5 - ceil($this->rating),
        ];
    }
}
