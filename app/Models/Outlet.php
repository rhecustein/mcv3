<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Outlet extends Model
{
    use HasFactory;

    protected $fillable = [
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
}
