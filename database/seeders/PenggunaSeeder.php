<?php

namespace Database\Seeders;

use App\Models\Pengguna;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PenggunaSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Pengguna Admin (Untuk Pengujian Rute Terlindungi)
        Pengguna::create([
            'role' => 'admin',
            'nama' => 'Admin Naik Kelas',
            'email' => 'admin@naikkelas.com',
            // Pastikan password ini sama dengan yang Anda gunakan untuk pengujian login
            'password' => Hash::make('password'), 
            'foto_profil' => 'https://example.com/admin-profile.jpg',
        ]);

        // 2. Pengguna Biasa (Untuk Pengujian Role/Akses)
        Pengguna::create([
            'role' => 'user',
            'nama' => 'User Biasa',
            'email' => 'user@naikkelas.com',
            'password' => Hash::make('password'),
            'foto_profil' => null,
        ]);
    }
}