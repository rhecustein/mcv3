<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class IpLock extends Model
{
    use HasFactory;

    protected $fillable = [
        'ip_address',
        'lock_type',
        'reason',
        'locked_at',
        'unlocked_at',
        'locked_by',
        'is_active',
    ];

    protected $casts = [
        'locked_at' => 'datetime',
        'unlocked_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * Relasi ke user yang mengunci IP
     */
    public function lockedBy()
    {
        return $this->belongsTo(User::class, 'locked_by');
    }

    /**
     * Scope: hanya IP aktif
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: IP yang masih terkunci (permanent atau belum lewat unlocked_at)
     */
    public function scopeStillLocked($query)
    {
        return $query->where(function ($q) {
            $q->where('lock_type', 'permanent')
              ->orWhere(function ($q2) {
                  $q2->where('lock_type', 'temporary')
                     ->where(function ($q3) {
                         $q3->whereNull('unlocked_at')
                            ->orWhere('unlocked_at', '>', now());
                     });
              });
        });
    }

    /**
     * Scope: Filter berdasarkan IP address
     */
    public function scopeForIp($query, string $ip)
    {
        return $query->where('ip_address', $ip);
    }
}
