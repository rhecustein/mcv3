<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Psychology Note Model
 * Manages psychology session notes and observations
 */
class PsychologyNote extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_id',
        'psychologist_id',
        'patient_id',
        'note_type',
        'content',
        'is_private',
        'attachments',
        'created_by',
    ];

    protected $casts = [
        'is_private' => 'boolean',
        'attachments' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Psychology session this note belongs to
     */
    public function session()
    {
        return $this->belongsTo(PsychologySession::class, 'session_id');
    }

    /**
     * Psychologist who wrote this note
     */
    public function psychologist()
    {
        return $this->belongsTo(User::class, 'psychologist_id');
    }

    /**
     * Patient this note is about
     */
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * User who created this note
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope: Only private notes
     */
    public function scopePrivate($query)
    {
        return $query->where('is_private', true);
    }

    /**
     * Scope: Only public notes
     */
    public function scopePublic($query)
    {
        return $query->where('is_private', false);
    }

    /**
     * Scope: Filter by note type
     */
    public function scopeType($query, string $type)
    {
        return $query->where('note_type', $type);
    }
}
