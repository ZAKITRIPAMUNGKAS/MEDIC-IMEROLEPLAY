<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AbsensiController;

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

// API untuk absensi otomatis dari FiveM
// Endpoint utama untuk menerima data absensi dari FiveM (dengan autentikasi)
Route::post('/absensi', [AbsensiController::class, 'store'])
    ->middleware(['api.auth', 'api.rate_limit:30,1']); // 30 requests per minute

// Endpoint untuk mendapatkan data absensi (dengan filter) - tanpa auth untuk internal use
Route::get('/absensi', [AbsensiController::class, 'index'])
    ->middleware(['api.rate_limit:60,1']); // 60 requests per minute

// Endpoint untuk cek status absensi player - tanpa auth untuk internal use
Route::get('/absensi/status/{player_id}', [AbsensiController::class, 'status'])
    ->middleware(['api.rate_limit:60,1']);

// Endpoint untuk monitoring real-time (siapa yang on duty)
Route::get('/absensi/on-duty', [AbsensiController::class, 'onDuty'])
    ->middleware(['api.rate_limit:30,1']);

// Endpoint untuk rekap jam kerja
Route::get('/absensi/report/{player_id}', [AbsensiController::class, 'report'])
    ->middleware(['api.rate_limit:30,1']);

// Test route untuk API
Route::get('/test', function () {
    return response()->json([
        'success' => true,
        'message' => 'API berjalan dengan baik!',
        'timestamp' => now(),
        'version' => '1.0.0'
    ]);
});
