<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Models\Patient;
use App\Http\Controllers\Api\PatientSearchController;
use App\Http\Controllers\Api\CertificateController;

// Public API (no auth required)
Route::get('/patients/search', function (Request $request) {
    $query = $request->q;
    return Patient::select('id', 'name', 'gender', 'phone as phone_number', 'birth_date as dob', 'address')
        ->where('name', 'like', "%{$query}%")
        ->limit(10)
        ->get();
});

// Protected API Routes (Sanctum auth required)
Route::middleware('auth:sanctum')->prefix('v1')->group(function () {

    // Certificate Management API
    Route::prefix('certificates')->name('api.certificates.')->group(function () {
        // List certificates with pagination & search
        Route::get('/', [CertificateController::class, 'index'])->name('index');

        // Search certificates
        Route::get('/search', [CertificateController::class, 'search'])->name('search');

        // Storage info
        Route::get('/storage/info', [CertificateController::class, 'storageInfo'])->name('storage.info');

        // Single certificate operations
        Route::get('/{id}', [CertificateController::class, 'show'])->name('show');
        Route::get('/{id}/download', [CertificateController::class, 'download'])->name('download');
        Route::get('/{id}/pdf-url', [CertificateController::class, 'pdfUrl'])->name('pdf-url');
    });

    // User info endpoint
    Route::get('/user', function (Request $request) {
        return response()->json([
            'success' => true,
            'data' => $request->user(),
        ]);
    });
});

