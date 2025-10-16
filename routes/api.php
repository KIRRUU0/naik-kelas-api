<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WebinarController;
use App\Http\Controllers\LayananBisnisController; // KOREKSI: Menggantikan ModulBisnisController
use App\Http\Controllers\PenggunaController;
use App\Http\Controllers\LayananUmumController;
use App\Http\Controllers\LowonganKarirController;
use App\Http\Controllers\PaketKemitraanController;
use App\Http\Controllers\MitraBrokerController;
use App\Http\Controllers\HomeController; 
use App\Http\Controllers\TentangController;

/*
|--------------------------------------------------------------------------
| API Routes: Diatur berdasarkan Level Keamanan
|--------------------------------------------------------------------------
*/

// =========================================================================
// 1. RUTE PUBLIK (Dapat diakses tanpa login)
// =========================================================================

// Rute Pengaturan HOME (GET)
Route::get('home', [HomeController::class, 'index']); 
Route::get('tentang', [TentangController::class, 'index']);

// RUTE PENDAFTARAN: POST Pengguna harus PUBLIK
Route::post('pengguna', [PenggunaController::class, 'store']); 

// Rute Spesifik (Statistik)
Route::get('webinar/statistik', [WebinarController::class, 'statistik']);

// Rute READ Publik (GET index & show)
Route::apiResource('webinar', WebinarController::class)->only(['index', 'show']);
Route::apiResource('layanan-bisnis', LayananBisnisController::class)->only(['index', 'show']); // KOREKSI NAMA RUTE
Route::apiResource('layanan-umum', LayananUmumController::class)->only(['index', 'show']);
Route::apiResource('lowongan-karir', LowonganKarirController::class)->only(['index', 'show']);
Route::apiResource('pengguna', PenggunaController::class)->only(['index', 'show']);


// =========================================================================
// 2. RUTE TERLINDUNGI (Membutuhkan Login Admin / auth:sanctum)
// =========================================================================

Route::middleware('auth:sanctum')->group(function () {
    
    // Rute default user Sanctum
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    
    // RUTE HOME UPDATE
    Route::post('home', [HomeController::class, 'updateOrCreate']); 
    
    // Rute CUD (Create, Update, Delete)
    Route::apiResource('webinar', WebinarController::class)->except(['index', 'show']);
    Route::apiResource('layanan-bisnis', LayananBisnisController::class)->except(['index', 'show']); // KOREKSI NAMA RUTE
    Route::apiResource('layanan-umum', LayananUmumController::class)->except(['index', 'show']);
    Route::apiResource('lowongan-karir', LowonganKarirController::class)->except(['index', 'show']);
    
    // UPDATE dan DELETE pengguna
    Route::apiResource('pengguna', PenggunaController::class)->except(['index', 'show', 'store']);
    
    // Rute CRUD Penuh untuk resource tersisa
    Route::apiResource('paket-kemitraan', PaketKemitraanController::class);
    Route::apiResource('mitra-broker', MitraBrokerController::class);
});