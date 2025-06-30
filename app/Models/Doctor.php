<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Doctor extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'outlet_id',
        'specialist',
        'specialization',
        'license_number',
        'gender',
        'birth_date',
        'education',
        'practice_days',
        'phone',
        'signature_image',
        'signature_qrcode',
        'signed_at',
        'signature_token',
        'is_signature_verified',
        'admin_id', // Relasi ke admin (pemilik outlet)
        'signature_image',
        'signature_qrcode',
        'signed_at',
        'signature_token',
        'is_signature_verified',
        'signature_image',
        'signature_qrcode',
    ];

    protected $casts = [
        'birth_date' => 'date',
    ];

    /**
     * Relasi ke user (akun login dokter)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke outlet (jika dokter terdaftar di outlet tertentu)
     */
    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }
    /**
     * Relasi ke admin (pemilik outlet)
     */
    //results
    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
    /**
     * Relasi ke hasil pemeriksaan
     */
    public function results()
    {
        return $this->hasMany(Result::class);
    }
}
