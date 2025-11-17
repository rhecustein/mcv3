<?php

use App\Http\Controllers\Api\V1\PatientController;
use App\Http\Controllers\Api\V1\PsychologistController;
use App\Http\Controllers\Api\V1\PsychologySessionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Health check endpoint (no auth required)
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now()->toISOString(),
        'version' => config('app.version', '1.0.0'),
    ]);
})->name('api.health');

// API Version 1
Route::prefix('v1')
    ->name('api.v1.')
    ->middleware(['throttle:api'])
    ->group(function () {

        // Public routes (no authentication required)
        Route::prefix('psychologists')->name('psychologists.')->group(function () {
            Route::get('/', [PsychologistController::class, 'index'])->name('index');
            Route::get('/{psychologist}', [PsychologistController::class, 'show'])->name('show');
            Route::get('/{psychologist}/availability', [PsychologistController::class, 'availability'])->name('availability');
        });

        // Protected routes (authentication required)
        Route::middleware(['auth:sanctum'])->group(function () {

            // User info
            Route::get('/user', function (Request $request) {
                return response()->json($request->user());
            })->name('user');

            // Patient endpoints
            Route::prefix('patients')->name('patients.')->group(function () {
                Route::get('/search', [PatientController::class, 'search'])->name('search');
                Route::get('/{patient}', [PatientController::class, 'show'])->name('show');
            });

            // Psychology Sessions
            Route::prefix('psychology/sessions')->name('psychology.sessions.')->group(function () {
                Route::get('/', [PsychologySessionController::class, 'index'])->name('index');
                Route::post('/', [PsychologySessionController::class, 'store'])->name('store');
                Route::get('/{session}', [PsychologySessionController::class, 'show'])->name('show');
                Route::post('/{session}/cancel', [PsychologySessionController::class, 'cancel'])->name('cancel');
                Route::post('/{session}/rate', [PsychologySessionController::class, 'rate'])->name('rate');
            });
        });
    });

/*
|--------------------------------------------------------------------------
| Legacy Routes (To be migrated to V1)
|--------------------------------------------------------------------------
*/

// Keep old route for backward compatibility, but mark as deprecated
Route::get('/patients/search', function (Request $request) {
    return response()->json([
        'error' => 'This endpoint is deprecated. Please use /api/v1/patients/search instead.',
        'deprecated_at' => '2025-11-17',
        'sunset_date' => '2025-12-31',
        'new_endpoint' => '/api/v1/patients/search',
    ], 410); // 410 Gone
})->name('patients.search.deprecated');
