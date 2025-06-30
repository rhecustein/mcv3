<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PackageTransaction extends Model
{
    protected $fillable = [
        'company_id',
        'outlet_id',
        'package_id',
        'created_by',
        'transaction_code',
        'status',
        'payment_method',
        'payment_reference',
        'renewal_type',
        'amount',
        'paid_at',
        'start_date',
        'end_date',
        'invoice_number',
        'metadata',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
        'start_date' => 'date',
        'end_date' => 'date',
        'metadata' => 'array',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function outlet(): BelongsTo
    {
        return $this->belongsTo(Outlet::class);
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
