<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Package extends Model
{
    use HasFactory;

    /**
     * Mass assignable attributes
     */
    protected $fillable = [
        'name',
        'description',
        'price',
        'duration_days',
        'sessions_included',
        'features',
        'is_active',
        'type',
    ];

    /**
     * Attribute casting
     */
    protected $casts = [
        'price' => 'decimal:2',
        'duration_days' => 'integer',
        'sessions_included' => 'integer',
        'features' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Companies using this package
     */
    public function companies()
    {
        return $this->hasMany(Company::class);
    }

    /**
     * Psychology subscriptions using this package
     */
    public function psychologySubscriptions()
    {
        return $this->hasMany(PsychologySubscription::class);
    }

    /**
     * Package transactions
     */
    public function transactions()
    {
        return $this->hasMany(PackageTransaction::class);
    }

    /**
     * Scope: Only active packages
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
