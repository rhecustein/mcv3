<?php

namespace App\Models;

use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CorporateHealthReport extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'company_id',
        'report_number',
        'report_title',
        'report_type',
        'period_start',
        'period_end',
        'total_employees',
        'employees_examined',
        'fit_count',
        'fit_with_notes_count',
        'unfit_count',
        'pending_count',
        'common_findings',
        'risk_categories',
        'total_cost',
        'average_cost_per_employee',
        'pdf_file',
        'excel_file',
        'status',
        'published_at',
        'generated_by',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'common_findings' => 'array',
        'risk_categories' => 'array',
        'total_cost' => 'decimal:2',
        'average_cost_per_employee' => 'decimal:2',
        'published_at' => 'datetime',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function generatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'generated_by');
    }

    /**
     * Generate unique report number
     */
    public static function generateReportNumber(): string
    {
        $prefix = 'RPT';
        $date = now()->format('Ymd');
        $random = strtoupper(substr(md5(uniqid()), 0, 6));

        return "{$prefix}-{$date}-{$random}";
    }

    /**
     * Get examination completion rate
     */
    public function getCompletionRateAttribute(): float
    {
        if ($this->total_employees == 0) {
            return 0;
        }

        return ($this->employees_examined / $this->total_employees) * 100;
    }

    /**
     * Get fitness rate
     */
    public function getFitnessRateAttribute(): float
    {
        if ($this->employees_examined == 0) {
            return 0;
        }

        return ($this->fit_count / $this->employees_examined) * 100;
    }

    /**
     * Scope for published reports
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }
}
