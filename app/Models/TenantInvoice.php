<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TenantInvoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'subscription_id',
        'invoice_number',
        'invoice_date',
        'due_date',
        'period_start',
        'period_end',
        'subtotal',
        'tax_amount',
        'discount_amount',
        'total_amount',
        'currency',
        'line_items',
        'status',
        'paid_amount',
        'paid_at',
        'payment_method',
        'payment_reference',
        'pdf_path',
        'notes',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'due_date' => 'date',
        'period_start' => 'date',
        'period_end' => 'date',
        'paid_at' => 'datetime',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'line_items' => 'array',
    ];

    /**
     * Relationships
     */
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function subscription()
    {
        return $this->belongsTo(TenantSubscription::class);
    }

    /**
     * Status Checks
     */
    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    public function isOverdue(): bool
    {
        return $this->status === 'sent' && $this->due_date->isPast();
    }

    public function getRemainingAmount(): float
    {
        return max(0, $this->total_amount - $this->paid_amount);
    }

    /**
     * Scopes
     */
    public function scopeUnpaid($query)
    {
        return $query->whereIn('status', ['draft', 'sent']);
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 'sent')
            ->where('due_date', '<', now());
    }

    /**
     * Helpers
     */
    public static function generateInvoiceNumber(): string
    {
        $year = now()->year;
        $month = now()->format('m');
        $lastInvoice = static::whereYear('created_at', $year)
            ->whereMonth('created_at', now()->month)
            ->orderBy('id', 'desc')
            ->first();

        $sequence = $lastInvoice ? ((int) substr($lastInvoice->invoice_number, -4)) + 1 : 1;

        return sprintf('INV-%s-%s-%04d', $year, $month, $sequence);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($invoice) {
            if (!$invoice->invoice_number) {
                $invoice->invoice_number = static::generateInvoiceNumber();
            }
        });
    }
}
