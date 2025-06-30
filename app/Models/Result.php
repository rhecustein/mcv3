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
        'medical_diagnosis_id',
        'created_by',
        'updated_by',
        'deleted_by',
        'edit',
        'created_ip',
        'created_city',
        'created_latitude',
        'created_longitude',
        
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

    public function diagnosis()
    {
        return $this->belongsTo(MedicalDiagnosis::class, 'medical_diagnosis_id');
    }
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    //patient
    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
