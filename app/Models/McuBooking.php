<?php

namespace App\Models;

use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class McuBooking extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'package_id',
        'provider_id',
        'patient_id',
        'company_id',
        'booking_number',
        'patient_name',
        'patient_email',
        'patient_phone',
        'patient_birth_date',
        'patient_gender',
        'patient_nik',
        'booking_date',
        'booking_time',
        'status',
        'original_price',
        'final_price',
        'discount_amount',
        'promo_code',
        'payment_status',
        'payment_id',
        'paid_at',
        'special_notes',
        'cancellation_reason',
        'cancelled_at',
        'cancelled_by',
        'result_id',
        'result_uploaded_at',
    ];

    protected $casts = [
        'booking_date' => 'date',
        'booking_time' => 'datetime',
        'patient_birth_date' => 'date',
        'original_price' => 'decimal:2',
        'final_price' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'result_uploaded_at' => 'datetime',
    ];

    /**
     * Package relationship
     */
    public function package(): BelongsTo
    {
        return $this->belongsTo(McuPackage::class, 'package_id');
    }

    /**
     * Provider relationship
     */
    public function provider(): BelongsTo
    {
        return $this->belongsTo(McuProvider::class, 'provider_id');
    }

    /**
     * Patient relationship
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }

    /**
     * Company relationship
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    /**
     * Payment relationship
     */
    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class, 'payment_id');
    }

    /**
     * Result relationship
     */
    public function result(): BelongsTo
    {
        return $this->belongsTo(Result::class, 'result_id');
    }

    /**
     * Review relationship
     */
    public function review(): HasOne
    {
        return $this->hasOne(Review::class, 'booking_id');
    }

    /**
     * Check if booking can be reviewed
     */
    public function canBeReviewed(): bool
    {
        return $this->status === 'completed' && !$this->review;
    }

    /**
     * Check if booking has been reviewed
     */
    public function hasReview(): bool
    {
        return $this->review !== null;
    }

    /**
     * Generate unique booking number
     */
    public static function generateBookingNumber(): string
    {
        $prefix = 'MCU';
        $date = now()->format('Ymd');
        $random = strtoupper(substr(md5(uniqid()), 0, 6));

        return "{$prefix}-{$date}-{$random}";
    }

    /**
     * Check if booking is confirmed
     */
    public function isConfirmed(): bool
    {
        return $this->status === 'confirmed';
    }

    /**
     * Check if booking is completed
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Check if booking is cancelled
     */
    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    /**
     * Check if payment is paid
     */
    public function isPaid(): bool
    {
        return $this->payment_status === 'paid';
    }

    /**
     * Scope for pending bookings
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for confirmed bookings
     */
    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    /**
     * Scope for today's bookings
     */
    public function scopeToday($query)
    {
        return $query->whereDate('booking_date', today());
    }

    /**
     * Scope for upcoming bookings
     */
    public function scopeUpcoming($query)
    {
        return $query->where('booking_date', '>=', today())
            ->whereIn('status', ['confirmed', 'pending']);
    }
}
