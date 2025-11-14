<?php

namespace App\Models;

use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MoodTracker extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'user_id',
        'tracking_date',
        'tracking_time',
        'mood',
        'mood_score',
        'emotions',
        'energy_level',
        'stress_level',
        'anxiety_level',
        'activities',
        'triggers',
        'sleep_hours',
        'sleep_quality',
        'notes',
        'gratitude',
        'has_physical_symptoms',
        'physical_symptoms',
        'time_of_day',
        'location_type',
        'streak_days',
    ];

    protected $casts = [
        'tracking_date' => 'date',
        'emotions' => 'array',
        'activities' => 'array',
        'triggers' => 'array',
        'physical_symptoms' => 'array',
        'has_physical_symptoms' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get mood score from mood name
     */
    public static function getMoodScore(string $mood): int
    {
        return match($mood) {
            'very_bad' => 1,
            'bad' => 2,
            'neutral' => 3,
            'good' => 4,
            'very_good' => 5,
            default => 3,
        };
    }

    /**
     * Scopes
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForDate($query, $date)
    {
        return $query->where('tracking_date', $date);
    }

    public function scopeForDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('tracking_date', [$startDate, $endDate]);
    }
}
