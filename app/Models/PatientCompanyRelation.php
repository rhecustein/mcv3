<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Pivot table for Patient-Company many-to-many relationship
 */
class PatientCompanyRelation extends Model
{
    use HasFactory;

    /**
     * The table associated with the model
     */
    protected $table = 'patient_company_relations';

    /**
     * Mass assignable attributes
     */
    protected $fillable = [
        'patient_id',
        'company_id',
        'employee_id',
        'position',
        'department',
        'join_date',
        'status',
        'notes',
    ];

    /**
     * Attribute casting
     */
    protected $casts = [
        'join_date' => 'date',
    ];

    /**
     * Patient in this relationship
     */
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Company in this relationship
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Scope: Only active relationships
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope: Filter by company
     */
    public function scopeForCompany($query, int $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    /**
     * Scope: Filter by patient
     */
    public function scopeForPatient($query, int $patientId)
    {
        return $query->where('patient_id', $patientId);
    }
}
