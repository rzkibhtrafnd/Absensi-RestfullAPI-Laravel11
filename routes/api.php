<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\PegawaiController;
use App\Http\Controllers\API\AbsensiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Public routes
Route::post('/login', [AuthController::class, 'login']);
Route::get('/qr-latest', [AbsensiController::class, 'getLatestQr']);

// Authenticated routes
Route::middleware('auth:sanctum')->group(function () {
    // Authentication
    Route::post('/logout', [AuthController::class, 'logout']);

    // Admin routes
    Route::middleware('role:admin')->prefix('admin')->group(function () {
        // Dashboard
        Route::get('/dashboard', fn () => response()->json(['message' => 'Admin dashboard']));
        
        // Pegawai management
        Route::prefix('pegawai')->controller(PegawaiController::class)->group(function () {
            Route::get('/', 'index');
            Route::post('/', 'store');
            Route::get('/{id}', 'show');
            Route::put('/{id}', 'update');
            Route::delete('/{id}', 'destroy');
            Route::get('/search', 'search');
            Route::get('/filter/role', 'filterByRole');
        });
        
        // Absensi settings
        Route::prefix('absensi')->controller(AbsensiController::class)->group(function () {
            Route::get('/settings', 'getSettings');
            Route::put('/settings', 'updateSettings');
        });
    });

    // HR routes
    Route::middleware('role:hr')->prefix('hr')->group(function () {
        // Pegawai management
        Route::prefix('pegawai')->controller(PegawaiController::class)->group(function () {
            Route::get('/', 'index');
            Route::post('/', 'store');
            Route::get('/{id}', 'show');
            Route::put('/{id}', 'update');
            Route::delete('/{id}', 'destroy');
            Route::get('/search', 'search');
            Route::get('/filter/role', 'filterByRole');
        });
        
        // Absensi management
        Route::prefix('absensi')->controller(AbsensiController::class)->group(function () {
            Route::get('/', 'listAbsensi');
            Route::post('/scan-qr', 'scanQr');
            Route::post('/ajukan', 'ajukanAbsensi');
            Route::get('/riwayat', 'riwayatAbsensi');
            Route::post('/{id}/approve', 'approveAbsensi');
            Route::post('/{id}/reject', 'rejectAbsensi');
            Route::get('/riwayat-pengajuan', 'riwayatPengajuan');
            Route::get('/settings', 'getSettings');
            Route::put('/settings', 'updateSettings');
        });
    });
    
    // Pegawai routes
    Route::middleware('role:pegawai')->prefix('pegawai')->group(function () {
        // Absensi operations
        Route::prefix('absensi')->controller(AbsensiController::class)->group(function () {
            Route::post('/scan-qr', 'scanQr');
            Route::post('/ajukan', 'ajukanAbsensi');
            Route::get('/riwayat', 'riwayatAbsensi');
            Route::get('/riwayat-pengajuan', 'riwayatPengajuan');
        });
    });
});