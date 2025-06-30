<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DocumentQueue extends Model
{
    use HasFactory;

    protected $table = 'document_queues';

    protected $fillable = [
        'result_id',
        'status',
        'generated_file',
        'error_log',
        'retry_count',
        'triggered_by',
        'processed_at',
        'completed_at',

        // Snapshot fields
        'no_letters',
        'patient_name',
        'type_surat',
        'outlet_name',
        'start_date',
        'expired_date',
    ];

    protected $casts = [
        'processed_at'   => 'datetime',
        'completed_at'   => 'datetime',
        'start_date'     => 'date',
        'expired_date'   => 'date',
    ];

    // 🔗 Relasi ke Result (untuk akses dinamis, jika diperlukan)
    public function result()
    {
        return $this->belongsTo(Result::class)->with(['patient', 'doctor']);
    }

    // ✅ Helper status
    public function isPending()    { return $this->status === 'pending'; }
    public function isProcessing() { return $this->status === 'processing'; }
    public function isSuccess()    { return $this->status === 'success'; }
    public function isFailed()     { return $this->status === 'failed'; }

    // 🔁 Relasi turunan melalui Result (opsional & aman null)
    public function patient()
    {
        return optional($this->result)->patient;
    }

    public function doctor()
    {
        return optional($this->result)->doctor;
    }

    public function outlet()
    {
        return optional($this->result)->outlet;
    }
}
