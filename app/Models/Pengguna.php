<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Pengguna extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'pengguna';

    protected $fillable = [
        'nama',
        'email', 
        'password',
        'foto_profil',
        'role'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    // ✅ Accessor otomatis include di JSON response
    protected $appends = ['foto_profil_url'];

    // Nonaktifkan timestamps
    public $timestamps = false;

    /**
     * ACCESSOR untuk URL foto profil
     * Menggunakan 'upload/foto-profil/' sesuai struktur hosting
     */
    public function getFotoProfilUrlAttribute()
    {
        if (!$this->foto_profil) {
            return null;
        }
        
        return url('upload/foto-profil/' . $this->foto_profil);
    }

    // Check if user is super admin
    public function isSuperAdmin()
    {
        return $this->role === 'super_admin';
    }

    // Check if user is admin
    public function isAdmin()
    {
        return $this->role === 'admin';
    }
}