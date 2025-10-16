<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable; 
use Laravel\Sanctum\HasApiTokens;

class Pengguna extends Authenticatable
{
    /** @use HasFactory<PenggunaFactory> */
    use HasApiTokens, HasFactory;

    /**
     * Nama tabel kustom yang Anda gunakan.
     * (Menunjuk ke tabel 'pengguna' Anda, bukan 'users').
     */
    protected $table = 'pengguna'; 
    
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'role',
        'foto_profil',
        'nama',
        'email',
        'password',
    ];
    public $timestamps = false; // Tetap sesuai setup Anda

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        // FIX: Hapus 'email_verified_at' jika kolom ini tidak ada di tabel 'pengguna' Anda
        'password' => 'hashed',
    ];
}