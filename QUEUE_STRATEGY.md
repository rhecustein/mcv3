# 🚀 Queue System Strategy - SehatCert

Comprehensive queue strategy for background jobs, notifications, and PDF management.

**Version**: 1.0
**Date**: 2026-01-07
**Project**: SehatCert Medical Certificate Platform

---

## 📋 Table of Contents

1. [Queue Driver Recommendation](#queue-driver-recommendation)
2. [Jobs to Queue](#jobs-to-queue)
3. [Queue Configuration](#queue-configuration)
4. [Queue Monitoring](#queue-monitoring)
5. [Implementation Guide](#implementation-guide)

---

## 🎯 Queue Driver Recommendation

### **RECOMMENDED: Redis Queue**

**Why Redis?**
- ✅ **Fast**: In-memory processing (microseconds)
- ✅ **Reliable**: Persistent, survives restarts
- ✅ **Scalable**: Handles thousands of jobs/second
- ✅ **Battle-tested**: Industry standard
- ✅ **Laravel Native**: Built-in support

**Comparison**:

| Driver | Speed | Reliability | Scalability | Cost |
|--------|-------|-------------|-------------|------|
| **Redis** | ⚡⚡⚡⚡⚡ | ✅ High | ✅ Excellent | Medium |
| Database | ⚡⚡ | ✅ High | ⚠️ Limited | Low |
| SQS (AWS) | ⚡⚡⚡⚡ | ✅ Very High | ✅ Unlimited | High |
| Beanstalkd | ⚡⚡⚡⚡ | ⚠️ Medium | ✅ Good | Low |
| Sync | ⚡⚡⚡⚡⚡ | N/A | ❌ None | Free |

**Decision**: **Redis for production**, Database for development/testing.

---

## 📦 Jobs to Queue

### **1. PDF Generation (Priority: HIGH)**

**Why Queue?**
- PDF generation is CPU-intensive (2-5 seconds per certificate)
- Blocks HTTP requests
- Can timeout on slow servers

**Job**: `GenerateCertificatePdfJob`

```php
<?php

namespace App\Jobs\Certificates;

use App\Models\Result;
use App\Services\PdfService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class GenerateCertificatePdfJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 120; // 2 minutes
    public $backoff = [10, 30, 60]; // Retry after 10s, 30s, 60s

    public function __construct(
        public Result $result
    ) {
        // Queue on 'high' priority for user-facing operations
        $this->onQueue('high');
    }

    public function handle(PdfService $pdfService): void
    {
        try {
            Log::info("Generating PDF for certificate", [
                'result_id' => $this->result->id,
                'unique_code' => $this->result->unique_code,
            ]);

            // Generate PDF
            $pdf = $pdfService->generateCertificate($this->result);

            // Save to storage
            $filename = "certificates/{$this->result->tenant_id}/{$this->result->unique_code}.pdf";
            Storage::disk('public')->put($filename, $pdf->output());

            // Update result with PDF path
            $this->result->update([
                'pdf_path' => $filename,
                'pdf_generated_at' => now(),
            ]);

            Log::info("PDF generated successfully", [
                'result_id' => $this->result->id,
                'filename' => $filename,
            ]);

        } catch (\Exception $e) {
            Log::error("PDF generation failed", [
                'result_id' => $this->result->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Will retry automatically based on $tries
            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("PDF generation failed permanently", [
            'result_id' => $this->result->id,
            'error' => $exception->getMessage(),
        ]);

        // Update result to mark as failed
        $this->result->update([
            'pdf_generation_failed' => true,
            'pdf_error' => $exception->getMessage(),
        ]);

        // TODO: Send notification to admin
    }
}
```

**Dispatch**:
```php
// In controller after creating certificate
GenerateCertificatePdfJob::dispatch($result);

// Or dispatch with delay (5 seconds)
GenerateCertificatePdfJob::dispatch($result)->delay(now()->addSeconds(5));
```

---

### **2. WhatsApp Notifications (Priority: MEDIUM)**

**Job**: `SendWhatsAppNotificationJob`

```php
<?php

namespace App\Jobs\Notifications;

use App\Models\Result;
use App\Services\WhatsAppService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendWhatsAppNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 5;
    public $timeout = 30;
    public $backoff = [30, 60, 120, 300, 600]; // Exponential backoff

    public function __construct(
        public Result $result,
        public string $phoneNumber,
        public string $message
    ) {
        $this->onQueue('notifications');
    }

    public function handle(WhatsAppService $whatsAppService): void
    {
        $whatsAppService->send($this->phoneNumber, $this->message);
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("WhatsApp notification failed permanently", [
            'result_id' => $this->result->id,
            'phone' => $this->phoneNumber,
            'error' => $exception->getMessage(),
        ]);
    }
}
```

---

### **3. Email Notifications (Priority: MEDIUM)**

**Job**: `SendCertificateEmailJob`

```php
<?php

namespace App\Jobs\Notifications;

use App\Models\Result;
use App\Mail\CertificateReadyMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendCertificateEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 60;

    public function __construct(
        public Result $result,
        public string $email
    ) {
        $this->onQueue('notifications');
    }

    public function handle(): void
    {
        Mail::to($this->email)->send(new CertificateReadyMail($this->result));
    }
}
```

---

### **4. PDF Cleanup (Priority: LOW)**

**Job**: `CleanupOldPdfsJob`

```php
<?php

namespace App\Jobs\Maintenance;

use App\Models\Result;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class CleanupOldPdfsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 1;
    public $timeout = 3600; // 1 hour for large cleanups

    public function __construct(
        public int $daysOld = 30, // Default: 30 days
        public bool $archive = true, // Archive before delete
        public ?string $tenantId = null // Specific tenant or all
    ) {
        $this->onQueue('maintenance');
    }

    public function handle(): void
    {
        $cutoffDate = Carbon::now()->subDays($this->daysOld);

        $query = Result::where('created_at', '<', $cutoffDate)
            ->whereNotNull('pdf_path');

        if ($this->tenantId) {
            $query->where('tenant_id', $this->tenantId);
        }

        $results = $query->get();

        $deletedCount = 0;
        $archivedCount = 0;
        $errorCount = 0;

        foreach ($results as $result) {
            try {
                if ($this->archive) {
                    // Archive to S3/Glacier before delete
                    $this->archivePdf($result);
                    $archivedCount++;
                }

                // Delete from local storage
                if (Storage::disk('public')->exists($result->pdf_path)) {
                    Storage::disk('public')->delete($result->pdf_path);
                    $deletedCount++;
                }

                // Update result
                $result->update([
                    'pdf_path' => null,
                    'pdf_deleted_at' => now(),
                    'pdf_archived' => $this->archive,
                ]);

            } catch (\Exception $e) {
                Log::error("Failed to cleanup PDF", [
                    'result_id' => $result->id,
                    'error' => $e->getMessage(),
                ]);
                $errorCount++;
            }
        }

        Log::info("PDF Cleanup completed", [
            'days_old' => $this->daysOld,
            'tenant_id' => $this->tenantId ?? 'all',
            'archived' => $archivedCount,
            'deleted' => $deletedCount,
            'errors' => $errorCount,
        ]);

        // Create cleanup log entry
        \App\Models\PdfCleanupLog::create([
            'days_old' => $this->daysOld,
            'tenant_id' => $this->tenantId,
            'archived_count' => $archivedCount,
            'deleted_count' => $deletedCount,
            'error_count' => $errorCount,
            'executed_at' => now(),
        ]);
    }

    private function archivePdf(Result $result): void
    {
        // Archive to S3 Glacier (cheap long-term storage)
        $archivePath = "archives/{$result->tenant_id}/certificates/{$result->unique_code}.pdf";

        $pdfContent = Storage::disk('public')->get($result->pdf_path);

        Storage::disk('s3')->put($archivePath, $pdfContent, [
            'StorageClass' => 'GLACIER', // Cheap archival storage
        ]);

        // Or compress and store locally
        // $compressedPath = $result->pdf_path . '.gz';
        // $compressed = gzencode($pdfContent, 9);
        // Storage::disk('archive')->put($compressedPath, $compressed);
    }
}
```

**Schedule** (in `app/Console/Kernel.php`):
```php
protected function schedule(Schedule $schedule): void
{
    // Run daily at 2 AM
    $schedule->job(new CleanupOldPdfsJob(30, true))
        ->dailyAt('02:00')
        ->name('cleanup-old-pdfs-30-days')
        ->onOneServer();

    // Aggressive cleanup for very old PDFs (6 months)
    $schedule->job(new CleanupOldPdfsJob(180, false))
        ->weekly()
        ->sundays()
        ->at('03:00')
        ->name('cleanup-very-old-pdfs')
        ->onOneServer();
}
```

---

### **5. Statistics Aggregation (Priority: LOW)**

**Job**: `AggregateCompanyStatisticsJob`

```php
<?php

namespace App\Jobs\Statistics;

use App\Models\Company;
use App\Models\Result;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon;

class AggregateCompanyStatisticsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public Company $company,
        public string $period = 'monthly' // daily, weekly, monthly, yearly
    ) {
        $this->onQueue('statistics');
    }

    public function handle(): void
    {
        $startDate = match($this->period) {
            'daily' => Carbon::today(),
            'weekly' => Carbon::now()->startOfWeek(),
            'monthly' => Carbon::now()->startOfMonth(),
            'yearly' => Carbon::now()->startOfYear(),
        };

        $statistics = Result::where('company_id', $this->company->id)
            ->where('created_at', '>=', $startDate)
            ->selectRaw('
                COUNT(*) as total_certificates,
                COUNT(DISTINCT patient_id) as unique_patients,
                COUNT(CASE WHEN type = "MC" THEN 1 END) as mc_count,
                COUNT(CASE WHEN type = "SKB" THEN 1 END) as skb_count,
                AVG(duration) as avg_duration
            ')
            ->first();

        // Cache for 1 hour
        cache()->put(
            "company_stats_{$this->company->id}_{$this->period}",
            $statistics,
            now()->addHour()
        );
    }
}
```

---

## ⚙️ Queue Configuration

### **config/queue.php**

```php
<?php

return [
    'default' => env('QUEUE_CONNECTION', 'redis'),

    'connections' => [
        'redis' => [
            'driver' => 'redis',
            'connection' => 'default',
            'queue' => env('REDIS_QUEUE', 'default'),
            'retry_after' => 90,
            'block_for' => null,
            'after_commit' => false,
        ],

        // Multiple queues with priorities
        'high' => [
            'driver' => 'redis',
            'connection' => 'default',
            'queue' => 'high',
            'retry_after' => 90,
        ],

        'notifications' => [
            'driver' => 'redis',
            'connection' => 'default',
            'queue' => 'notifications',
            'retry_after' => 90,
        ],

        'maintenance' => [
            'driver' => 'redis',
            'connection' => 'default',
            'queue' => 'maintenance',
            'retry_after' => 3600,
        ],

        'statistics' => [
            'driver' => 'redis',
            'connection' => 'default',
            'queue' => 'statistics',
            'retry_after' => 300,
        ],
    ],

    'failed' => [
        'driver' => env('QUEUE_FAILED_DRIVER', 'database-uuids'),
        'database' => env('DB_CONNECTION', 'mysql'),
        'table' => 'failed_jobs',
    ],
];
```

### **.env Configuration**

```env
QUEUE_CONNECTION=redis
REDIS_QUEUE=default

# Redis Configuration
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
REDIS_DB=0
```

---

## 🚀 Running Queue Workers

### **1. Development (Single Worker)**

```bash
php artisan queue:work redis --queue=high,notifications,default,statistics,maintenance --tries=3 --timeout=90
```

### **2. Production (Supervisor)**

**Install Supervisor**:
```bash
sudo apt-get install supervisor
```

**Supervisor Config** (`/etc/supervisor/conf.d/sehatcert-worker.conf`):
```ini
[program:sehatcert-worker-high]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/sehatcert/artisan queue:work redis --queue=high --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=4
redirect_stderr=true
stdout_logfile=/var/www/sehatcert/storage/logs/worker-high.log
stopwaitsecs=3600

[program:sehatcert-worker-notifications]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/sehatcert/artisan queue:work redis --queue=notifications --sleep=3 --tries=5 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/sehatcert/storage/logs/worker-notifications.log
stopwaitsecs=3600

[program:sehatcert-worker-default]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/sehatcert/artisan queue:work redis --queue=default --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/sehatcert/storage/logs/worker-default.log
stopwaitsecs=3600

[program:sehatcert-worker-maintenance]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/sehatcert/artisan queue:work redis --queue=maintenance --sleep=10 --tries=1 --max-time=7200
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=1
redirect_stderr=true
stdout_logfile=/var/www/sehatcert/storage/logs/worker-maintenance.log
stopwaitsecs=7200
```

**Start Supervisor**:
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start all
sudo supervisorctl status
```

---

## 📊 Queue Monitoring

### **1. Laravel Horizon (Recommended)**

**Install**:
```bash
composer require laravel/horizon
php artisan horizon:install
php artisan migrate
```

**Configure** (`config/horizon.php`):
```php
'environments' => [
    'production' => [
        'supervisor-1' => [
            'connection' => 'redis',
            'queue' => ['high', 'default'],
            'balance' => 'auto',
            'processes' => 10,
            'tries' => 3,
        ],
        'supervisor-notifications' => [
            'connection' => 'redis',
            'queue' => ['notifications'],
            'balance' => 'auto',
            'processes' => 5,
            'tries' => 5,
        ],
        'supervisor-maintenance' => [
            'connection' => 'redis',
            'queue' => ['maintenance', 'statistics'],
            'balance' => 'simple',
            'processes' => 2,
            'tries' => 1,
        ],
    ],
],
```

**Access**: `https://sehatcert.com/horizon`

**Features**:
- Real-time metrics
- Job throughput
- Failed jobs management
- Queue balancing
- Auto-scaling

### **2. Manual Monitoring**

```bash
# View jobs in queue
php artisan queue:monitor redis:high,redis:notifications,redis:default

# View failed jobs
php artisan queue:failed

# Retry failed jobs
php artisan queue:retry all

# Clear failed jobs
php artisan queue:flush
```

---

## 🎯 Best Practices

### **1. Job Design**
- ✅ Keep jobs small and focused (Single Responsibility)
- ✅ Make jobs idempotent (can be run multiple times safely)
- ✅ Set appropriate timeout and retry values
- ✅ Always handle failures gracefully

### **2. Performance**
- ✅ Use job batching for bulk operations
- ✅ Chunk large datasets
- ✅ Use appropriate queue priorities
- ✅ Monitor queue depth

### **3. Error Handling**
- ✅ Log all failures
- ✅ Send alerts for critical failures
- ✅ Implement exponential backoff
- ✅ Store failed jobs for analysis

### **4. Monitoring**
- ✅ Use Laravel Horizon
- ✅ Set up alerts (Sentry, Bugsnag)
- ✅ Monitor Redis memory usage
- ✅ Track job metrics

---

## 📈 Queue Metrics to Monitor

| Metric | Threshold | Action |
|--------|-----------|--------|
| Queue Depth | > 1000 jobs | Add more workers |
| Processing Time | > 90s average | Optimize jobs |
| Failed Jobs | > 10/hour | Investigate |
| Redis Memory | > 80% | Scale Redis |
| Worker CPU | > 80% | Add servers |

---

## 🔧 Troubleshooting

### **Jobs not processing**
```bash
# Check workers are running
php artisan queue:work redis --queue=high --once

# Check Redis connection
redis-cli ping

# Check Supervisor
sudo supervisorctl status
```

### **Jobs failing repeatedly**
```bash
# View failed jobs
php artisan queue:failed

# View specific job
php artisan queue:failed <job-id>

# Retry
php artisan queue:retry <job-id>
```

### **Memory issues**
```bash
# Restart workers regularly
php artisan queue:restart

# Or set max jobs per worker
php artisan queue:work --max-jobs=1000
```

---

## ✅ Summary

**Queue Strategy**:
- 🎯 **Driver**: Redis (production), Database (dev)
- 🎯 **Priorities**: high → notifications → default → statistics → maintenance
- 🎯 **Workers**: 4 high, 2 notifications, 2 default, 1 maintenance
- 🎯 **Monitoring**: Laravel Horizon
- 🎯 **Scheduling**: PDF cleanup daily, statistics hourly

**Key Jobs**:
1. GenerateCertificatePdfJob (HIGH priority)
2. SendWhatsAppNotificationJob (MEDIUM priority)
3. SendCertificateEmailJob (MEDIUM priority)
4. CleanupOldPdfsJob (LOW priority - scheduled)
5. AggregateCompanyStatisticsJob (LOW priority - scheduled)

**Next**: Implement PDF Storage Management for Superadmin →
