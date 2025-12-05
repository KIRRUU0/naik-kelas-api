<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Panggil semua Seeder kustom yang baru dibuat
        $this->call([
            PenggunaSeeder::class,
            InitialDataSeeder::class,
        ]);

        // Baris lama yang membuat user factory dapat dihapus atau diabaikan:
        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}