<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SessionLogin extends Model
{
    protected $table = 'session_logins';

    protected $fillable = [
        'user_id',
        'session_id',
        'ip_address',
        'user_agent',
        'device',
        'city',
        'province',
        'latitude',
        'longitude',
        'success',
        'is_active',
        'logged_in_at',
        'last_activity_at',
        'logged_out_at',
    ];

    protected $casts = [
        'success' => 'boolean',
        'is_active' => 'boolean',
        'logged_in_at' => 'datetime',
        'last_activity_at' => 'datetime',
        'logged_out_at' => 'datetime',
        'latitude' => 'float',
        'longitude' => 'float',
    ];

    /**
     * Relasi ke user.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
