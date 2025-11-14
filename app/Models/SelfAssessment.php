<?php

namespace App\Models;

use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class SelfAssessment extends Model
{
    use BelongsToTenant, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'user_id',
        'template_id',
        'assessment_number',
        'started_at',
        'completed_at',
        'status',
        'responses',
        'total_score',
        'percentage_score',
        'severity_level',
        'interpretation',
        'recommendations',
        'requires_professional_help',
        'is_high_risk',
        'psychologist_notified',
        'referred_to_psychologist',
        'follow_up_session_id',
        'is_anonymous',
        'shared_with_psychologist',
        'shared_with_employer',
        'previous_assessment_id',
        'score_change',
        'trend',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'responses' => 'array',
        'percentage_score' => 'decimal:2',
        'recommendations' => 'array',
        'requires_professional_help' => 'boolean',
        'is_high_risk' => 'boolean',
        'psychologist_notified' => 'boolean',
        'is_anonymous' => 'boolean',
        'shared_with_psychologist' => 'boolean',
        'shared_with_employer' => 'boolean',
    ];

    /**
     * Relationships
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(AssessmentTemplate::class, 'template_id');
    }

    public function psychologist(): BelongsTo
    {
        return $this->belongsTo(Psychologist::class, 'referred_to_psychologist');
    }

    public function followUpSession(): BelongsTo
    {
        return $this->belongsTo(PsychologySession::class, 'follow_up_session_id');
    }

    public function previousAssessment(): BelongsTo
    {
        return $this->belongsTo(SelfAssessment::class, 'previous_assessment_id');
    }

    /**
     * Generate unique assessment number
     */
    public static function generateAssessmentNumber(): string
    {
        return 'ASM-' . now()->format('Ymd') . '-' . strtoupper(substr(md5(uniqid()), 0, 6));
    }

    /**
     * Complete assessment
     */
    public function complete(array $responses): void
    {
        $this->responses = $responses;
        $this->completed_at = now();
        $this->status = 'completed';

        // Calculate score based on template rules
        $this->calculateScore();

        // Determine severity and interpretation
        $this->interpretResults();

        $this->save();

        // Check for high-risk situations
        if ($this->is_high_risk) {
            $this->createCrisisAlert();
        }
    }

    /**
     * Calculate score
     */
    protected function calculateScore(): void
    {
        $template = $this->template;
        $totalScore = 0;

        foreach ($this->responses as $response) {
            $totalScore += $response['score'] ?? 0;
        }

        $this->total_score = $totalScore;
        $this->percentage_score = ($totalScore / $template->max_score) * 100;
    }

    /**
     * Interpret results
     */
    protected function interpretResults(): void
    {
        $template = $this->template;
        $interpretations = $template->interpretation_ranges;

        foreach ($interpretations as $range) {
            if ($this->total_score >= $range['min'] && $this->total_score <= $range['max']) {
                $this->severity_level = $range['level'];
                $this->interpretation = $range['description'];
                break;
            }
        }

        // Determine if professional help is needed
        $this->requires_professional_help = in_array($this->severity_level, [
            'moderate',
            'moderately_severe',
            'severe'
        ]);

        // Check for high-risk indicators (e.g., suicidal thoughts in PHQ-9)
        $this->checkHighRisk();
    }

    /**
     * Check for high-risk indicators
     */
    protected function checkHighRisk(): void
    {
        // Check if any response indicates high risk
        // For example, in PHQ-9, question 9 about self-harm
        foreach ($this->responses as $index => $response) {
            if ($this->template->code === 'phq9' && $index === 8 && ($response['score'] ?? 0) >= 2) {
                $this->is_high_risk = true;
                break;
            }
        }
    }

    /**
     * Create crisis alert
     */
    protected function createCrisisAlert(): void
    {
        CrisisAlert::create([
            'tenant_id' => $this->tenant_id,
            'user_id' => $this->user_id,
            'assessment_id' => $this->id,
            'alert_type' => 'suicide_risk',
            'severity' => 'high',
            'description' => 'High-risk assessment detected',
            'indicators' => $this->responses,
        ]);

        $this->psychologist_notified = true;
    }

    /**
     * Scopes
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeHighRisk($query)
    {
        return $query->where('is_high_risk', true);
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }
}

class AssessmentTemplate extends Model
{
    protected $fillable = [
        'name',
        'code',
        'description',
        'category',
        'questions',
        'scoring_rules',
        'interpretation_ranges',
        'total_questions',
        'max_score',
        'is_active',
        'recommended_frequency_days',
    ];

    protected $casts = [
        'questions' => 'array',
        'scoring_rules' => 'array',
        'interpretation_ranges' => 'array',
        'is_active' => 'boolean',
    ];

    public function assessments(): HasMany
    {
        return $this->hasMany(SelfAssessment::class, 'template_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }
}

class CrisisAlert extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'user_id',
        'assessment_id',
        'alert_type',
        'severity',
        'description',
        'indicators',
        'status',
        'assigned_to_psychologist',
        'acknowledged_at',
        'contacted_at',
        'resolved_at',
        'actions_taken',
        'outcome_notes',
        'emergency_session_id',
        'escalated_to_emergency',
        'escalation_notes',
    ];

    protected $casts = [
        'indicators' => 'array',
        'acknowledged_at' => 'datetime',
        'contacted_at' => 'datetime',
        'resolved_at' => 'datetime',
        'escalated_to_emergency' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(SelfAssessment::class);
    }

    public function psychologist(): BelongsTo
    {
        return $this->belongsTo(Psychologist::class, 'assigned_to_psychologist');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeHighSeverity($query)
    {
        return $query->whereIn('severity', ['high', 'critical']);
    }
}
