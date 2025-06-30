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
}
