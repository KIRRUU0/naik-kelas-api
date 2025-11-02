<?php

namespace Database\Seeders;

use App\Models\Home;
use App\Models\Tentang;
use App\Models\Webinar;
use App\Models\LayananUmum;
use App\Models\LowonganKarir;
use App\Models\MitraBroker;
use App\Models\PaketKemitraan;
use App\Models\LayananBisnis;
use App\Enums\StatusLowongan;  // Wajib diimport

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class InitialDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Home
        Home::create([
            'tagline_brand' => 'Naik Kelas, Bisnis Lebih Berdampak!',
            'deskripsi_brand' => 'Kami membantu UMKM meningkatkan profitabilitas dan efisiensi operasional melalui solusi digital dan pelatihan terpadu.',
            'gambar_brand' => 'url/gambar/hero.jpg',
            'url_cta' => 'https://naikkelas.com/daftar-sekarang',
        ]);

        // 2. Tentang
        Tentang::create([
            'judul' => 'Visi dan Misi Kami',
            'sub_judul' => 'Memberdayakan UMKM di Era Digital',
            'deskripsi' => 'Sejak 2023, kami berkomitmen menjadi mitra terdepan dalam transformasi digital bagi ribuan bisnis di Indonesia.',
            'gambar' => 'url/gambar/about.jpg',
        ]);

        // 3. Webinar 
        Webinar::create([
            'kategori_id' => $pemasaran_id, // Menggunakan ID dinamis
            'status_acara' => 2,
            'judul_webinar' => 'Teknik SEO Lokal untuk UMKM 2025',
            'nama_mentor' => 'Budi Setiawan',
            'tanggal_acara' => Carbon::now()->addDays(7)->format('Y-m-d'),
            'waktu_mulai' => Carbon::now()->addDays(7)->format('Y-m-d H:i:s'),
            'url_cta' => 'https://webinar.com/seo-lokal',
        ]);
        
        // 4. Layanan Umum
        LayananUmum::create([
            'kategori_id' => $produk_id, // Menggunakan ID dinamis
            'judul_layanan' => 'Jasa Pembuatan Website Portofolio',
            'deskripsi' => 'Membuat website profesional dalam 7 hari kerja.',
            'highlight' => 1,
            'url_cta' => 'https://naikkelas.com/website-jasa',
        ]);

        // 5. Lowongan Karir
        LowonganKarir::create([
            'posisi' => 'Full Stack Developer (Laravel)',
            'status' => StatusLowongan::DIBUKA->value,
            'deskripsi' => 'Mencari pengembang handal dengan pengalaman minimal 2 tahun.',
            'url_cta' => 'https://karir.naikkelas.com/dev',
        ]);

        // 6. Mitra Broker
        MitraBroker::create([
            'tipe_broker' => 1,
            'kategori_id' => $keuangan_id, // Menggunakan ID dinamis
            'judul_broker' => 'Broker Keuangan Terpercaya Indonesia',
            'gambar' => 'url/gambar/broker-logo.png',
            // 'nama_kategori' => 'Pinjaman Modal',
            'deskripsi' => 'Menyediakan modal usaha dengan bunga kompetitif.',
            'fitur_unggulan' => 3,
            'url_cta' => 'https://broker.com/modal',
        ]);

        // 7. Paket Kemitraan
        PaketKemitraan::create([
            'kategori_id' => $pemasaran_id, // Menggunakan ID dinamis
            'nama_paket' => 'Paket Premium Pemasaran',
            'gambar' => 'url/gambar/paket-premium.png',
            'deskripsi' => 'Dapatkan layanan pemasaran lengkap dan dukungan penuh.',
            'fitur_unggulan' => 'Konsultasi 2x, Pelatihan Eksklusif, Garansi Pemasaran',
            'harga' => 5000000,
            'status' => 1,
            'url_cta' => 'https://naikkelas.com/paket-premium',
        ]);
        
        // 8. Layanan Bisnis
        LayananBisnis::create([
            'kategori_id' => $bisnis_id, // Menggunakan ID dinamis
            'judul_bisnis' => 'Solusi Integrasi E-Commerce',
            'deskripsi' => 'Integrasikan stok dan penjualan Anda ke semua marketplace.',
            'fitur_unggulan' => 'Sinkronisasi Realtime, Laporan Terpusat',
            'url_cta' => 'https://naikkelas.com/ecom',
        ]);
    }
}