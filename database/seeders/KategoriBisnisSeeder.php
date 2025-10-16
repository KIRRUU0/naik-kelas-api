<?php

namespace Database\Seeders;

use App\Models\KategoriBisnis;
use Illuminate\Database\Seeder;

class KategoriBisnisSeeder extends Seeder
{
    public function run(): void
    {
        KategoriBisnis::create([
            'kategori_id' => 101,
            'nama_kategori' => 'Pemasaran Digital',
            'gambar' => 'url/gambar/pemasaran.png',
            'deskripsi' => 'Strategi dan alat digital untuk meningkatkan penjualan.',
        ]);

        KategoriBisnis::create([
            'kategori_id' => 102,
            'nama_kategori' => 'Keuangan dan Akuntansi',
            'gambar' => 'url/gambar/keuangan.png',
            'deskripsi' => 'Pengelolaan kas dan laporan keuangan untuk UMKM.',
        ]);
        
        KategoriBisnis::create([
            'kategori_id' => 103,
            'nama_kategori' => 'Pengembangan Produk',
            'gambar' => 'url/gambar/produk.png',
            'deskripsi' => 'Menciptakan dan mematangkan ide produk baru.',
        ]);
    }
}