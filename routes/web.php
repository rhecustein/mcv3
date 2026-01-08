<?php

// File: routes/web.php

use App\Http\Middleware\EnsureSingleSession;
use App\Http\Controllers\AdminManagementController;
use App\Http\Controllers\OutletController;
use App\Http\Controllers\DoctorManagementController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ResultController;
use App\Http\Controllers\SessionLoginController;
use App\Http\Controllers\TemplateResultController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\PackageTransactionController;
use App\Http\Controllers\StatisticsManagementController;
use App\Http\Controllers\PatientController;
use App\Http\Middleware\CheckRoleType;
use App\Http\Controllers\DoctorOutletController;
use App\Http\Controllers\HealthletterController;
use App\Http\Controllers\DocumentQueueController;
use App\Http\Controllers\PublicResultController;
use App\Http\Controllers\ReportManagementController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\LegacyResultController;
use App\Http\Controllers\OutletProfileController;
use App\Http\Controllers\CompanyClientController;

use App\Models\Result;
use App\Models\Patient;
use App\Models\Company;
use App\Models\IcdMaster;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Health Management System Routes
|--------------------------------------------------------------------------
|
| Organized by access level and functionality:
| 1. Public Routes (no authentication)
| 2. Common Authenticated Routes (all roles)
| 3. Superadmin Routes (system management)
| 4. Admin Routes (admin management)
| 5. Outlet Routes (daily operations)
| 6. Doctor Routes (medical operations)
| 7. Company Routes (company management)
| 8. Patient Routes (patient portal)
| 9. API Routes (data endpoints)
|
*/

// ==================== PUBLIC ROUTES ====================

// Landing page
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Public result verification
Route::get('/verify/{code}', [PublicResultController::class, 'verify'])
    ->name('result.verify');

// Authentication routes
require __DIR__.'/auth.php';



// ==================== COMMON AUTHENTICATED ROUTES ====================
Route::middleware(['auth', EnsureSingleSession::class])->group(function () {
    
    // ========== DASHBOARD ==========
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->middleware(['verified'])->name('dashboard');
    
    // ========== NOTIFICATIONS ==========
    Route::get('/header/notifications', [NotificationController::class, 'fetchHeaderNotifications'])
        ->name('header.notifications');
    
    // ========== PROFILE MANAGEMENT ==========
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('edit');
        Route::put('/', [ProfileController::class, 'update'])->name('update');
        Route::put('/password', [ProfileController::class, 'updatePassword'])->name('password');
        Route::get('/{user}', [ProfileController::class, 'show'])->name('show');
        Route::get('/sessions', [ProfileController::class, 'sessionLogs'])->name('sessions');
    });
    
    // ========== SETTINGS (ALL ROLES) ==========
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [ProfileController::class, 'settings'])->name('index');
        Route::get('/activity', [ProfileController::class, 'activity'])->name('activity');
        Route::get('/session', [ProfileController::class, 'session'])->name('session');
        Route::get('/notifications', [ProfileController::class, 'notifications'])->name('notifications');
        Route::put('/notifications', [ProfileController::class, 'updateNotifications'])->name('notifications.update');
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    });
});

// ==================== SUPERADMIN ROUTES ====================
Route::middleware(['auth', 'can:isSuperadmin'])
    ->prefix('superadmin')
    ->name('superadmin.')
    ->group(function () {
    
    // ========== DASHBOARD ==========
    Route::get('/dashboard', [AdminManagementController::class, 'dashboard'])->name('dashboard');
    
    // ========== ADMIN MANAGEMENT ==========
    Route::prefix('admins')->name('admins.')->group(function () {
        Route::get('/', [AdminManagementController::class, 'index'])->name('index');
        Route::get('/create', [AdminManagementController::class, 'create'])->name('create');
        Route::post('/', [AdminManagementController::class, 'store'])->name('store');
        Route::get('/{admin}/edit', [AdminManagementController::class, 'edit'])->name('edit');
        Route::put('/{admin}', [AdminManagementController::class, 'update'])->name('update');
        Route::delete('/{admin}', [AdminManagementController::class, 'destroy'])->name('destroy');
        Route::post('/{user}/ban', [AdminManagementController::class, 'ban'])->name('ban');
        Route::post('/{user}/unban', [AdminManagementController::class, 'unban'])->name('unban');
    });
    
    // ========== OUTLET MANAGEMENT ==========
    Route::prefix('outlets')->name('outlets.')->group(function () {
        Route::get('/', [OutletController::class, 'index'])->name('index');
        Route::get('/create', [OutletController::class, 'create'])->name('create');
        Route::post('/', [OutletController::class, 'store'])->name('store');
        Route::get('/{outlet}/edit', [OutletController::class, 'edit'])->name('edit');
        Route::put('/{outlet}', [OutletController::class, 'update'])->name('update');
        Route::delete('/{outlet}', [OutletController::class, 'destroy'])->name('destroy');
        Route::patch('/{outlet}/toggle', [OutletController::class, 'toggleActive'])->name('toggle');
        Route::post('/{outlet}/reset-password', [OutletController::class, 'resetPassword'])->name('reset-password');
    });
    
    // ========== DOCTOR MANAGEMENT ==========
    Route::prefix('doctors')->name('doctors.')->group(function () {
        Route::get('/', [DoctorManagementController::class, 'index'])->name('index');
        Route::get('/create', [DoctorManagementController::class, 'create'])->name('create');
        Route::post('/', [DoctorManagementController::class, 'store'])->name('store');
        Route::get('/{doctor}/edit', [DoctorManagementController::class, 'edit'])->name('edit');
        Route::put('/{doctor}', [DoctorManagementController::class, 'update'])->name('update');
        Route::delete('/{doctor}', [DoctorManagementController::class, 'destroy'])->name('destroy');
        Route::post('/{doctor}/ban', [DoctorManagementController::class, 'ban'])->name('ban');
        Route::post('/{doctor}/unban', [DoctorManagementController::class, 'unban'])->name('unban');
        Route::post('/{doctor}/reset-password', [DoctorManagementController::class, 'resetPassword'])->name('resetPassword');
    });
    
    // ========== COMPANY MANAGEMENT ==========
    Route::prefix('companies')->name('companies.')->group(function () {
        Route::get('/', [CompanyController::class, 'index'])->name('index');
        Route::get('/create', [CompanyController::class, 'create'])->name('create');
        Route::post('/', [CompanyController::class, 'store'])->name('store');
        Route::get('/{company}/edit', [CompanyController::class, 'edit'])->name('edit');
        Route::put('/{company}', [CompanyController::class, 'update'])->name('update');
        Route::delete('/{company}', [CompanyController::class, 'destroy'])->name('destroy');
    });
    
    // ========== TEMPLATE MANAGEMENT ==========
    Route::prefix('template-results')->name('template-results.')->group(function () {
        Route::get('/', [TemplateResultController::class, 'index'])->name('index');
        Route::get('/create', [TemplateResultController::class, 'create'])->name('create');
        Route::post('/', [TemplateResultController::class, 'store'])->name('store');
        Route::get('/{templateResult}/edit', [TemplateResultController::class, 'edit'])->name('edit');
        Route::put('/{templateResult}', [TemplateResultController::class, 'update'])->name('update');
        Route::delete('/{templateResult}', [TemplateResultController::class, 'destroy'])->name('destroy');
    });
    
    // ========== STATISTICS & ANALYTICS ==========
    Route::prefix('statistics')->name('statistics.')->group(function () {
        Route::get('/', [StatisticsManagementController::class, 'index'])->name('index');
        Route::get('/leaderboard', [StatisticsManagementController::class, 'leaderboard'])->name('leaderboard');
        Route::get('/api/leaderboard', [StatisticsManagementController::class, 'getLeaderboard'])->name('api.leaderboard');
    });
    
    // ========== PACKAGE MANAGEMENT ==========
    Route::prefix('packages')->name('packages.')->group(function () {
        Route::get('/', [PackageController::class, 'index'])->name('index');
        Route::get('/create', [PackageController::class, 'create'])->name('create');
        Route::post('/', [PackageController::class, 'store'])->name('store');
        Route::get('/{package}/edit', [PackageController::class, 'edit'])->name('edit');
        Route::put('/{package}', [PackageController::class, 'update'])->name('update');
        Route::delete('/{package}', [PackageController::class, 'destroy'])->name('destroy');
    });
    
    // ========== PACKAGE TRANSACTIONS ==========
    Route::prefix('package-transactions')->name('package-transactions.')->group(function () {
        Route::get('/', [PackageTransactionController::class, 'index'])->name('index');
        Route::get('/{packageTransaction}', [PackageTransactionController::class, 'show'])->name('show');
        Route::delete('/{packageTransaction}', [PackageTransactionController::class, 'destroy'])->name('destroy');
    });
    
    // ========== SESSION MANAGEMENT ==========
    Route::prefix('session-logins')->name('session-logins.')->group(function () {
        Route::get('/', [SessionLoginController::class, 'index'])->name('index');
        Route::get('/{sessionLogin}', [SessionLoginController::class, 'show'])->name('show');
        Route::delete('/{sessionLogin}', [SessionLoginController::class, 'destroy'])->name('destroy');
        Route::post('/logout', [SessionLoginController::class, 'logout'])->name('logout');
        Route::post('/logout-all', [SessionLoginController::class, 'logoutAll'])->name('logoutAll');
        Route::post('/logout-other', [SessionLoginController::class, 'logoutOther'])->name('logoutOther');
        Route::post('/block-ip', [SessionLoginController::class, 'blockIp'])->name('block');
        Route::post('/unblock-ip', [SessionLoginController::class, 'unblockIp'])->name('unblock');
    });

    // ========== PDF STORAGE MANAGEMENT ==========
    Route::prefix('pdf-storage')->name('pdf-storage.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Superadmin\PdfStorageController::class, 'index'])->name('index');
        Route::get('/settings', [\App\Http\Controllers\Superadmin\PdfStorageController::class, 'settings'])->name('settings');
        Route::post('/settings', [\App\Http\Controllers\Superadmin\PdfStorageController::class, 'updateSettings'])->name('update-settings');
        Route::post('/cleanup', [\App\Http\Controllers\Superadmin\PdfStorageController::class, 'cleanup'])->name('cleanup');
        Route::get('/logs', [\App\Http\Controllers\Superadmin\PdfStorageController::class, 'logs'])->name('logs');
        Route::get('/archives', [\App\Http\Controllers\Superadmin\PdfStorageController::class, 'archives'])->name('archives');
        Route::post('/restore', [\App\Http\Controllers\Superadmin\PdfStorageController::class, 'restore'])->name('restore');
        Route::post('/bulk-restore', [\App\Http\Controllers\Superadmin\PdfStorageController::class, 'bulkRestore'])->name('bulk-restore');
        Route::get('/statistics', [\App\Http\Controllers\Superadmin\PdfStorageController::class, 'statisticsPage'])->name('statistics');
        Route::get('/api/statistics', [\App\Http\Controllers\Superadmin\PdfStorageController::class, 'statistics'])->name('api.statistics');
    });
});

// ==================== ADMIN ROUTES ====================
Route::middleware(['auth', CheckRoleType::class . ':admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

    // Dashboard
    Route::get('/dashboard', [AdminManagementController::class, 'dashboard'])->name('dashboard');

    // ========== PDF STORAGE MANAGEMENT (Admin/Tenant) ==========
    Route::prefix('pdf-storage')->name('pdf-storage.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\PdfStorageController::class, 'index'])->name('dashboard');
        Route::get('/browse', [\App\Http\Controllers\Admin\PdfStorageController::class, 'browse'])->name('browse');
        Route::get('/download/{id}', [\App\Http\Controllers\Admin\PdfStorageController::class, 'download'])->name('download');
        Route::get('/settings', [\App\Http\Controllers\Admin\PdfStorageController::class, 'settings'])->name('settings');
        Route::get('/api/storage-info', [\App\Http\Controllers\Admin\PdfStorageController::class, 'storageInfo'])->name('api.storage-info');
    });
});

// ==================== OUTLET ROUTES ====================
Route::middleware(['auth', CheckRoleType::class . ':outlet'])
    ->prefix('outlet')
    ->name('outlet.')
    ->group(function () {
    
    // ========== DASHBOARD & HOME ==========
    Route::get('/dashboard', [OutletController::class, 'dashboard'])->name('dashboard');
    Route::get('/home', [OutletController::class, 'home'])->name('home');
    
    // ========== CORE RESOURCES ==========
    Route::resource('doctors', DoctorOutletController::class)->except(['show']);
    Route::get('/patients', [PatientController::class, 'index'])->name('patients.index');
    Route::get('/statistics', [StatisticsManagementController::class, 'index'])->name('statistics.index');
    
    // ========== OUTLET PROFILE MANAGEMENT ==========
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [OutletProfileController::class, 'show'])->name('show');
        Route::get('/edit', [OutletProfileController::class, 'edit'])->name('edit');
        Route::put('/update', [OutletProfileController::class, 'update'])->name('update');
        Route::put('/password', [OutletProfileController::class, 'updatePassword'])->name('password');
        Route::put('/location', [OutletProfileController::class, 'updateLocation'])->name('location');
        Route::get('/api', [OutletProfileController::class, 'apiProfile'])->name('api');
        
        // Settings routes
        Route::get('/settings', [OutletProfileController::class, 'settings'])->name('settings');
        Route::get('/activity', [OutletProfileController::class, 'activity'])->name('activity');
        Route::get('/notifications', [OutletProfileController::class, 'notifications'])->name('notifications');
        Route::put('/notifications', [OutletProfileController::class, 'updateNotifications'])->name('notifications.update');
    });

    // ========== HEALTH LETTERS MANAGEMENT ==========
    Route::prefix('healthletters')->name('healthletter.')->group(function () {
        Route::get('/', [HealthletterController::class, 'index'])->name('index');
    });
    
    // ========== MEDICAL CERTIFICATES (SKB - Surat Keterangan Sehat) ==========
    Route::prefix('results/skb')->name('results.skb.')->group(function () {
        Route::get('/create', [HealthletterController::class, 'createSuratSehat'])->name('create');
        Route::post('/', [HealthletterController::class, 'storeSuratSehat'])->name('store.skb');
        Route::get('/{id}', [HealthletterController::class, 'showSuratSehat'])->name('show');
        Route::get('/{id}/edit', [HealthletterController::class, 'editSuratSehat'])->name('edit');
        Route::put('/{id}', [HealthletterController::class, 'updateSuratSehat'])->name('update');
        Route::delete('/{id}', [HealthletterController::class, 'deleteSuratSehat'])->name('delete');
        Route::get('/preview/{encryptedId}', [HealthletterController::class, 'previewSuratSehat'])->name('preview');
        Route::get('/document/{uniqueCode}', [HealthletterController::class, 'showSuratSehat'])->name('document');
        Route::get('/regenerate/{id}', [HealthletterController::class, 'regenerateSuratSehat'])->name('regenerate');
        Route::post('/sign-confirm', [HealthletterController::class, 'signConfirmSuratSehat'])->name('sign.confirm');
        Route::post('/bulk-regenerate', [HealthletterController::class, 'bulkingRegenerateSuratSehat'])->name('bulk.regenerate');
    });
    
    // ========== MEDICAL CERTIFICATES (MC - Medical Certificate) ==========
    Route::prefix('results/mc')->name('results.mc.')->group(function () {
        Route::get('/create', [HealthletterController::class, 'createSuratSakit'])->name('create');
        Route::post('/', [HealthletterController::class, 'storeSuratSakit'])->name('store');
        Route::get('/{id}', [HealthletterController::class, 'showSuratSakit'])->name('show');
        Route::get('/{id}/edit', [HealthletterController::class, 'editSuratSakit'])->name('edit');
        Route::put('/{id}', [HealthletterController::class, 'updateSuratSakit'])->name('update');
        Route::delete('/{id}', [HealthletterController::class, 'deleteSuratSakit'])->name('delete');
        Route::get('/preview/{encryptedId}', [HealthletterController::class, 'previewSuratSakit'])->name('preview');
        Route::get('/document/{uniqueCode}', [HealthletterController::class, 'showSuratSakit'])->name('document');
        Route::get('/regenerate/{id}', [HealthletterController::class, 'regenerateSuratSakit'])->name('regenerate');
        Route::post('/sign-confirm', [HealthletterController::class, 'signConfirmSuratSakit'])->name('sign.confirm');
    });
    
    // ========== LEGACY RESULTS ROUTES (BACKWARD COMPATIBILITY) ==========
    Route::prefix('results')->name('results.')->group(function () {
        Route::post('/skb', [HealthletterController::class, 'storeSuratSehat'])->name('store');
        Route::get('/preview/{id}', [HealthletterController::class, 'tesPdf'])->name('preview');
        Route::get('/document/{uniqueCode}', [HealthletterController::class, 'show'])->name('show');
        Route::get('/regenerate/{id}', [HealthletterController::class, 'regenerateDocument'])->name('regenerate');
        Route::get('/{id}/edit', [HealthletterController::class, 'edit'])->name('edit');
        Route::put('/{id}', [HealthletterController::class, 'update'])->name('update');
        Route::delete('/{id}', [HealthletterController::class, 'delete'])->name('delete');
        Route::post('/sign-confirm', [HealthletterController::class, 'signConfirm'])->name('sign.confirm');
        Route::post('/bulk-regenerate', [HealthletterController::class, 'bulkingRegenerate'])->name('bulk.regenerate');
        Route::post('/verify', [HealthletterController::class, 'checkVerification'])->name('verify');
        Route::post('/send-notif', [HealthletterController::class, 'sendNotif'])->name('sendNotif');
        Route::get('/{id}/download', [ResultController::class, 'download'])->name('download');
        Route::get('/confirm-location', [ResultController::class, 'confirmLocation'])->name('confirm-location');
        Route::get('/create/mc', [ResultController::class, 'showForm'])->name('create.mc');
    });
    
    // ========== SURAT MANAGEMENT (LEGACY) ==========
    Route::prefix('surat')->name('surat.')->group(function () {
        Route::get('/', [ResultController::class, 'index'])->name('index');
        Route::get('/create', [ResultController::class, 'create'])->name('create');
    });

    // ========== LEGACY RESULTS MANAGEMENT ==========
    Route::prefix('legacy-results')->name('legacy-results.')->group(function () {
        // Main legacy results routes
        Route::get('/', [LegacyResultController::class, 'index'])->name('index');
        Route::get('/{id}', [LegacyResultController::class, 'show'])->name('show');
        
        // Trash management
        Route::get('/trash/index', [LegacyResultController::class, 'trash'])->name('trash');
        Route::patch('/trash/{id}/restore', [LegacyResultController::class, 'restore'])->name('restore');
        Route::delete('/trash/{id}/force-delete', [LegacyResultController::class, 'forceDelete'])->name('force-delete');
        
        // Bulk operations
        Route::post('/bulk-action', [LegacyResultController::class, 'bulkAction'])->name('bulk-action');
        
        // Export and statistics
        Route::get('/export/csv', [LegacyResultController::class, 'export'])->name('export');
        Route::get('/api/statistics', [LegacyResultController::class, 'statistics'])->name('statistics');
    });
        
    // ========== REPORTS MANAGEMENT ==========
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportManagementController::class, 'index'])->name('index');
        Route::post('/export', [ReportManagementController::class, 'export'])->name('export');
        Route::get('/types/{type}', [ReportManagementController::class, 'form'])->name('form');
        Route::get('/preview', [ReportManagementController::class, 'previewData'])->name('preview');
        
        // Old reports (legacy system)
        Route::prefix('old')->name('old.')->group(function () {
            Route::get('/', [ReportManagementController::class, 'indexOld'])->name('index');
            Route::post('/export', [ReportManagementController::class, 'exportOld'])->name('export');
            Route::get('/types/{type}', [ReportManagementController::class, 'formOld'])->name('form');
            Route::get('/preview', [ReportManagementController::class, 'previewDataOld'])->name('preview');
        });
    });
    
    // ========== DOCUMENT QUEUE MANAGEMENT ==========
    Route::prefix('queue-monitor')->name('queue.')->group(function () {
        Route::get('/', [DocumentQueueController::class, 'index'])->name('index');
        Route::post('/retry/{id}', [DocumentQueueController::class, 'retry'])->name('retry');
        Route::get('/data', [DocumentQueueController::class, 'fetchData'])->name('data');
        Route::delete('/delete/{id}', [HealthletterController::class, 'destroyQueue'])->name('destroy');
    });
    
    // ========== TRASH MANAGEMENT ==========
    Route::prefix('result-trash')->name('result.trash.')->group(function () {
        Route::get('/', [HealthletterController::class, 'indexTrash'])->name('index');
        Route::post('/restore/{id}', [HealthletterController::class, 'restore'])->name('restore');
    });
    
    // ========== SESSION MANAGEMENT ==========
    Route::prefix('sessions')->name('sessions.')->group(function () {
        Route::get('/', [SessionLoginController::class, 'indexOutlet'])->name('index');
        Route::post('/{id}/logout', [SessionLoginController::class, 'forceLogoutOutlet'])->name('forceLogout');
    });
    
    // ========== PROFILE MANAGEMENT ==========
    Route::prefix('profile')->name('profile.')->group(function () {
        // Profile show route with encrypted ID (for superadmin)
        Route::get('/view/{user}', [ProfileController::class, 'show'])->name('show');
        Route::get('/sessions', [ProfileController::class, 'sessionLogs'])->name('sessions');
    });

    // ========== COMPANY MANAGEMENT ==========
    Route::post('/companies', [HealthletterController::class, 'storeCompany'])->name('companies.store');
    
    // ========== ADDITIONAL ENDPOINTS ==========
    Route::get('/doctors/by-outlet', [HealthletterController::class, 'apiGetDoctor'])->name('doctors.by.outlet');
    
    // ========== LIVE SEARCH APIs ==========
    Route::prefix('api')->name('api.')->group(function () {
        
        // Patient search
        Route::get('/patients/live-search', function (Request $request) {
            $query = $request->get('q');
            $patients = Patient::with(['user', 'company'])
                ->whereHas('user', function ($q) use ($query) {
                    $q->where('name', 'like', '%' . $query . '%');
                })
                ->limit(10)
                ->get()
                ->map(function ($p) {
                    $companyName = $p->company?->name ? ' - ' . $p->company->name : '';
                    return [
                        'id' => $p->id,
                        'name' => $p->full_name . $companyName,
                        'dob' => $p->birth_date,
                        'gender' => $p->gender,
                        'phone_number' => $p->phone,
                        'address' => $p->address,
                    ];
                });
            return response()->json($patients);
        })->name('patients.search');
        
        // Company search
        Route::get('/companies/live-search', function (Request $request) {
            $query = $request->get('q');
            $companies = Company::where('name', 'like', '%' . $query . '%')
                ->limit(10)
                ->get(['id', 'name']);
            return response()->json($companies);
        })->name('companies.search');
        
        // ICD-10 search
        Route::get('/icd10/live-search', function (Request $request) {
            $query = $request->get('q');
            $icds = IcdMaster::where('code', 'like', '%' . $query . '%')
                ->orWhere('title', 'like', '%' . $query . '%')
                ->limit(15)
                ->get(['id', 'code', 'title']);
            return response()->json($icds);
        })->name('icd10.search');
    });
});

// ==================== DOCTOR ROUTES ====================
Route::middleware(['auth', CheckRoleType::class . ':doctor'])
    ->prefix('doctor')
    ->name('doctor.')
    ->group(function () {
    
    // Dashboard
    Route::get('/dashboard', function () {
        return view('doctor.dashboard');
    })->name('dashboard');
    
    // Doctor specific routes can be added here
});

// ==================== COMPANY ROUTES ====================
Route::middleware(['auth', CheckRoleType::class . ':companies'])
    ->prefix('company')
    ->name('company.')
    ->group(function () {
    
    // ========== DASHBOARD ==========
    Route::get('/dashboard', [CompanyClientController::class, 'dashboard'])->name('dashboard');
    
    // ========== EMPLOYEE HEALTH MANAGEMENT ==========
    Route::prefix('patients')->name('patients.')->group(function () {
        Route::get('/', [CompanyClientController::class, 'patients'])->name('index');
        Route::get('/export', [CompanyClientController::class, 'exportPatients'])->name('export');
        Route::get('/{patient}', [CompanyClientController::class, 'showPatient'])->name('show');
    });
    
    // ========== HEALTH HISTORY ==========
    Route::prefix('health-history')->name('health-history.')->group(function () {
        Route::get('/', [CompanyClientController::class, 'healthHistory'])->name('index');
        Route::get('/patient/{patient}', [CompanyClientController::class, 'patientHealthHistory'])->name('patient');
        Route::get('/export', [CompanyClientController::class, 'exportHealthHistory'])->name('export');
    });
    
    // ========== HEALTH REPORTS ==========
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [CompanyClientController::class, 'reports'])->name('index');
        Route::get('/monthly', [CompanyClientController::class, 'monthlyReport'])->name('monthly');
        Route::get('/export', [CompanyClientController::class, 'exportReport'])->name('export');
        Route::get('/health-summary', [CompanyClientController::class, 'healthSummary'])->name('health-summary');
    });
    
    // ========== STATISTICS & ANALYTICS ==========
    Route::prefix('statistics')->name('statistics.')->group(function () {
        Route::get('/', [CompanyClientController::class, 'statistics'])->name('index');
        Route::get('/trends', [CompanyClientController::class, 'healthTrends'])->name('trends');
        Route::get('/api/chart-data', [CompanyClientController::class, 'getChartData'])->name('api.chart');
    });
    
    // ========== CERTIFICATES & DOCUMENTS ==========
    Route::prefix('certificates')->name('certificates.')->group(function () {
        Route::get('/skb', [CompanyClientController::class, 'healthCertificates'])->name('skb');
        Route::get('/mc', [CompanyClientController::class, 'medicalCertificates'])->name('mc');
        Route::get('/download/{result}', [CompanyClientController::class, 'downloadCertificate'])->name('download');
        Route::get('/verify/{code}', [CompanyClientController::class, 'verifyCertificate'])->name('verify');
    });
    
    // ========== OUTLET MANAGEMENT ==========
    Route::prefix('outlets')->name('outlets.')->group(function () {
        Route::get('/', [CompanyClientController::class, 'outlets'])->name('index');
        Route::get('/performance', [CompanyClientController::class, 'outletPerformance'])->name('performance');
        Route::get('/{outlet}/details', [CompanyClientController::class, 'outletDetails'])->name('details');
    });
    
    // ========== PROFILE MANAGEMENT ==========
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [CompanyClientController::class, 'profile'])->name('show');
        Route::get('/edit', [CompanyClientController::class, 'editProfile'])->name('edit');
        Route::put('/update', [CompanyClientController::class, 'updateProfile'])->name('update');
    });
    
    // ========== SETTINGS ==========
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [CompanyClientController::class, 'settings'])->name('index');
        Route::get('/notifications', [CompanyClientController::class, 'notifications'])->name('notifications');
        Route::put('/notifications', [CompanyClientController::class, 'updateNotifications'])->name('notifications.update');
        Route::get('/security', [CompanyClientController::class, 'security'])->name('security');
        Route::put('/password', [CompanyClientController::class, 'updatePassword'])->name('password');
    });
    
    // ========== SUPPORT ==========
    Route::prefix('support')->name('support.')->group(function () {
        Route::get('/', [CompanyClientController::class, 'support'])->name('index');
        Route::get('/faq', [CompanyClientController::class, 'faq'])->name('faq');
        Route::get('/contact', [CompanyClientController::class, 'contact'])->name('contact');
        Route::post('/ticket', [CompanyClientController::class, 'createTicket'])->name('ticket');
    });
    
    // ========== API ENDPOINTS ==========
    Route::prefix('api')->name('api.')->group(function () {
        Route::get('/dashboard-stats', [CompanyClientController::class, 'getDashboardStats'])->name('dashboard.stats');
        Route::get('/patient-health/{patient}', [CompanyClientController::class, 'getPatientHealth'])->name('patient.health');
        Route::get('/health-trends', [CompanyClientController::class, 'getHealthTrends'])->name('health.trends');
        Route::get('/outlet-stats', [CompanyClientController::class, 'getOutletStats'])->name('outlet.stats');
    });
});

// ==================== PATIENT ROUTES ====================
Route::middleware(['auth', CheckRoleType::class . ':patient'])
    ->prefix('patient')
    ->name('patient.')
    ->group(function () {
    
    // Dashboard
    Route::get('/dashboard', function () {
        return view('patient.dashboard');
    })->name('dashboard');
    
    // Patient specific routes can be added here
});