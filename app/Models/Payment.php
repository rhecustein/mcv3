<?php

namespace App\Models;

use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Payment extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'payment_number',
        'payable_type',
        'payable_id',
        'amount',
        'currency',
        'payment_method',
        'payment_gateway',
        'gateway_transaction_id',
        'gateway_payment_url',
        'gateway_response',
        'status',
        'paid_at',
        'expired_at',
        'refunded_at',
        'notes',
        'receipt_url',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'gateway_response' => 'array',
        'paid_at' => 'datetime',
        'expired_at' => 'datetime',
        'refunded_at' => 'datetime',
    ];

    /**
     * Polymorphic relationship to payable
     */
    public function payable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Generate unique payment number
     */
    public static function generatePaymentNumber(): string
    {
        $prefix = 'PAY';
        $date = now()->format('Ymd');
        $random = strtoupper(substr(md5(uniqid()), 0, 8));

        return "{$prefix}-{$date}-{$random}";
    }

    /**
     * Check if payment is pending
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if payment is successful
     */
    public function isSuccess(): bool
    {
        return $this->status === 'success';
    }

    /**
     * Check if payment has failed
     */
    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    /**
     * Check if payment is expired
     */
    public function isExpired(): bool
    {
        return $this->status === 'expired' ||
               ($this->expired_at && now()->isAfter($this->expired_at));
    }

    /**
     * Mark payment as success
     */
    public function markAsSuccess(array $gatewayResponse = []): void
    {
        $this->update([
            'status' => 'success',
            'paid_at' => now(),
            'gateway_response' => $gatewayResponse,
        ]);
    }

    /**
     * Mark payment as failed
     */
    public function markAsFailed(array $gatewayResponse = []): void
    {
        $this->update([
            'status' => 'failed',
            'gateway_response' => $gatewayResponse,
        ]);
    }

    /**
     * Refund payment
     */
    public function refund(): void
    {
        $this->update([
            'status' => 'refunded',
            'refunded_at' => now(),
        ]);
    }

    /**
     * Scope for successful payments
     */
    public function scopeSuccess($query)
    {
        return $query->where('status', 'success');
    }

    /**
     * Scope for pending payments
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}
