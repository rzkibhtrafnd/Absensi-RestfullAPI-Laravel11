<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\PegawaiController;
use App\Http\Controllers\API\AbsensiController;

Route::post('/login', [AuthController::class, 'login']);
Route::get('/qr-latest', [AbsensiController::class, 'getLatestQr']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    // Admin
    Route::middleware('role:admin')->prefix('admin')->group(function () {
        Route::get('/dashboard', fn () => response()->json(['message' => 'Admin dashboard']));
        Route::apiResource('pegawai', PegawaiController::class)->except(['create', 'edit']);
        Route::get('pegawai/search', [PegawaiController::class, 'search']);
        Route::get('pegawai/filter/role', [PegawaiController::class, 'filterByRole']);
        Route::get('absensi/settings', [AbsensiController::class, 'getSettings']);
        Route::put('absensi/settings', [AbsensiController::class, 'updateSettings']);
    });

    // HR (Manager)
    Route::middleware('role:hr')->prefix('hr')->group(function () {
        Route::apiResource('pegawai', PegawaiController::class)->except(['create', 'edit']);
        Route::get('pegawai/search', [PegawaiController::class, 'search']);
        Route::get('pegawai/filter/role', [PegawaiController::class, 'filterByRole']);
        Route::get('absensi', [AbsensiController::class, 'listAbsensi']);
    
        Route::prefix('absensi')->controller(AbsensiController::class)->group(function () {
            Route::post('scan-qr', 'scanQr');
            Route::post('ajukan', 'ajukanAbsensi');
            Route::get('riwayat', 'riwayatAbsensi');
            Route::post('{id}/approve', 'approveAbsensi');
            Route::post('{id}/reject', 'rejectAbsensi');
            Route::get('riwayat-pengajuan', 'riwayatPengajuan');
        });
    });
    
    // Pegawai
    Route::middleware('role:pegawai')->prefix('pegawai')->group(function () {
        Route::prefix('absensi')->controller(AbsensiController::class)->group(function () {
            Route::post('scan-qr', 'scanQr');
            Route::post('ajukan', 'ajukanAbsensi');
            Route::get('riwayat', 'riwayatAbsensi');
            Route::get('riwayat-pengajuan', 'riwayatPengajuan');
        });
    });
});
