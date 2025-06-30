<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * Mass assignable attributes.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_type',
        'phone',
        'avatar',
        'last_ip',
        'last_login_at',
        'otp_code',
        'otp_code_expired_at',
        'email_verified_at',
    ];

    /**
     * Hidden attributes for arrays.
     */
    protected $hidden = [
        'password',
        'remember_token',
        'otp_code',
    ];

    /**
     * Attribute casting.
     */
    protected $casts = [
        'email_verified_at'     => 'datetime',
        'last_login_at'         => 'datetime',
        'otp_code_expired_at'   => 'datetime',
    ];

    /**
     * ============================
     * Relationships by Role Type
     * ============================
     */
    public function admin()
    {
        return $this->hasOne(Admin::class);
    }

    public function outlet()
    {
        return $this->hasOne(Outlet::class, 'user_id');
    }

    public function doctor()
    {
        return $this->hasOne(Doctor::class);
    }

    public function patient()
    {
        return $this->hasOne(Patient::class);
    }

    public function companyAdmins()
    {
        return $this->hasMany(User::class)->where('role_type', 'companies');
    }

    public function sessionLogins()
    {
        return $this->hasMany(SessionLogin::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function getOutletAttribute()
    {
        return $this->admin->outlet ?? null;
    }
    /**
     * =======================
     * Custom Role-Type Helper
     * =======================
     */
    public function hasRoleType(string|array $role): bool
    {
        return is_array($role)
            ? in_array($this->role_type, $role)
            : $this->role_type === $role;
    }
}
