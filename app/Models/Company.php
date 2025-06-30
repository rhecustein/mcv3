<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Company extends Model
{
    use HasFactory;

    /**
     * Mass assignable attributes.
     */

    protected $fillable = [
        'name',
        'code',
        'industry_type',
        'registration_number',
        'email',
        'phone',
        'address',
        'city',
        'province',
        'postal_code',
        'is_active',
    ];

    /**
     * Attribute casting.
     */
    protected $casts = [
        'is_active' => 'boolean',
        'package_start_date' => 'date',
        'package_end_date' => 'date',
    ];

    /**
     * Relationship: Company has many CompanyAdmins
     */
    public function companyAdmins()
    {
        return $this->hasMany(User::class)->where('role_type', 'companies');
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }
    /**
     * Relationship: Company has many patients (via relation table)
     */
    public function patients()
    {
        return $this->belongsToMany(Patient::class, 'patient_company_relations');
    }

    /**
     * Relationship: Company has health reports
     */
    public function healthReports()
    {
        return $this->hasMany(CompanyHealthReport::class);
    }

    /**
     * Scope: Only active companies
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    //result
    public function results()
    {
        return $this->hasMany(Result::class);
    }

    public function packageTransactions()
    {
        return $this->hasMany(PackageTransaction::class);
    }
}
