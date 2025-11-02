<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController; 

// Rute Default / Landing Page
Route::get('/', function () {
    return view('welcome'); // Asumsi Anda memiliki view 'welcome'
});

// =========================================================================
// RUTE AUTHENTIKASI (BERBASIS SESI/WEB)
// =========================================================================

// Rute untuk login (Menampilkan form dan memproses POST)
// PENTING: TIDAK BOLEH memiliki ->name('login') di API
Route::get('/login', [LoginController::class, 'showLoginFrom']);
Route::post('/login', [LoginController::class, 'login']);

// Rute untuk logout (Memerlukan sesi aktif)
Route::post('/logout', [LoginController::class, 'logout'])->name('logout'); 

// =========================================================================
// RUTE TERLINDUNGI (Membutuhkan Sesi Aktif)
// =========================================================================

// Rute yang dilindungi oleh middleware autentikasi (guard 'web' default)
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard'); // Asumsi Anda memiliki view 'dashboard'
    })->name('dashboard');
    
    // Tambahkan rute berbasis web/view lainnya di sini.
});