<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CompanyHealthReport extends Model
{
    use HasFactory;

    /**
     * Mass assignable attributes.
     */
    protected $fillable = [
        'company_id',
        'report_date',
        'report_period',
        'total_patients',
        'total_appointments',
        'total_sick_letters',
        'total_health_letters',
        'top_diagnosis',
        'top_diagnosis_count',
        'report_file',
        'data_json',
    ];

    /**
     * Cast attributes.
     */
    protected $casts = [
        'report_date' => 'date',
        'data_json' => 'array',
    ];

    /**
     * Relasi ke perusahaan
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Scope: laporan berdasarkan bulan atau periode
     */
    public function scopeOfPeriod($query, string $period)
    {
        return $query->where('report_period', $period);
    }

    /**
     * Scope: laporan terbaru
     */
    public function scopeLatestReport($query)
    {
        return $query->orderByDesc('report_date');
    }
}
