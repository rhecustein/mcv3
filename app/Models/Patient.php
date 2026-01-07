<?php

namespace App\Models;

use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Patient extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'user_id',
        'company_id',
        'nik',
        'gender',
        'birth_date',
        'birth_place',
        'blood_type',
        'phone',
        'address',
        'emergency_contact_name',
        'marital_status',
        'job',
        'company_name',
        'religion',
        'medical_notes',
        'outlet_id',
        'full_name',
        'identity', // Nomor identitas lain (KTP, SIM, dsb.)
    ];

    // Jika memiliki relasi:
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }

    //results
    public function results()
    {
        return $this->hasMany(Result::class);
    }

    /**
     * Feedback from company about patient
     */
    public function feedbacks()
    {
        return $this->hasMany(CompanyPatientFeedback::class);
    }

    /**
     * Many-to-many relationship with companies via pivot table
     */
    public function companies()
    {
        return $this->belongsToMany(Company::class, 'patient_company_relations')
            ->withTimestamps();
    }

    /**
     * Medical diagnoses for this patient
     */
    public function diagnoses()
    {
        return $this->hasMany(MedicalDiagnosis::class);
    }

    /**
     * MCU (Medical Check Up) bookings
     */
    public function mcuBookings()
    {
        return $this->hasMany(McuBooking::class);
    }
}
