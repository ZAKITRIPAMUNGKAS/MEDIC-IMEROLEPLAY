<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\Api\OperationRecordApiController;
use App\Http\Controllers\Api\MedicalFormApiController;

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

/*
|--------------------------------------------------------------------------
| API Rekam Medis — Operation Records
|--------------------------------------------------------------------------
| Semua endpoint dilindungi dengan API Key (header X-API-Key).
| Contoh: X-API-Key: <nilai API_KEY di .env>
*/
Route::middleware(['api.auth', 'api.rate_limit:60,1'])->prefix('rekam-medis')->group(function () {
    // Daftar semua rekam operasi (dengan filter opsional)
    Route::get('/',              [OperationRecordApiController::class, 'index']);

    // Detail satu rekam operasi
    Route::get('/{id}',         [OperationRecordApiController::class, 'show'])->where('id', '[0-9]+');

    // Riwayat rekam operasi berdasarkan citizen_id pasien
    Route::get('/pasien/{citizen_id}', [OperationRecordApiController::class, 'byPatient']);

    // Buat rekam operasi baru
    Route::post('/',             [OperationRecordApiController::class, 'store']);

    // Update rekam operasi
    Route::put('/{id}',         [OperationRecordApiController::class, 'update'])->where('id', '[0-9]+');

    // Hapus rekam operasi
    Route::delete('/{id}',      [OperationRecordApiController::class, 'destroy'])->where('id', '[0-9]+');
});

/*
|--------------------------------------------------------------------------
| API Form Medis / Rekam Medis Pasien — Medical Forms
|--------------------------------------------------------------------------
*/
Route::middleware(['api.auth', 'api.rate_limit:60,1'])->prefix('medical-forms')->group(function () {
    // Daftar semua form medis (dengan filter opsional)
    Route::get('/',                        [MedicalFormApiController::class, 'index']);

    // Testimoni pasien yang sudah disetujui — letakkan SEBELUM /{id}
    Route::get('/testimoni',               [MedicalFormApiController::class, 'testimoni']);

    // Riwayat form medis berdasarkan citizen_id pasien — letakkan SEBELUM /{id}
    Route::get('/pasien/{citizen_id}',     [MedicalFormApiController::class, 'byPatient']);

    // Detail satu form medis
    Route::get('/{id}',                    [MedicalFormApiController::class, 'show'])->where('id', '[0-9]+');

    // Buat form medis baru (dari website lain)
    Route::post('/',                       [MedicalFormApiController::class, 'store']);

    // Update status form medis (approve / reject)
    Route::patch('/{id}/status',           [MedicalFormApiController::class, 'updateStatus'])->where('id', '[0-9]+');

    // Hapus form medis
    Route::delete('/{id}',                 [MedicalFormApiController::class, 'destroy'])->where('id', '[0-9]+');
});
