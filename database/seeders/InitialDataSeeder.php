<?php

namespace Database\Seeders;

use App\Models\Home;
use App\Models\Kontak;
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
            'gambar_brand' => 'hero.jpg',
            'url_cta' => 'https://naikkelas.com/daftar-sekarang',
        ]);

        // 2. Layanan Bisnis - Trading (Nasional)
        LayananBisnis::create([
            'type' => 'trading',
            'tipe_broker' => 'nasional',
            'judul_bisnis' => 'Broker Trading Terpercaya Indonesia',
            'deskripsi' => 'Nikmati platform trading dengan spread rendah dan eksekusi cepat.',
            'fitur_unggulan' => 'Regulasi Resmi, Edukasi Gratis, Dukungan 24/7',
            'url_cta' => 'https://naikkelas.com/trading',
        ]);

        // 3. Layanan Bisnis - Trading (Internasional)
        LayananBisnis::create([
            'type' => 'trading',
            'tipe_broker' => 'internasional',
            'judul_bisnis' => 'Broker Trading Internasional',
            'deskripsi' => 'Akses pasar global dengan leverage hingga 1:500.',
            'fitur_unggulan' => 'Platform Canggih, Analisis Pasar, Akun Demo',
            'url_cta' => 'https://naikkelas.com/trading-internasional',
        ]);

        // 4. Layanan Bisnis - Jasa Recruitment
        LayananBisnis::create([
            'type' => 'jasa_recruitment',
            'judul_bisnis' => 'Jasa Recruitment Profesional',
            'deskripsi' => 'Temukan talenta terbaik untuk bisnis Anda dengan proses seleksi yang ketat.',
            'fitur_unggulan' => 'Database Kandidat Luas, Wawancara Mendalam',
            'url_cta' => 'https://naikkelas.com/jasa-recruitment',
        ]);

        // 5. Layanan Bisnis - Modal Bisnis
        LayananBisnis::create([
            'type' => 'modal_bisnis',
            'judul_bisnis' => 'Modal Bisnis untuk UMKM',
            'deskripsi' => 'Dapatkan modal usaha dengan bunga ringan dan proses cepat.',
            'fitur_unggulan' => 'Proses Cepat, Syarat Mudah, Tanpa Agunan',
            'url_cta' => 'https://naikkelas.com/modal-bisnis',
        ]);

        // 6. Layanan Bisnis - Webinar
        LayananBisnis::create([
            'type' => 'webinar',
            'judul_bisnis' => 'Webinar Bisnis Online',
            'deskripsi' => 'Ikuti webinar bisnis dengan pembicara ahli dari berbagai industri.',
            'fitur_unggulan' => 'Sertifikat, Rekaman, Materi Eksklusif',
            'tanggal_acara' => Carbon::now()->addWeeks(2)->toDateString(),
            'waktu_mulai' => '15:00:00',
            'url_cta' => 'https://naikkelas.com/webinar-bisnis',
        ]);

        // 7. Paket Kemitraan
        PaketKemitraan::create([
            'nama_paket' => 'Paket Premium Pemasaran',
            'gambar' => 'url/gambar/paket-premium.png',
            'deskripsi' => 'Dapatkan layanan pemasaran lengkap dan dukungan penuh.',
            'fitur_unggulan' => 'Konsultasi 2x, Pelatihan Eksklusif, Garansi Pemasaran',
            'harga' => 5000000,
            'url_cta' => 'https://naikkelas.com/paket-premium',
        ]);
        
        // 4. Layanan Umum
        LayananUmum::create([
            'judul_layanan' => 'Jasa Pembuatan Website Portofolio',
            'deskripsi' => 'Membuat website profesional dalam 7 hari kerja.',
            'highlight' => 1,
            'url_cta' => 'https://naikkelas.com/website-jasa',
        ]);

        // 5. Lowongan Karir - Buka
        LowonganKarir::create([
            'posisi' => 'Full Stack Developer (Laravel)',
            'status' => StatusLowongan::DIBUKA->value,
            'deskripsi' => 'Mencari pengembang handal dengan pengalaman minimal 2 tahun.',
            'url_cta' => 'https://karir.naikkelas.com/dev',
        ]);

        // 6. Lowongan Karir - Tutup
        LowonganKarir::create([
            'posisi' => 'Digital Marketing Specialist',
            'status' => StatusLowongan::DITUTUP->value,
            'deskripsi' => 'Posisi ini sudah terisi. Terima kasih atas minat Anda.',
            'url_cta' => 'https://karir.naikkelas.com/marketing',
        ]);
        
        // 7. Kontak
        Kontak::create([
            'nama' => 'Naik Kelas adalah platform yang berfokus pada pemberdayaan UMKM melalui solusi digital inovatif dan pelatihan bisnis terpadu.',
            'email' => 'Jl. Merdeka No.123, Jakarta, Indonesia',
            'nomor_telepon' => '+62 21 1234 5678',
            'pesan' => 'nyontek dong',
        ]);
    }
}