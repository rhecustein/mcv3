<?php

namespace App\Models;

use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class PsychologySession extends Model
{
    use BelongsToTenant, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'psychologist_id',
        'user_id',
        'subscription_id',
        'session_number',
        'session_type',
        'category',
        'scheduled_at',
        'started_at',
        'ended_at',
        'duration_minutes',
        'actual_duration_minutes',
        'client_concern',
        'urgency_level',
        'is_anonymous',
        'is_emergency',
        'room_id',
        'room_token',
        'join_url',
        'call_metadata',
        'status',
        'price',
        'payment_method',
        'payment_id',
        'is_paid',
        'client_rating',
        'client_feedback',
        'psychologist_rating',
        'cancellation_reason',
        'cancelled_at',
        'cancelled_by',
        'cancelled_by_role',
        'rescheduled_from_id',
        'rescheduled_to_id',
        'reminder_sent_24h',
        'reminder_sent_1h',
        'feedback_requested',
        'metadata',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'price' => 'decimal:2',
        'is_anonymous' => 'boolean',
        'is_emergency' => 'boolean',
        'is_paid' => 'boolean',
        'call_metadata' => 'array',
        'cancelled_at' => 'datetime',
        'reminder_sent_24h' => 'boolean',
        'reminder_sent_1h' => 'boolean',
        'feedback_requested' => 'boolean',
        'metadata' => 'array',
    ];

    /**
     * Relationships
     */
    public function psychologist(): BelongsTo
    {
        return $this->belongsTo(Psychologist::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(PsychologySubscription::class);
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    public function note(): HasOne
    {
        return $this->hasOne(PsychologyNote::class, 'session_id');
    }

    /**
     * Generate unique session number
     */
    public static function generateSessionNumber(): string
    {
        return 'PSY-' . now()->format('Ymd') . '-' . strtoupper(substr(md5(uniqid()), 0, 6));
    }

    /**
     * Check if session is upcoming
     */
    public function isUpcoming(): bool
    {
        return $this->scheduled_at->isFuture() && in_array($this->status, ['scheduled', 'confirmed']);
    }

    /**
     * Check if session is in progress
     */
    public function isInProgress(): bool
    {
        return $this->status === 'in_progress';
    }

    /**
     * Check if session is completed
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Start session
     */
    public function start(): void
    {
        $this->started_at = now();
        $this->status = 'in_progress';
        $this->save();
    }

    /**
     * Complete session
     */
    public function complete(): void
    {
        $this->ended_at = now();
        $this->status = 'completed';
        $this->actual_duration_minutes = $this->started_at->diffInMinutes($this->ended_at);
        $this->save();

        // Update psychologist statistics
        $this->psychologist->updateStatistics();
    }

    /**
     * Cancel session
     */
    public function cancel(string $reason, User $cancelledBy, string $role): void
    {
        $this->status = 'cancelled';
        $this->cancellation_reason = $reason;
        $this->cancelled_at = now();
        $this->cancelled_by = $cancelledBy->id;
        $this->cancelled_by_role = $role;
        $this->save();
    }

    /**
     * Scopes
     */
    public function scopeUpcoming($query)
    {
        return $query->where('scheduled_at', '>', now())
            ->whereIn('status', ['scheduled', 'confirmed']);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('scheduled_at', today());
    }

    public function scopeEmergency($query)
    {
        return $query->where('is_emergency', true);
    }
}
