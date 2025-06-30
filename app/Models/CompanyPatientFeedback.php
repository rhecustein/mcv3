<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CompanyPatientFeedback extends Model
{
    use HasFactory;

    /**
     * Mass assignable attributes.
     */
    protected $fillable = [
        'company_id',
        'patient_id',
        'feedback',
        'type',
        'rating',
        'is_followed_up',
        'followed_up_at',
        'followed_up_by',
    ];

    /**
     * Cast attributes to correct types.
     */
    protected $casts = [
        'is_followed_up' => 'boolean',
        'followed_up_at' => 'datetime',
        'rating' => 'integer',
    ];

    /**
     * Relasi: Feedback dari pasien perusahaan
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Relasi: User perusahaan yang follow-up (optional)
     */
    public function followedUpBy()
    {
        return $this->belongsTo(User::class, 'followed_up_by');
    }

    /**
     * Scope: hanya feedback yang belum ditindaklanjuti
     */
    public function scopePending($query)
    {
        return $query->where('is_followed_up', false);
    }

    /**
     * Scope: filter berdasarkan tipe feedback
     */
    public function scopeType($query, string $type)
    {
        return $query->where('type', $type);
    }
}
