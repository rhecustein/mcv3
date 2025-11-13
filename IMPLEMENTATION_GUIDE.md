# 🚀 Implementation Guide - Sprint 1 (Multi-Tenancy)

**For**: Solo Developer
**Sprint**: 1 of 7
**Duration**: 2 weeks
**Focus**: Multi-tenancy Foundation

---

## 📋 Quick Start

### Step 1: Install stancl/tenancy Package

```bash
# Install the tenancy package
composer require stancl/tenancy

# Publish tenancy config
php artisan vendor:publish --provider='Stancl\Tenancy\TenancyServiceProvider' --tag=config

# Publish tenancy migrations
php artisan vendor:publish --provider='Stancl\Tenancy\TenancyServiceProvider' --tag=migrations
```

### Step 2: Run Migrations

```bash
# Run all migrations (including tenancy)
php artisan migrate

# Fresh migration with seed (if needed)
php artisan migrate:fresh --seed
```

### Step 3: Seed Initial Data

```bash
# Seed subscription plans and demo tenants
php artisan db:seed
```

---

## 🗂️ Files Created

### Migrations (database/migrations/)
```
✅ 2025_11_13_000001_create_tenants_table.php
✅ 2025_11_13_000002_create_subscription_plans_table.php
✅ 2025_11_13_000003_create_tenant_subscriptions_table.php
✅ 2025_11_13_000004_create_tenant_usage_table.php
✅ 2025_11_13_000005_add_tenant_id_to_existing_tables.php
✅ 2025_11_13_000006_create_tenant_invoices_table.php
```

### Models (app/Models/)
```
✅ Tenant.php - Main tenant model with quota management
✅ SubscriptionPlan.php - Plan definitions
✅ TenantSubscription.php - Active subscriptions
✅ TenantUsage.php - Usage tracking
✅ TenantInvoice.php - Billing & invoices
```

### Seeders (database/seeders/)
```
✅ SubscriptionPlanSeeder.php - 3 plans (Starter, Pro, Enterprise)
✅ TenantSeeder.php - 3 demo tenants
✅ DatabaseSeeder.php - Master seeder
```

---

## 🏗️ Database Schema Overview

### tenants
```sql
- id, name, slug, domain
- subscription_plan, subscription_status
- trial_ends_at, subscription_ends_at
- max_users, max_documents_per_month, max_storage_mb
- current_users, current_documents_this_month, current_storage_mb
- settings (JSON), enabled_features (JSON)
- is_active
```

### subscription_plans
```sql
- id, name, slug, description
- price_monthly, price_yearly
- max_users, max_documents_per_month, max_storage_mb
- features (JSON)
- is_active, is_featured
```

### tenant_subscriptions
```sql
- id, tenant_id, subscription_plan_id
- starts_at, ends_at, billing_cycle
- price, status, auto_renew
- payment_method, paid_at
```

### tenant_usage
```sql
- id, tenant_id
- period_start, period_end
- documents_generated, mcu_bookings
- storage_used_mb, api_calls
```

### tenant_invoices
```sql
- id, tenant_id, invoice_number
- invoice_date, due_date
- subtotal, tax_amount, total_amount
- status, paid_at
```

---

## 🎯 Next Steps - Configuration

### 1. Configure stancl/tenancy

Edit `config/tenancy.php`:

```php
// Central domains (non-tenant domains)
'central_domains' => [
    'mcv3.local',         // Development
    'localhost',
    '127.0.0.1',
    'mcv3.com',           // Production
],

// Tenant identification
'tenant_route_namespace' => 'App\\Http\\Controllers\\Tenant',
```

### 2. Setup Subdomain Routing

Create `routes/tenant.php`:

```php
<?php

use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

Route::middleware([
    'web',
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
])->group(function () {
    Route::get('/', function () {
        return view('tenant.dashboard', [
            'tenant' => tenant()
        ]);
    })->name('tenant.dashboard');

    // Your existing routes here
    // Route::resource('results', ResultController::class);
    // etc...
});
```

### 3. Create Tenant Middleware

```bash
php artisan make:middleware TenantAware
```

`app/Http/Middleware/TenantAware.php`:

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class TenantAware
{
    public function handle(Request $request, Closure $next)
    {
        // Ensure tenant is initialized
        if (!tenancy()->initialized) {
            abort(404, 'Tenant not found');
        }

        // Share tenant with all views
        view()->share('currentTenant', tenant());

        return $next($request);
    }
}
```

### 4. Register Middleware

`app/Http/Kernel.php`:

```php
protected $middlewareGroups = [
    'tenant' => [
        \Stancl\Tenancy\Middleware\InitializeTenancyByDomain::class,
        \Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains::class,
        \App\Http\Middleware\TenantAware::class,
    ],
];
```

---

## 🧪 Testing

### Test Tenant Creation

```php
// In tinker or test
php artisan tinker

$tenant = Tenant::create([
    'name' => 'Test Clinic',
    'slug' => 'testclinic',
    'contact_email' => 'test@clinic.com',
]);

// Check trial period
$tenant->isTrialing(); // true
$tenant->trialDaysRemaining(); // 14
```

### Test Usage Tracking

```php
$tenant->incrementDocumentCount();
$tenant->incrementStorageUsage(15.5);
$tenant->canGenerateDocument(); // check quota
```

### Test Plans

```php
$plans = SubscriptionPlan::active()->get();

foreach ($plans as $plan) {
    echo $plan->name . ': ' . $plan->getFormattedPriceMonthly();
}
```

---

## 🔒 Security Checklist

### Tenant Isolation

✅ Every query must be scoped to current tenant
✅ Use global scopes on models:

```php
// In your existing models (User, Result, etc.)
protected static function booted()
{
    static::addGlobalScope('tenant', function ($builder) {
        if (tenancy()->initialized) {
            $builder->where('tenant_id', tenant('id'));
        }
    });
}
```

### Prevent Cross-Tenant Access

✅ Never trust user input for tenant_id
✅ Always use `tenant('id')` to get current tenant
✅ Test with multiple subdomains

---

## 📊 Usage Examples

### Check Quota Before Action

```php
// Before generating document
$tenant = tenant();

if (!$tenant->canGenerateDocument()) {
    return response()->json([
        'error' => 'Monthly document quota exceeded',
        'quota' => $tenant->max_documents_per_month,
        'used' => $tenant->current_documents_this_month,
    ], 429);
}

// Generate document...
$tenant->incrementDocumentCount();
```

### Track Storage

```php
// After uploading file
$fileSize = $file->getSize() / 1048576; // bytes to MB

if (!$tenant->hasStorageSpace($fileSize)) {
    return response()->json([
        'error' => 'Storage quota exceeded',
        'quota_mb' => $tenant->max_storage_mb,
        'used_mb' => $tenant->current_storage_mb,
    ], 429);
}

$tenant->incrementStorageUsage($fileSize);
```

### Display Usage Dashboard

```php
// In controller
public function dashboard()
{
    $tenant = tenant();

    return view('tenant.dashboard', [
        'usagePercent' => [
            'users' => $tenant->getUsagePercentage('users'),
            'documents' => $tenant->getUsagePercentage('documents'),
            'storage' => $tenant->getUsagePercentage('storage'),
        ],
        'remaining' => [
            'documents' => $tenant->getRemainingDocuments(),
            'storage' => $tenant->getRemainingStorage(),
        ],
    ]);
}
```

---

## 🎨 Frontend Integration

### Display Subscription Info

```blade
{{-- resources/views/tenant/dashboard.blade.php --}}

<div class="subscription-info">
    <h3>{{ $currentTenant->getPlanName() }}</h3>

    @if($currentTenant->isTrialing())
        <div class="alert alert-info">
            Trial: {{ $currentTenant->trialDaysRemaining() }} days remaining
        </div>
    @endif

    @if($currentTenant->subscription_status === 'active')
        <p>Active until: {{ $currentTenant->subscription_ends_at->format('d M Y') }}</p>
    @endif
</div>

<div class="usage-stats">
    <div class="stat">
        <h4>Documents</h4>
        <div class="progress">
            <div class="progress-bar" style="width: {{ $usagePercent['documents'] }}%"></div>
        </div>
        <small>{{ $currentTenant->current_documents_this_month }} / {{ $currentTenant->max_documents_per_month }}</small>
    </div>

    <div class="stat">
        <h4>Storage</h4>
        <div class="progress">
            <div class="progress-bar" style="width: {{ $usagePercent['storage'] }}%"></div>
        </div>
        <small>{{ number_format($currentTenant->current_storage_mb, 2) }} MB / {{ $currentTenant->max_storage_mb }} MB</small>
    </div>
</div>
```

---

## 🔄 Monthly Reset (Cron Job)

### Create Command

```bash
php artisan make:command ResetMonthlyUsage
```

```php
<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use Illuminate\Console\Command;

class ResetMonthlyUsage extends Command
{
    protected $signature = 'tenants:reset-monthly-usage';
    protected $description = 'Reset monthly usage counters for all tenants';

    public function handle()
    {
        Tenant::all()->each(function ($tenant) {
            $tenant->resetMonthlyUsage();
            $this->info("Reset usage for: {$tenant->name}");
        });

        $this->info('✅ All tenants reset successfully');
    }
}
```

### Schedule in `app/Console/Kernel.php`

```php
protected function schedule(Schedule $schedule)
{
    // Reset monthly usage on 1st of every month at 00:00
    $schedule->command('tenants:reset-monthly-usage')
        ->monthly()
        ->at('00:00');
}
```

---

## 🚀 Deploy Checklist

### Development

- [ ] Run migrations
- [ ] Seed data
- [ ] Test subdomain routing (*.mcv3.local)
- [ ] Configure /etc/hosts:
  ```
  127.0.0.1  mcv3.local
  127.0.0.1  kimiafarma.mcv3.local
  127.0.0.1  siloam.mcv3.local
  127.0.0.1  pratama.mcv3.local
  ```

### Production

- [ ] Setup wildcard DNS (*.yourdomain.com)
- [ ] Configure SSL for wildcard domain
- [ ] Enable queue workers for jobs
- [ ] Setup cron for scheduled tasks
- [ ] Configure Redis for caching
- [ ] Enable database backups

---

## 📝 Development Workflow

### Daily Workflow

```bash
# 1. Pull latest code
git pull origin claude/optimize-pdf-size-011CV5zKTxoxAycsMkyFzQHC

# 2. Run migrations (if new)
php artisan migrate

# 3. Start dev server
php artisan serve

# 4. In another terminal, start queue worker
php artisan queue:work

# 5. Access tenant
# http://kimiafarma.mcv3.local:8000
```

### When Adding Features

1. Always add `tenant_id` to new tables
2. Add global scope to new models
3. Test with multiple tenants
4. Check quota before actions
5. Update usage metrics

---

## 🎯 Next Sprint Preview

**Sprint 2**: Payment Gateway & API

Upcoming features:
- Midtrans integration
- Subscription upgrade/downgrade
- Auto-renewal
- Invoice generation
- REST API with OAuth
- Webhook handlers

---

## 💡 Tips for Solo Developer

### Time Management
- Focus on P0 features only
- Use existing components where possible
- Don't over-optimize early
- Document as you go

### Code Quality
- Write tests for critical paths
- Use Laravel conventions
- Keep controllers thin
- Use service classes for complex logic

### Debugging
```bash
# Enable query logging
DB::enableQueryLog();
// ... your code
dd(DB::getQueryLog());

# Check current tenant
dd(tenant());

# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

---

## 📞 Need Help?

### Resources
- stancl/tenancy docs: https://tenancyforlaravel.com
- Laravel docs: https://laravel.com/docs
- Project docs: See SAAS_TRANSFORMATION_PLAN.md

### Common Issues

**Issue**: Tenant not found
**Solution**: Check subdomain routing and DNS

**Issue**: Cross-tenant data leak
**Solution**: Verify global scopes on all models

**Issue**: Quota not updating
**Solution**: Use `incrementDocumentCount()` after generation

---

**Ready to start? Run these commands:**

```bash
composer require stancl/tenancy
php artisan migrate
php artisan db:seed
php artisan serve
```

**Then access**: http://kimiafarma.mcv3.local:8000

🚀 **Happy coding!**
