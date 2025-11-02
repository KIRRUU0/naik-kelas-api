<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LayananBisnisController;
use App\Http\Controllers\PenggunaController;
use App\Http\Controllers\LayananUmumController;
use App\Http\Controllers\LowonganKarirController;
use App\Http\Controllers\PaketKemitraanController;
use App\Http\Controllers\HomeController; 
use App\Http\Controllers\TentangController;
use App\Http\Controllers\KontakController;
use App\Http\Controllers\Api\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes: Diatur berdasarkan Level Keamanan (Menggunakan Laravel Sanctum)
|--------------------------------------------------------------------------
*/

// =========================================================================
// 1. RUTE PUBLIK (Dapat diakses tanpa login/token)
// =========================================================================

// RUTE AUTENTIKASI PUBLIK
Route::post('auth/login', [AuthController::class, 'login']); 

// RUTE PENDAFTARAN PENGGUNA (Publik - untuk registrasi pertama)
Route::post('pengguna', [PenggunaController::class, 'store']);

// RUTE FORM KONTAK - PUBLIC ACCESS (TANPA LOGIN)
Route::post('/kontak', [KontakController::class, 'store']);

// Rute READ Publik (GET index & show)
Route::get('home', [HomeController::class, 'index']); 
Route::get('home/{id}', [HomeController::class, 'show']);
Route::get('tentang', [TentangController::class, 'index']);
Route::get('tentang/{id}', [TentangController::class, 'show']);
Route::apiResource('layanan-bisnis', LayananBisnisController::class)->only(['index', 'show']);
Route::apiResource('layanan-umum', LayananUmumController::class)->only(['index', 'show']);
Route::apiResource('lowongan-karir', LowonganKarirController::class)->only(['index', 'show']);
Route::apiResource('paket-kemitraan', PaketKemitraanController::class)->only(['index', 'show']);

// =========================================================================
// 2. RUTE TERLINDUNGI (Middleware: auth:sanctum)
// =========================================================================

Route::middleware(['auth:sanctum'])->group(function () {
    
    // RUTE AUTENTIKASI
    Route::post('auth/logout', [AuthController::class, 'logout']); 
    
    // Data user yang login
    Route::get('/user', function (Request $request) {
        return response()->json([
            'status' => 'success',
            'data' => $request->user()->makeHidden(['password'])
        ]);
    });

    // ---------------------------------------------------------------------
    // RUTE PROFIL USER (Untuk semua user yang login)
    // ---------------------------------------------------------------------
    Route::get('profile', [PenggunaController::class, 'profile']);
    Route::put('profile', [PenggunaController::class, 'updateProfile']);

    // ---------------------------------------------------------------------
    // RUTE SUPER ADMIN ONLY
    // ---------------------------------------------------------------------
    Route::middleware('role:super_admin')->group(function () {
        // Management pengguna lengkap (hanya super_admin)
        Route::get('pengguna', [PenggunaController::class, 'index']);
        Route::get('pengguna/{pengguna}', [PenggunaController::class, 'show']);
        Route::delete('pengguna/{pengguna}', [PenggunaController::class, 'destroy']);
        Route::post('pengguna/admin', [PenggunaController::class, 'storeAdmin']); // Buat admin baru
    });

    // ---------------------------------------------------------------------
    // RUTE ADMIN & SUPER ADMIN (Update pengguna)
    // ---------------------------------------------------------------------
    Route::middleware('role:admin,super_admin')->group(function () {
        Route::post('pengguna/{id}', [PenggunaController::class, 'update']); 
    });

    // ---------------------------------------------------------------------
    // RUTE ADMIN & SUPER ADMIN (CRUD Konten)
    // ---------------------------------------------------------------------
    Route::middleware('role:admin,super_admin')->group(function () {
        
        // HOME Routes
        Route::post('home', [HomeController::class, 'store']);
        Route::put('home', [HomeController::class, 'update']);
        Route::post('home/{id}', [HomeController::class, 'update']);
        Route::delete('home/{home}', [HomeController::class, 'destroy']);
        
        // Route khusus untuk home
        Route::post('home/{home}/upload-brand', [HomeController::class, 'uploadBrand']);
        Route::put('home/{home}/content', [HomeController::class, 'updateContent']);
        
        // TENTANG Routes
        Route::put('tentang', [TentangController::class, 'update']);
        Route::post('tentang', [TentangController::class, 'store']);
        Route::delete('tentang/{tentang}', [TentangController::class, 'destroy']);
        
        // ✅ Route khusus untuk tentang (TAMBAHAN BARU)
        Route::post('tentang/{tentang}/upload-image', [TentangController::class, 'uploadImage']);
        Route::put('tentang/{tentang}/content', [TentangController::class, 'updateContent']);

        // ✅ RUTE LAYANAN BISNIS YANG DIPERBAIKI
        Route::post('layanan-bisnis', [LayananBisnisController::class, 'store']);
        Route::post('layanan-bisnis/{layanan_bisnis}', [LayananBisnisController::class, 'update']); // ✅ UBAH KE POST
        Route::delete('layanan-bisnis/{layanan_bisnis}', [LayananBisnisController::class, 'destroy']);

        // Resource lainnya tetap pakai apiResource
        Route::apiResource('layanan-umum', LayananUmumController::class)->except(['index', 'show']);
        Route::apiResource('lowongan-karir', LowonganKarirController::class)->except(['index', 'show']);
        
        // paket-kemitraan
        Route::post('paket-kemitraan', [PaketKemitraanController::class, 'store']);
        Route::post('paket-kemitraan/{id}', [PaketKemitraanController::class, 'update']); // ✅ POST untuk update
        Route::delete('paket-kemitraan/{id}', [PaketKemitraanController::class, 'destroy']);

        // DELETE by type
        Route::delete('/layanan-bisnis/delete-by-type', [LayananBisnisController::class, 'deleteByType']);
        
        // PESAN KONTAK
        Route::get('/kontak', [KontakController::class, 'index']);
        Route::get('/kontak/unread', [KontakController::class, 'unreadMessages']);
        Route::get('/kontak/{id}', [KontakController::class, 'show']);
        Route::put('/kontak/{id}/baca', [KontakController::class, 'markAsRead']);
        Route::put('/kontak/mark-all-read', [KontakController::class, 'markAllAsRead']);
        Route::delete('/kontak/{id}', [KontakController::class, 'destroy']);
        
    });
});

// ✅ ROUTE KHUSUS UNTUK CREATE SUPER ADMIN (tanpa auth)
Route::post('/pengguna/create-super-admin', [PenggunaController::class, 'createSuperAdmin']);

// =========================================================================
// 3. RUTE FALLBACK (Untuk handle route tidak ditemukan)
// =========================================================================

Route::fallback(function () {
    return response()->json([
        'status' => 'error',
        'message' => 'Endpoint tidak ditemukan'
    ], 404);
});