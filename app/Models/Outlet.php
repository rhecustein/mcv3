<?php

namespace App\Models;

use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Outlet extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'admin_id',
        'name',
        'code',
        'phone',
        'email',
        'address',
        'city',
        'province',
        'postal_code',
        'is_active',
        'timezone',
        'latitude',
        'longitude',
        'region_name',
        'user_id', // Relasi ke user (akun login outlet)
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Relasi: Outlet dimiliki oleh satu admin
     */
    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    public function results()
    {
        return $this->hasMany(Result::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function doctors()
    {
        return $this->hasMany(Doctor::class);
    }

    /**
     * PDF generation queue for this outlet
     */
    public function documentQueues()
    {
        return $this->hasMany(DocumentQueue::class);
    }

    /**
     * Patients registered at this outlet
     */
    public function patients()
    {
        return $this->hasMany(Patient::class);
    }

    /**
     * Medical diagnoses created at this outlet
     */
    public function diagnoses()
    {
        return $this->hasMany(MedicalDiagnosis::class);
    }
}
