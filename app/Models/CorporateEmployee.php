<?php

namespace App\Models;

use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CorporateEmployee extends Model
{
    use BelongsToTenant, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'company_id',
        'patient_id',
        'employee_id',
        'full_name',
        'email',
        'phone',
        'gender',
        'birth_date',
        'nik',
        'department',
        'position',
        'join_date',
        'employment_status',
        'health_insurance_number',
        'last_mcu_date',
        'next_mcu_due',
        'health_status',
        'emergency_contact',
        'medical_history',
        'notes',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'join_date' => 'date',
        'last_mcu_date' => 'date',
        'next_mcu_due' => 'date',
        'emergency_contact' => 'array',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Check if employee MCU is overdue
     */
    public function isMcuOverdue(): bool
    {
        return $this->next_mcu_due && now()->isAfter($this->next_mcu_due);
    }

    /**
     * Get days until next MCU
     */
    public function daysUntilNextMcu(): ?int
    {
        if (!$this->next_mcu_due) {
            return null;
        }

        return now()->diffInDays($this->next_mcu_due, false);
    }

    /**
     * Scope for active employees
     */
    public function scopeActive($query)
    {
        return $query->where('employment_status', 'active');
    }

    /**
     * Scope for overdue MCU
     */
    public function scopeMcuOverdue($query)
    {
        return $query->where('next_mcu_due', '<', now())
            ->where('employment_status', 'active');
    }
}
