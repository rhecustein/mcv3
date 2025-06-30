<?php
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
use App\Http\Controllers\StatisticsController;
use App\Http\Controllers\StatisticsManagementController;
use App\Http\Controllers\PatientController;
use App\Http\Middleware\CheckRoleType;
use App\Http\Controllers\DoctorOutletController;
use App\Http\Controllers\HealthletterController;
use App\Http\Controllers\DocumentQueueController;
use App\Http\Controllers\PublicResultController;
use App\Http\Controllers\ReportManagementController;
use App\Http\Controllers\NotificationController;
use App\Models\Patient;
use App\Models\Company;
use App\Models\IcdMaster;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use LDAP\Result;

Route::get('/', function () {
    return view('welcome');
});

//verify
Route::get('/verify/{code}', [PublicResultController::class, 'verify'])->name('result.verify');


//static route semua role kecuali guest guankan StatisticsController

Route::middleware(['auth', EnsureSingleSession::class])->group(function () {
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/header/notifications', [NotificationController::class, 'fetchHeaderNotifications'])
    ->middleware('auth')
    ->name('header.notifications');

Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
Route::get('/profile/{user}', [ProfileController::class, 'show'])->name('profile.show');
Route::get('/profile/sessions', [ProfileController::class, 'sessionLogs'])->name('profile.sessions');



// Group route setting semua role
Route::middleware(['auth'])->group(function () {
    Route::get('/settings', [ProfileController::class, 'settings'])->name('settings.index');
    Route::get('/settings/activity', [ProfileController::class, 'activity'])->name('settings.activity');
    Route::get('/settings/session', [ProfileController::class, 'session'])->name('settings.session');
    Route::get('/settings/notifications', [ProfileController::class, 'notifications'])->name('settings.notifications');
    Route::put('/settings/notifications', [ProfileController::class, 'updateNotifications'])->name('settings.notifications.update');
    Route::get('/settings/profile', [ProfileController::class, 'edit'])->name('settings.profile.edit');
    Route::put('/settings/profile', [ProfileController::class, 'update'])->name('settings.profile.update');
    Route::put('/settings/profile/password', [ProfileController::class, 'updatePassword'])->name('settings.profile.password');
});

// Group route untuk superadmin
Route::middleware(['auth', 'can:isSuperadmin'])->prefix('superadmin')->name('admins.')->group(function () {
    Route::get('/admins', [AdminManagementController::class, 'index'])->name('index');
    Route::get('/admins/create', [AdminManagementController::class, 'create'])->name('create');
    Route::post('/admins', [AdminManagementController::class, 'store'])->name('store');
    Route::get('/admins/{admin}/edit', [AdminManagementController::class, 'edit'])->name('edit');
    Route::put('/admins/{admin}', [AdminManagementController::class, 'update'])->name('update');
    Route::delete('/admins/{admin}', [AdminManagementController::class, 'destroy'])->name('destroy');
    Route::post('/admins/{user}/ban', [AdminManagementController::class, 'ban'])->name('ban');
    Route::post('/admins/{user}/unban', [AdminManagementController::class, 'unban'])->name('unban');
    //dashboard
    Route::get('/dashboard', [AdminManagementController::class, 'dashboard'])->name('dashboard');
});

// Group route untuk superadmin - manajemen outlet
Route::middleware(['auth', 'can:isSuperadmin'])->prefix('superadmin')->name('outlets.')->group(function () {
    Route::get('/outlets', [OutletController::class, 'index'])->name('index');
    Route::get('/outlets/create', [OutletController::class, 'create'])->name('create');
    Route::post('/outlets', [OutletController::class, 'store'])->name('store');
    Route::get('/outlets/{outlet}/edit', [OutletController::class, 'edit'])->name('edit');
    Route::put('/outlets/{outlet}', [OutletController::class, 'update'])->name('update');
    Route::delete('/outlets/{outlet}', [OutletController::class, 'destroy'])->name('destroy');
    Route::patch('/outlets/{outlet}/toggle', [OutletController::class, 'toggleActive'])->name('toggle');
    Route::post('/outlets/{outlet}/reset-password', [OutletController::class, 'resetPassword'])->name('reset-password');
});

// Group route untuk superadmin - manajemen dokter
Route::middleware(['auth', 'can:isSuperadmin'])->prefix('superadmin')->name('doctors.')->group(function () {
    Route::get('/doctors', [DoctorManagementController::class, 'index'])->name('index');
    Route::get('/doctors/create', [DoctorManagementController::class, 'create'])->name('create');
    Route::post('/doctors', [DoctorManagementController::class, 'store'])->name('store');
    Route::get('/doctors/{doctor}/edit', [DoctorManagementController::class, 'edit'])->name('edit');
    Route::put('/doctors/{doctor}', [DoctorManagementController::class, 'update'])->name('update');
    Route::delete('/doctors/{doctor}', [DoctorManagementController::class, 'destroy'])->name('destroy');
    Route::post('/doctors/{doctor}/ban', [DoctorManagementController::class, 'ban'])->name('ban');
    Route::post('/doctors/{doctor}/unban', [DoctorManagementController::class, 'unban'])->name('unban');
    Route::post('/doctors/{doctor}/reset-password', [DoctorManagementController::class, 'resetPassword'])->name('resetPassword');
});

// Group route untuk superadmin - manajemen Company
Route::middleware(['auth', 'can:isSuperadmin'])->prefix('superadmin')->name('companies.')->group(function () {
    Route::get('/companies', [CompanyController::class, 'index'])->name('index');
    Route::get('/companies/create', [CompanyController::class, 'create'])->name('create');
    Route::post('/companies', [CompanyController::class, 'store'])->name('store');
    Route::get('/companies/{company}/edit', [CompanyController::class, 'edit'])->name('edit');
    Route::put('/companies/{company}', [CompanyController::class, 'update'])->name('update');
    Route::delete('/companies/{company}', [CompanyController::class, 'destroy'])->name('destroy');
});

// Group route untuk superadmin - manajemen Template Result
Route::middleware(['auth', 'can:isSuperadmin'])->prefix('superadmin')->name('template-results.')->group(function () {
    Route::get('/template-results', [\App\Http\Controllers\TemplateResultController::class, 'index'])->name('index');
    Route::get('/template-results/create', [\App\Http\Controllers\TemplateResultController::class, 'create'])->name('create');
    Route::post('/template-results', [\App\Http\Controllers\TemplateResultController::class, 'store'])->name('store');
    Route::get('/template-results/{templateResult}/edit', [\App\Http\Controllers\TemplateResultController::class, 'edit'])->name('edit');
    Route::put('/template-results/{templateResult}', [\App\Http\Controllers\TemplateResultController::class, 'update'])->name('update');
    Route::delete('/template-results/{templateResult}', [\App\Http\Controllers\TemplateResultController::class, 'destroy'])->name('destroy');
});

// Group route untuk superadmin - statistik
Route::middleware(['auth', 'can:isSuperadmin'])->prefix('superadmin')->name('statistics.')->group(function () {
    Route::get('/statistics', [\App\Http\Controllers\StatisticsController::class, 'index'])->name('index');
    Route::get('/statistics/leaderboard', [\App\Http\Controllers\StatisticsController::class, 'leaderboard'])->name('leaderboard');
    Route::get('/api/leaderboard', [StatisticsController::class, 'getLeaderboard'])->name('api.leaderboard');
});

// Group route untuk superadmin - manajemen paket
Route::middleware(['auth', 'can:isSuperadmin'])->prefix('superadmin')->name('packages.')->group(function () {
    Route::get('/packages', [\App\Http\Controllers\PackageController::class, 'index'])->name('index');
    Route::get('/packages/create', [\App\Http\Controllers\PackageController::class, 'create'])->name('create');
    Route::post('/packages', [\App\Http\Controllers\PackageController::class, 'store'])->name('store');
    Route::get('/packages/{package}/edit', [\App\Http\Controllers\PackageController::class, 'edit'])->name('edit');
    Route::put('/packages/{package}', [\App\Http\Controllers\PackageController::class, 'update'])->name('update');
    Route::delete('/packages/{package}', [\App\Http\Controllers\PackageController::class, 'destroy'])->name('destroy');
});

// Group route untuk superadmin - manajemen transaksi paket
Route::middleware(['auth', 'can:isSuperadmin'])->prefix('superadmin')->name('package-transactions.')->group(function () {
    Route::get('/package-transactions', [\App\Http\Controllers\PackageTransactionController::class, 'index'])->name('index');
    Route::get('/package-transactions/{packageTransaction}', [\App\Http\Controllers\PackageTransactionController::class, 'show'])->name('show');
    Route::delete('/package-transactions/{packageTransaction}', [\App\Http\Controllers\PackageTransactionController::class, 'destroy'])->name('destroy');
});

// Group route untuk superadmin - manajemen session login
Route::prefix('superadmin/session-logins')->name('session-logins.')->group(function () {
    Route::get('/', [SessionLoginController::class, 'index'])->name('index');                     // ✅
    Route::get('/{sessionLogin}', [SessionLoginController::class, 'show'])->name('show');         // ✅
    Route::delete('/{sessionLogin}', [SessionLoginController::class, 'destroy'])->name('destroy'); // ✅
    Route::post('/logout', [SessionLoginController::class, 'logout'])->name('logout');             // ✅
    Route::post('/logout-all', [SessionLoginController::class, 'logoutAll'])->name('logoutAll');   // ✅
    Route::post('/logout-other', [SessionLoginController::class, 'logoutOther'])->name('logoutOther'); // ✅
    Route::post('/block-ip', [SessionLoginController::class, 'blockIp'])->name('block');       // ✅
    Route::post('/unblock-ip', [SessionLoginController::class, 'unblockIp'])->name('unblock'); // ✅
});


Route::prefix('outlet')
    ->middleware(['auth', CheckRoleType::class . ':outlet'])
    ->name('outlet.')
    ->group(function () {
        Route::get('/dashboard', [OutletController::class, 'dashboard'])->name('dashboard');
        Route::get('/home', [OutletController::class, 'home'])->name('home');
        Route::resource('doctors', DoctorOutletController::class)->except(['show']);
        Route::get('/pasien', [PatientController::class, 'index'])->name('patients.index');
        Route::get('/statistics', [StatisticsManagementController::class, 'index'])->name('statistics.index');
        //reports
        Route::get('/reports', [ReportManagementController::class, 'index'])->name('reports.index');
        Route::post('/reports/export', [ReportManagementController::class, 'export'])->name('reports.export');
        Route::get('/reports/types/{type}', [ReportManagementController::class, 'form'])->name('reports.form');
        Route::get('/reports/preview', [ReportManagementController::class, 'previewData'])->name('reports.preview');
        //reports old
        Route::get('/reports/old', [ReportManagementController::class, 'indexOld'])->name('reports.old');
        Route::post('/reports/old/export', [ReportManagementController::class, 'exportOld'])->name('reports.old.export');
        Route::get('/reports/old/types/{type}', [ReportManagementController::class, 'formOld'])->name('reports.old.form');
        Route::get('/reports/old/preview', [ReportManagementController::class, 'previewDataOld'])->name('reports.old.preview');
        
        Route::get('/healthletters', [HealthletterController::class, 'index'])->name('healthletter.index');
        Route::get('/results/skb/create', [HealthletterController::class, 'createSuratSehat'])->name('results.skb.create');
        Route::get('/results/mc/create', [HealthletterController::class, 'createSuratSakit'])->name('results.mc.create');
        Route::post('/results/skb', [HealthletterController::class, 'storeSuratSehat'])->name('results.store');
        Route::get('/results/preview/{id}', [HealthletterController::class, 'tesPdf'])->name('results.preview');
        Route::get('/results/document/{uniqueCode}', [HealthletterController::class, 'show'])->name('result.show');
        Route::get('/results/regenerate/{id}', [HealthletterController::class, 'regenerateDocument'])->name('results.regenerate');
        Route::get('/results/{id}/edit', [HealthletterController::class, 'edit'])->name('results.edit');
        Route::put('/results/{id}', [HealthletterController::class, 'update'])->name('results.update');
        Route::delete('/results/{id}', [HealthletterController::class, 'delete'])->name('results.delete');
        Route::post('/results/sign-confirm', [HealthletterController::class, 'signConfirm'])->name('results.sign.confirm');
        Route::post('/results/bulk-regenerate', [HealthletterController::class, 'bulkingRegenerate'])->name('results.bulk.regenerate');
        Route::post('/results/verify', [HealthletterController::class, 'checkVerification'])->name('results.verify');
        Route::get('/doctors/by-outlet', [HealthletterController::class, 'apiGetDoctor'])->name('doctors.by.outlet');

        Route::get('/results/{id}/download', [ResultController::class, 'download'])->name('results.download');
        Route::get('/results/confirm-location', [ResultController::class, 'confirmLocation'])->name('results.confirm-location');
        Route::get('/results/create/mc', [ResultController::class, 'showForm'])->name('results.create.mc');
        Route::get('/surat', [ResultController::class, 'index'])->name('surat.index');
        Route::get('/surat/create', [ResultController::class, 'create'])->name('surat.create');

        // show results
        Route::get('/results/skb/{id}', [HealthletterController::class, 'showSuratSehat'])->name('results.skb.show');
        Route::get('/results/mc/{id}', [HealthletterController::class, 'showSuratSakit'])->name('results.mc.show');

        // skb & mc
       Route::get('/patients/live-search', function (Request $request) {
            $query = $request->get('q');
            $patients = Patient::with(['user', 'company']) // pastikan ada relasi ke company
                ->whereHas('user', function ($q) use ($query) {
                    $q->where('name', 'like', '%' . $query . '%');
                })
                ->limit(10)
                ->get()
                ->map(function ($p) {
                    $companyName = $p->company?->name ? ' - ' . $p->company->name : '';
                    return [
                        'id' => $p->id,
                        'name' => $p->full_name . $companyName, // "Pasien - Perusahaan"
                        'dob' => $p->birth_date,
                        'gender' => $p->gender,
                        'phone_number' => $p->phone,
                        'address' => $p->address,
                    ];
                });

            return response()->json($patients);
        });

        Route::get('/companies/live-search', function (\Illuminate\Http\Request $request) {
            $query = $request->get('q');

            $companies = Company::where('name', 'like', '%' . $query . '%')
                ->limit(10)
                ->get(['id', 'name']);

            return response()->json($companies);
        });

        Route::get('/icd10/live-search', function (Request $request) {
            $query = $request->get('q');

            $icds = IcdMaster::where('code', 'like', '%' . $query . '%')
                ->orWhere('title', 'like', '%' . $query . '%')
                ->limit(15)
                ->get(['id', 'code', 'title']);

            return response()->json($icds);
        });

        //storeCompany
        Route::post('/companies', [HealthletterController::class, 'storeCompany'])->name('companies.store');

        // Route untuk form input surat sehat (SKB)
        Route::get('/results/skb/create', [HealthletterController::class, 'createSuratSehat'])->name('results.skb.create');

        // Route untuk form input surat sakit (MC)
        Route::get('/results/mc/create', [HealthletterController::class, 'createSuratSakit'])->name('results.mc.create');

        //Route [outlet.results.sendNotif] not defined.
        Route::post('/results/send-notif', [HealthletterController::class, 'sendNotif'])->name('results.sendNotif');

        // Route untuk antrian genarasi surat sehat (SKB)
        Route::get('/queue-monitor', [DocumentQueueController::class, 'index'])->name('queue.index');
        Route::post('/queue-monitor/retry/{id}', [DocumentQueueController::class, 'retry'])->name('queue.retry');
        Route::get('/queue-monitor/data', [DocumentQueueController::class, 'fetchData'])->name('queue.data');

        //results.store
        Route::post('/results/skb', [HealthletterController::class, 'storeSuratSehat'])->name('results.store.skb');
        Route::post('/results/mc', [HealthletterController::class, 'storeSuratSakit'])->name('results.store.mc');
        
        Route::get('/results/skb/preview/{encryptedId}', [HealthletterController::class, 'previewSuratSehat'])->name('results.skb.preview');
        Route::get('/results/mc/preview/{encryptedId}', [HealthletterController::class, 'previewSuratSakit'])->name('results.mc.preview');


        Route::get('/results/skb/document/{uniqueCode}', [HealthletterController::class, 'showSuratSehat'])->name('results.skb.document');
        Route::get('/results/mc/document/{uniqueCode}', [HealthletterController::class, 'showSuratSakit'])->name('results.mc.document');

        Route::get('/results/skb/regenerate/{id}', [HealthletterController::class, 'regenerateSuratSehat'])->name('results.skb.regenerate');
        Route::get('/results/mc/regenerate/{id}', [HealthletterController::class, 'regenerateSuratSakit'])->name('results.mc.regenerate');
        Route::get('/results/skb/{id}/edit', [HealthletterController::class, 'editSuratSehat'])->name('results.skb.edit');
        Route::get('/results/mc/{id}/edit', [HealthletterController::class, 'editSuratSakit'])->name('results.mc.edit');

        Route::put('/results/skb/{id}', [HealthletterController::class, 'updateSuratSehat'])->name('results.skb.update');
        Route::put('/results/mc/{id}', [HealthletterController::class, 'updateSuratSakit'])->name('results.mc.update');

        Route::delete('/results/skb/{id}', [HealthletterController::class, 'deleteSuratSehat'])->name('results.skb.delete');
        Route::delete('/results/mc/{id}', [HealthletterController::class, 'deleteSuratSakit'])->name('results.mc.delete');

        Route::post('/results/skb/sign-confirm', [HealthletterController::class, 'signConfirmSuratSehat'])->name('results.skb.sign.confirm');
        Route::post('/results/mc/sign-confirm', [HealthletterController::class, 'signConfirmSuratSakit'])->name('results.mc.sign.confirm');

        Route::post('/results/skb/bulk-regenerate', [HealthletterController::class, 'bulkingRegenerateSuratSehat'])->name('results.skb.bulk.regenerate');

        Route::delete('/queue-monitor/delete/{id}', [HealthletterController::class, 'destroyQueue'])->name('queue.destroy');

        //trash
        Route::get('/result-trash', [HealthletterController::class, 'indexTrash'])->name('result.trash.index');
        Route::post('/result-trash/restore/{id}', [HealthletterController::class, 'restore'])->name('result.trash.restore');

        //session
        Route::get('/sessions', [SessionLoginController::class, 'indexOutlet'])->name('sessions.index');
        Route::post('/sessions/{id}/logout', [SessionLoginController::class, 'forceLogoutOutlet'])->name('sessions.forceLogout');

    });
});
require __DIR__.'/auth.php';
