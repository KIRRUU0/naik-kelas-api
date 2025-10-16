<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WebinarController;
use App\Http\Controllers\LayananBisnisController;
use App\Http\Controllers\PenggunaController;
use App\Http\Controllers\LayananUmumController;
use App\Http\Controllers\LowonganKarirController;
use App\Http\Controllers\PaketKemitraanController;
use App\Http\Controllers\MitraBrokerController;
use App\Http\Controllers\HomeController; 
use App\Http\Controllers\TentangController;
use App\Http\Controllers\KategoriBisnisController;

/*
|--------------------------------------------------------------------------
| API Routes: Diatur berdasarkan Level Keamanan
|--------------------------------------------------------------------------
*/

// =========================================================================
// 1. RUTE PUBLIK (Dapat diakses tanpa login)
// =========================================================================

// Rute Pengaturan HOME & TENTANG (GET)
Route::get('home', [HomeController::class, 'index']); 
Route::get('tentang', [TentangController::class, 'index']);

// RUTE PENDAFTARAN: POST Pengguna (Registration)
Route::post('pengguna', [PenggunaController::class, 'store']); 

// Rute Spesifik (Statistik)
Route::get('webinar/statistik', [WebinarController::class, 'statistik']);

// Rute READ Publik (GET index & show) - Hanya rute yang memang untuk publik
Route::apiResource('webinar', WebinarController::class)->only(['index', 'show']);
Route::apiResource('kategori-bisnis', KategoriBisnisController::class)->only(['index', 'show']);
Route::apiResource('layanan-bisnis', LayananBisnisController::class)->only(['index', 'show']);
Route::apiResource('layanan-umum', LayananUmumController::class)->only(['index', 'show']);
Route::apiResource('lowongan-karir', LowonganKarirController::class)->only(['index', 'show']);
Route::apiResource('mitra-broker', MitraBrokerController::class)->only(['index', 'show']);
Route::apiResource('paket-kemitraan', PaketKemitraanController::class)->only(['index', 'show']);
Route::apiResource('tentang', TentangController::class)->only(['index', 'show']); 

// PERBAIKAN UTAMA #1: Konflik rute 'pengguna' telah dihapus dari bagian publik.

// =========================================================================
// 2. RUTE TERLINDUNGI (Membutuhkan Login Admin / auth:sanctum)
// =========================================================================

Route::middleware('auth:sanctum')->group(function () {
    
    // Rute default user Sanctum
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    
    // RUTE HOME UPDATE (Perbaikan: Menggunakan method 'update' di Controller)
    // PERBAIKAN UTAMA #2: Mengganti 'updateOrCreate' menjadi 'update'
    Route::post('home', [HomeController::class, 'update']); 
    
    // Rute CUD (Create, Update, Delete) untuk resource yang indeks/shownya publik
    Route::apiResource('webinar', WebinarController::class)->except(['index', 'show']);
    Route::apiResource('kategori-bisnis', KategoriBisnisController::class)->except(['index', 'show']);
    Route::apiResource('layanan-bisnis', LayananBisnisController::class)->except(['index', 'show']);
    Route::apiResource('layanan-umum', LayananUmumController::class)->except(['index', 'show']);
    Route::apiResource('lowongan-karir', LowonganKarirController::class)->except(['index', 'show']);
    Route::apiResource('paket-kemitraan', PaketKemitraanController::class)->except(['index', 'show']);
    Route::apiResource('mitra-broker', MitraBrokerController::class)->except(['index', 'show']);
    Route::apiResource('tentang', TentangController::class)->except(['index', 'show']);
    
    // Rute CRUD Penuh untuk Pengguna (Admin)
    // PERBAIKAN UTAMA #1: Mengganti ->except(['index', 'show', 'store']) menjadi ->except(['store'])
    // Ini memastikan index, show, update, dan destroy berada di bawah proteksi admin.
    Route::apiResource('pengguna', PenggunaController::class)->except(['store']); 
});