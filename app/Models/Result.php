<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Result extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'outlet_id',
        'patient_id',
        'doctor_id',
        'template_result_id',
        'medical_diagnosis_id',
        'unique_code',
        'qrcode',
        'no_letters',
        'document_name',
        'type',
        'sign_type',
        'sign_value',
        'verification_date',
        'print_date',
        'start_date',
        'end_date',
        'date',
        'time',
        'duration',
        'send_notif_wa',
        'send_notif_email',
        'notif_wa',
        'notif_email',
        'company_id',
        'admin_id',
        'status',
        'created_by',
        'updated_by',
        'deleted_by',
        'edit',
        'created_ip',
        'created_city',
        'created_latitude',
        'created_longitude',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'date' => 'date',
        'verification_date' => 'datetime',
        'print_date' => 'datetime',
        'send_notif_wa' => 'boolean',
        'send_notif_email' => 'boolean',
        'notif_wa' => 'boolean',
        'notif_email' => 'boolean',
        'created_latitude' => 'decimal:8',
        'created_longitude' => 'decimal:8',
    ];

    /** RELATIONS **/

    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function template()
    {
        return $this->belongsTo(TemplateResult::class, 'template_result_id');
    }

    /**
     * Primary diagnosis relationship
     */
    public function diagnosis()
    {
        return $this->belongsTo(MedicalDiagnosis::class, 'medical_diagnosis_id');
    }

    /**
     * Alternative diagnosis relationship (in case the table/model name is different)
     */
    public function medicalDiagnosis()
    {
        return $this->belongsTo(MedicalDiagnosis::class, 'medical_diagnosis_id');
    }

    /**
     * Alternative diagnosis relationship for ICD codes
     */
    public function icdDiagnosis()
    {
        return $this->belongsTo(IcdMaster::class, 'medical_diagnosis_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    /** SCOPES **/

    /**
     * Scope untuk filter berdasarkan outlet
     */
    public function scopeForOutlet($query, $outletId)
    {
        return $query->where('outlet_id', $outletId);
    }

    /**
     * Scope untuk filter berdasarkan dokter
     */
    public function scopeForDoctor($query, $doctorId)
    {
        return $query->where('doctor_id', $doctorId);
    }

    /**
     * Scope untuk filter berdasarkan perusahaan
     */
    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    /**
     * Scope untuk filter berdasarkan tipe surat
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope untuk filter berdasarkan periode
     */
    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Scope untuk surat bulan ini
     */
    public function scopeThisMonth($query)
    {
        return $query->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year);
    }

    /**
     * Scope untuk surat hari ini
     */
    public function scopeToday($query)
    {
        return $query->whereDate('created_at', now()->toDateString());
    }

    /** ACCESSORS & MUTATORS **/

    /**
     * Get diagnosis name with fallback
     */
    public function getDiagnosisNameAttribute()
    {
        // Try multiple approaches to get diagnosis name
        if ($this->diagnosis) {
            return $this->diagnosis->name ?? 
                   $this->diagnosis->diagnosis_name ?? 
                   $this->diagnosis->title ?? 
                   $this->diagnosis->description ?? 
                   'Diagnosis tersedia';
        }

        if ($this->medicalDiagnosis) {
            return $this->medicalDiagnosis->name ?? 
                   $this->medicalDiagnosis->diagnosis_name ?? 
                   $this->medicalDiagnosis->title ?? 
                   'Diagnosis tersedia';
        }

        if ($this->icdDiagnosis) {
            return $this->icdDiagnosis->name ?? 
                   $this->icdDiagnosis->diagnosis_name ?? 
                   $this->icdDiagnosis->title ?? 
                   'ICD Diagnosis';
        }

        return 'Tidak ada diagnosis';
    }

    /**
     * Get type in uppercase
     */
    public function getTypeUpperAttribute()
    {
        return strtoupper($this->type ?? '');
    }

    /**
     * Get formatted created date
     */
    public function getFormattedCreatedAtAttribute()
    {
        return $this->created_at ? $this->created_at->format('d M Y H:i') : '-';
    }

    /**
     * Get notification status as text
     */
    public function getNotifWaTextAttribute()
    {
        return $this->notif_wa ? 'Ya' : 'Tidak';
    }

    public function getNotifEmailTextAttribute()
    {
        return $this->notif_email ? 'Ya' : 'Tidak';
    }

    /** HELPER METHODS **/

    /**
     * Check if result has diagnosis
     */
    public function hasDiagnosis()
    {
        return !empty($this->medical_diagnosis_id) || 
               !empty($this->diagnosis_text) || 
               !empty($this->medical_diagnosis);
    }

    /**
     * Get all possible diagnosis text
     */
    public function getAllDiagnosisText()
    {
        $diagnosisTexts = [];

        // From relations
        if ($this->diagnosis) {
            $diagnosisTexts[] = $this->diagnosis->name ?? $this->diagnosis->diagnosis_name;
        }

        // From direct fields (if any)
        $directFields = ['diagnosis_text', 'medical_diagnosis', 'diagnosis_name'];
        foreach ($directFields as $field) {
            if (isset($this->attributes[$field]) && !empty($this->attributes[$field])) {
                $diagnosisTexts[] = $this->attributes[$field];
            }
        }

        return array_filter($diagnosisTexts);
    }

    /**
     * Get best available diagnosis
     */
    public function getBestDiagnosis()
    {
        $allTexts = $this->getAllDiagnosisText();
        return !empty($allTexts) ? $allTexts[0] : 'Tidak ada diagnosis';
    }

    /**
     * Check if result is completed (has document)
     */
    public function isCompleted()
    {
        return !empty($this->document_name);
    }

    /**
     * Check if result is draft
     */
    public function isDraft()
    {
        return empty($this->document_name);
    }

    /**
     * Get status text
     */
    public function getStatusText()
    {
        return $this->isCompleted() ? 'Selesai' : 'Draft';
    }

    /**
     * Get duration text with proper handling for MC and SKB
     */
    public function getDurationText()
    {
        // For MC (Medical Certificate), duration should always be present
        if (strtolower($this->type) === 'mc') {
            if (!empty($this->duration)) {
                return $this->duration . ' hari';
            }
            
            // If duration is null but we have start_date and end_date, calculate it
            if ($this->start_date && $this->end_date) {
                $start = \Carbon\Carbon::parse($this->start_date);
                $end = \Carbon\Carbon::parse($this->end_date);
                $calculatedDuration = $start->diffInDays($end) + 1; // +1 to include both start and end date
                return $calculatedDuration . ' hari';
            }
            
            // Default for MC if no duration data available
            return '1 hari';
        }
        
        // For SKB (Surat Keterangan Sehat), duration might not be applicable
        if (strtolower($this->type) === 'skb') {
            return '-'; // SKB typically doesn't have duration
        }
        
        // For other types, return duration if available
        if (!empty($this->duration)) {
            return $this->duration . ' hari';
        }

        return '-';
    }

    /**
     * Get raw duration value with calculation fallback
     */
    public function getDurationValueAttribute()
    {
        // Return existing duration if available
        if (!empty($this->duration)) {
            return $this->duration;
        }
        
        // Calculate from dates if MC type and dates are available
        if (strtolower($this->type) === 'mc' && $this->start_date && $this->end_date) {
            $start = \Carbon\Carbon::parse($this->start_date);
            $end = \Carbon\Carbon::parse($this->end_date);
            return $start->diffInDays($end) + 1;
        }
        
        return null;
    }

    /** BOOT METHOD **/

    protected static function boot()
    {
        parent::boot();

        // Auto-generate unique code if not provided
        static::creating(function ($result) {
            if (empty($result->unique_code)) {
                $result->unique_code = $result->generateUniqueCode();
            }
        });
    }

    /**
     * Generate unique code for result
     */
    public function generateUniqueCode()
    {
        $prefix = strtoupper($this->type ?? 'DOC');
        $timestamp = now()->format('YmdHis');
        $random = strtoupper(substr(md5(uniqid()), 0, 4));
        
        return "{$prefix}-{$timestamp}-{$random}";
    }
}