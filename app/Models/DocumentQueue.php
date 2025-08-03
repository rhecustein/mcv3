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

        // Snapshot fields for faster access without joins
        'no_letters',
        'patient_name',
        'type_surat',
        'outlet_name',
        'outlet_id', // Add outlet_id for direct filtering
        'start_date',
        'expired_date',
        'filename',
    ];

    protected $casts = [
        'processed_at'   => 'datetime',
        'completed_at'   => 'datetime',
        'start_date'     => 'date',
        'expired_date'   => 'date',
        'retry_count'    => 'integer',
    ];

    // 🔗 Primary Relations
    public function result()
    {
        return $this->belongsTo(Result::class)->with(['patient', 'doctor', 'outlet']);
    }

    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }

    // 🔁 Relations through Result (with null safety)
    public function patient()
    {
        return $this->hasOneThrough(
            Patient::class,
            Result::class,
            'id', // Foreign key on Result table
            'id', // Foreign key on Patient table
            'result_id', // Local key on DocumentQueue table
            'patient_id' // Local key on Result table
        );
    }

    public function doctor()
    {
        return $this->hasOneThrough(
            Doctor::class,
            Result::class,
            'id',
            'id',
            'result_id',
            'doctor_id'
        );
    }

    // ✅ Status Helper Methods
    public function isPending()    { return $this->status === 'pending'; }
    public function isProcessing() { return $this->status === 'processing'; }
    public function isSuccess()    { return $this->status === 'success'; }
    public function isFailed()     { return $this->status === 'failed'; }

    // 📊 Scopes for filtering
    public function scopeForOutlet($query, $outletId)
    {
        return $query->where('outlet_id', $outletId);
    }

    public function scopeWithStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeProcessing($query)
    {
        return $query->where('status', 'processing');
    }

    public function scopeSuccess($query)
    {
        return $query->where('status', 'success');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function scopeOfType($query, $type)
    {
        return $query->where('type_surat', $type);
    }

    public function scopeRecentFirst($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    public function scopeOldFirst($query)
    {
        return $query->orderBy('created_at', 'asc');
    }

    // 🕐 Time-based scopes
    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    public function scopeThisWeek($query)
    {
        return $query->whereBetween('created_at', [
            now()->startOfWeek(),
            now()->endOfWeek()
        ]);
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year);
    }

    public function scopeOlderThan($query, $days)
    {
        return $query->where('created_at', '<', now()->subDays($days));
    }

    // 🔍 Search scopes
    public function scopeSearchByPatient($query, $search)
    {
        return $query->where('patient_name', 'LIKE', "%{$search}%");
    }

    public function scopeSearchByLetterNumber($query, $search)
    {
        return $query->where('no_letters', 'LIKE', "%{$search}%");
    }

    // 📈 Accessors for better data presentation
    public function getStatusLabelAttribute()
    {
        return ucfirst($this->status);
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'pending' => 'amber',
            'processing' => 'blue',
            'success' => 'emerald',
            'failed' => 'red',
            default => 'slate'
        };
    }

    public function getTypeUpperAttribute()
    {
        return strtoupper($this->type_surat ?? '');
    }

    public function getFormattedCreatedAtAttribute()
    {
        return $this->created_at ? $this->created_at->format('d M Y H:i') : '-';
    }

    public function getFormattedCompletedAtAttribute()
    {
        return $this->completed_at ? $this->completed_at->format('d M Y H:i') : '-';
    }

    public function getDurationAttribute()
    {
        if (!$this->created_at || !$this->completed_at) {
            return null;
        }

        $seconds = $this->created_at->diffInSeconds($this->completed_at);
        
        if ($seconds < 60) {
            return "{$seconds}s";
        } elseif ($seconds < 3600) {
            $minutes = floor($seconds / 60);
            $remainingSeconds = $seconds % 60;
            return "{$minutes}m {$remainingSeconds}s";
        } else {
            $hours = floor($seconds / 3600);
            $minutes = floor(($seconds % 3600) / 60);
            return "{$hours}h {$minutes}m";
        }
    }

    public function getCanRetryAttribute()
    {
        return $this->status === 'failed';
    }

    public function getCanDeleteAttribute()
    {
        return in_array($this->status, ['pending', 'failed']);
    }

    // 🛠️ Helper Methods
    public function markAsProcessing()
    {
        return $this->update([
            'status' => 'processing',
            'processed_at' => now(),
            'error_log' => null
        ]);
    }

    public function markAsSuccess($filename = null)
    {
        return $this->update([
            'status' => 'success',
            'completed_at' => now(),
            'generated_file' => $filename,
            'error_log' => null
        ]);
    }

    public function markAsFailed($errorMessage)
    {
        return $this->update([
            'status' => 'failed',
            'completed_at' => now(),
            'error_log' => $errorMessage
        ]);
    }

    public function incrementRetry()
    {
        return $this->update([
            'retry_count' => ($this->retry_count ?? 0) + 1
        ]);
    }

    public function resetForRetry()
    {
        return $this->update([
            'status' => 'pending',
            'processed_at' => null,
            'completed_at' => null,
            'error_log' => null,
            'retry_count' => ($this->retry_count ?? 0) + 1
        ]);
    }

    // 📊 Static helper methods for statistics
    public static function getStatsForOutlet($outletId)
    {
        return static::where('outlet_id', $outletId)
            ->selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN status = "pending" THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN status = "processing" THEN 1 ELSE 0 END) as processing,
                SUM(CASE WHEN status = "success" THEN 1 ELSE 0 END) as success,
                SUM(CASE WHEN status = "failed" THEN 1 ELSE 0 END) as failed
            ')
            ->first();
    }

    public static function getFailedQueuesForOutlet($outletId)
    {
        return static::where('outlet_id', $outletId)
            ->where('status', 'failed')
            ->get();
    }

    public static function cleanCompletedForOutlet($outletId, $days = 7)
    {
        return static::where('outlet_id', $outletId)
            ->where('status', 'success')
            ->where('completed_at', '<', now()->subDays($days))
            ->delete();
    }

    // 🔄 Boot method for auto-population
    protected static function boot()
    {
        parent::boot();

        // Auto-populate outlet_id from result when creating
        static::creating(function ($queue) {
            if (!$queue->outlet_id && $queue->result_id) {
                $result = Result::find($queue->result_id);
                if ($result) {
                    $queue->outlet_id = $result->outlet_id;
                    
                    // Also populate other snapshot data if not already set
                    if (!$queue->patient_name && $result->patient) {
                        $queue->patient_name = $result->patient->full_name;
                    }
                    if (!$queue->outlet_name && $result->outlet) {
                        $queue->outlet_name = $result->outlet->name;
                    }
                    if (!$queue->type_surat) {
                        $queue->type_surat = $result->type;
                    }
                    if (!$queue->no_letters) {
                        $queue->no_letters = $result->no_letters;
                    }
                }
            }
        });
    }

    // 🔍 Advanced search and filtering
    public function scopeAdvancedFilter($query, $filters)
    {
        return $query->when($filters['status'] ?? null, function ($q, $status) {
                return $q->where('status', $status);
            })
            ->when($filters['type'] ?? null, function ($q, $type) {
                return $q->where('type_surat', $type);
            })
            ->when($filters['patient'] ?? null, function ($q, $patient) {
                return $q->where('patient_name', 'LIKE', "%{$patient}%");
            })
            ->when($filters['outlet'] ?? null, function ($q, $outlet) {
                return $q->where('outlet_name', 'LIKE', "%{$outlet}%");
            })
            ->when($filters['date_from'] ?? null, function ($q, $date) {
                return $q->whereDate('created_at', '>=', $date);
            })
            ->when($filters['date_to'] ?? null, function ($q, $date) {
                return $q->whereDate('created_at', '<=', $date);
            });
    }

    // 📄 Export helpers
    public function toExportArray()
    {
        return [
            'ID' => $this->id,
            'Result ID' => $this->result_id,
            'No Surat' => $this->no_letters,
            'Pasien' => $this->patient_name,
            'Tipe Surat' => $this->type_upper,
            'Outlet' => $this->outlet_name,
            'Status' => $this->status_label,
            'Retry Count' => $this->retry_count ?? 0,
            'Dibuat' => $this->formatted_created_at,
            'Selesai' => $this->formatted_completed_at,
            'Durasi' => $this->duration ?? '-',
            'Error' => $this->error_log ?? '-'
        ];
    }

    // 🔔 Notification helpers
    public function shouldNotifyFailure()
    {
        return $this->status === 'failed' && ($this->retry_count ?? 0) >= 3;
    }

    public function getNotificationMessage()
    {
        return match($this->status) {
            'success' => "Dokumen {$this->no_letters} berhasil di-generate",
            'failed' => "Gagal generate dokumen {$this->no_letters}: {$this->error_log}",
            'processing' => "Sedang memproses dokumen {$this->no_letters}",
            default => "Status dokumen {$this->no_letters}: {$this->status}"
        };
    }
}