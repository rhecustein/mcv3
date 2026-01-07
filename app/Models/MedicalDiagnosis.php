<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MedicalDiagnosis extends Model
{
    protected $fillable = [
        'outlet_id',
        'patient_id',
        'doctor_id',
        'icd_master_id',
        'diagnosis_name',
        'description',
        'diagnosed_at',
    ];

    // =====================
    // Relasi
    // =====================

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }

    public function icd()
    {
        return $this->belongsTo(IcdMaster::class, 'icd_master_id');
    }

    /**
     * Alias for icd relationship for consistency
     */
    public function icdMaster()
    {
        return $this->icd();
    }

    /**
     * Results using this diagnosis
     */
    public function results()
    {
        return $this->hasMany(Result::class, 'medical_diagnosis_id');
    }

    /**
     * Scope: Filter by patient
     */
    public function scopeForPatient($query, int $patientId)
    {
        return $query->where('patient_id', $patientId);
    }

    /**
     * Scope: Filter by doctor
     */
    public function scopeForDoctor($query, int $doctorId)
    {
        return $query->where('doctor_id', $doctorId);
    }

    /**
     * Scope: Recent diagnoses
     */
    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('diagnosed_at', '>=', now()->subDays($days));
    }
}
