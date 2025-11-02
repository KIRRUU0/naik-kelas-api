<?php

namespace Database\Seeders;

use App\Models\Pengguna;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PenggunaSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Pengguna Super Admin (Akses Penuh)
        Pengguna::create([
            'role' => 'super_admin',
            'nama' => 'Super Admin 1',
            'email' => 'superadmin1@naikkelas.com',
            'password' => Hash::make('password'), 
            'foto_profil' => 'https://example.com/superadmin-profile.jpg',
        ]);

        // 2. Pengguna Super Admin (Akses Penuh)
        Pengguna::create([
            'role' => 'super_admin',
            'nama' => 'Super Admin 2',
            'email' => 'superadmin2@naikkelas.com',
            'password' => Hash::make('password'), 
            'foto_profil' => 'https://example.com/superadmin-profile.jpg',
        ]);
        
        // 2. Pengguna Admin Biasa (Akses Terbatas)
        Pengguna::create([
            'role' => 'admin',
            'nama' => 'Admin Biasa NK',
            'email' => 'admin@naikkelas.com',
            'password' => Hash::make('password'), 
            'foto_profil' => 'https://example.com/admin-profile.jpg',
        ]);

        // 3. Pengguna Biasa (Tetap untuk pengujian akses non-admin)
        Pengguna::create([
            'role' => 'user',
            'nama' => 'User Biasa',
            'email' => 'user@naikkelas.com',
            'password' => Hash::make('password'),
            'foto_profil' => null,
        ]);
    }
}