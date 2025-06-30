<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Admin extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'region_name',
        'province',
        'position_title',
        'contact_number',
    ];

    /**
     * Relasi ke model User (akun login)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi virtual berdasarkan region_name
    public function usersInRegion()
    {
        return $this->hasManyThrough(User::class, Patient::class, 'region', 'id', 'region_name', 'user_id');
    }

    public function outletsInRegion()
    {
        return $this->hasMany(Outlet::class, 'region_name', 'region_name');
    }

    public function doctorsInRegion()
    {
        return $this->hasMany(Doctor::class, 'region_name', 'region_name');
    }

    public function lettersInRegion()
    {
        return $this->hasMany(Result::class, 'region_name', 'region_name');
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    public function outlets()
    {
        return $this->hasMany(Outlet::class);
    }
    public function doctors()
    {
        return $this->hasMany(Doctor::class);
    }

    public function patients()
    {
        return $this->hasMany(Patient::class);
    }
    

}

//