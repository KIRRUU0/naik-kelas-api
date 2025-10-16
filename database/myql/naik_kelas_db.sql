-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 16, 2025 at 08:26 PM
-- Server version: 11.5.2-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `naik_kelas_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `home`
--

CREATE TABLE `home` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tagline_brand` varchar(255) NOT NULL,
  `deskripsi_brand` text NOT NULL,
  `gambar_brand` varchar(255) NOT NULL,
  `url_cta` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `home`
--

INSERT INTO `home` (`id`, `tagline_brand`, `deskripsi_brand`, `gambar_brand`, `url_cta`) VALUES
(1, 'Naik Kelas, Bisnis Lebih Berdampak!', 'Kami membantu UMKM meningkatkan profitabilitas dan efisiensi operasional melalui solusi digital dan pelatihan terpadu.', 'url/gambar/hero.jpg', 'https://naikkelas.com/daftar-sekarang');

-- --------------------------------------------------------

--
-- Table structure for table `kategori_bisnis`
--

CREATE TABLE `kategori_bisnis` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `kategori_id` int(11) NOT NULL,
  `nama_kategori` varchar(50) NOT NULL,
  `gambar` varchar(255) NOT NULL,
  `deskripsi` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `kategori_bisnis`
--

INSERT INTO `kategori_bisnis` (`id`, `kategori_id`, `nama_kategori`, `gambar`, `deskripsi`) VALUES
(1, 101, 'Pemasaran Digital', 'url/gambar/pemasaran.png', 'Strategi dan alat digital untuk meningkatkan penjualan.'),
(2, 102, 'Keuangan dan Akuntansi', 'url/gambar/keuangan.png', 'Pengelolaan kas dan laporan keuangan untuk UMKM.'),
(3, 103, 'Pengembangan Produk', 'url/gambar/produk.png', 'Menciptakan dan mematangkan ide produk baru.'),
(4, 999, 'Kategori Baru Uji', 'url/baru/kategori-999.png', 'Deskripsi untuk kategori baru.');

-- --------------------------------------------------------

--
-- Table structure for table `layanan_bisnis`
--

CREATE TABLE `layanan_bisnis` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `kategori_id` bigint(20) UNSIGNED NOT NULL,
  `judul_bisnis` varchar(255) NOT NULL,
  `deskripsi` text NOT NULL,
  `fitur_unggulan` varchar(255) NOT NULL,
  `url_cta` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `layanan_bisnis`
--

INSERT INTO `layanan_bisnis` (`id`, `kategori_id`, `judul_bisnis`, `deskripsi`, `fitur_unggulan`, `url_cta`) VALUES
(1, 101, 'Solusi Integrasi E-Commerce', 'Integrasikan stok dan penjualan Anda ke semua marketplace.', 'Sinkronisasi Realtime, Laporan Terpusat', 'https://naikkelas.com/ecom'),
(2, 101, 'Solusi Baru', 'Tes data.', 'Fitur A', 'url/tes');

-- --------------------------------------------------------

--
-- Table structure for table `layanan_umum`
--

CREATE TABLE `layanan_umum` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `kategori_id` bigint(20) UNSIGNED NOT NULL,
  `judul_layanan` varchar(50) NOT NULL,
  `deskripsi` text NOT NULL,
  `highlight` int(11) NOT NULL,
  `url_cta` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `layanan_umum`
--

INSERT INTO `layanan_umum` (`id`, `kategori_id`, `judul_layanan`, `deskripsi`, `highlight`, `url_cta`) VALUES
(1, 103, 'Jasa Pembuatan Website Portofolio', 'Membuat website profesional dalam 7 hari kerja.', 1, 'https://naikkelas.com/website-jasa');

-- --------------------------------------------------------

--
-- Table structure for table `lowongan_karir`
--

CREATE TABLE `lowongan_karir` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `posisi` varchar(255) NOT NULL,
  `status` int(11) NOT NULL,
  `deskripsi` text NOT NULL,
  `url_cta` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `lowongan_karir`
--

INSERT INTO `lowongan_karir` (`id`, `posisi`, `status`, `deskripsi`, `url_cta`) VALUES
(1, 'Full Stack Developer (Laravel)', 1, 'Mencari pengembang handal dengan pengalaman minimal 2 tahun.', 'https://karir.naikkelas.com/dev');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_pengguna_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '2025_10_15_093151_create_home_table', 1),
(4, '2025_10_15_093156_create_kategori_bisnis_table', 1),
(5, '2025_10_15_093201_create_layanan_bisnis_table', 1),
(6, '2025_10_15_093205_create_layanan_umum_table', 1),
(7, '2025_10_15_093209_create_lowongan_karir_table', 1),
(8, '2025_10_15_093213_create_mitra_broker_table', 1),
(9, '2025_10_15_093216_create_paket_kemitraan_table', 1),
(10, '2025_10_15_093219_create_webinar_table', 1),
(11, '2025_10_16_152434_create_tentang_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `mitra_broker`
--

CREATE TABLE `mitra_broker` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tipe_broker` int(11) NOT NULL,
  `kategori_id` bigint(20) UNSIGNED NOT NULL,
  `judul_broker` varchar(255) NOT NULL,
  `gambar` varchar(255) NOT NULL,
  `nama_kategori` varchar(125) NOT NULL,
  `deskripsi` text NOT NULL,
  `fitur_unggulan` int(11) NOT NULL,
  `url_cta` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `mitra_broker`
--

INSERT INTO `mitra_broker` (`id`, `tipe_broker`, `kategori_id`, `judul_broker`, `gambar`, `nama_kategori`, `deskripsi`, `fitur_unggulan`, `url_cta`) VALUES
(1, 1, 102, 'Broker Keuangan Terpercaya Indonesia', 'url/gambar/broker-logo.png', 'Pinjaman Modal', 'Menyediakan modal usaha dengan bunga kompetitif.', 3, 'https://broker.com/modal');

-- --------------------------------------------------------

--
-- Table structure for table `paket_kemitraan`
--

CREATE TABLE `paket_kemitraan` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `kategori_id` bigint(20) UNSIGNED NOT NULL,
  `nama_paket` varchar(255) NOT NULL,
  `gambar` varchar(255) NOT NULL,
  `deskripsi` text NOT NULL,
  `fitur_unggulan` text NOT NULL,
  `harga` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `url_cta` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `paket_kemitraan`
--

INSERT INTO `paket_kemitraan` (`id`, `kategori_id`, `nama_paket`, `gambar`, `deskripsi`, `fitur_unggulan`, `harga`, `status`, `url_cta`) VALUES
(1, 101, 'Paket Premium Pemasaran', 'url/gambar/paket-premium.png', 'Dapatkan layanan pemasaran lengkap dan dukungan penuh.', 'Konsultasi 2x, Pelatihan Eksklusif, Garansi Pemasaran', 5000000, 1, 'https://naikkelas.com/paket-premium');

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pengguna`
--

CREATE TABLE `pengguna` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `role` varchar(255) NOT NULL,
  `foto_profil` varchar(255) DEFAULT NULL,
  `nama` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pengguna`
--

INSERT INTO `pengguna` (`id`, `role`, `foto_profil`, `nama`, `email`, `password`) VALUES
(1, 'admin', 'https://example.com/admin-profile.jpg', 'Admin Naik Kelas', 'admin@naikkelas.com', '$2y$12$LiLqoJZEKhZG8k0ezjtQN.m2p.t3FiCy2W9KsjVyeOvN9nEzC8/6W'),
(2, 'user', NULL, 'User Biasa', 'user@naikkelas.com', '$2y$12$a273LLkngO9LLlF42duobejvEXF8pT.wxI7q5ur.1lEonsmqe3u6m');

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `personal_access_tokens`
--

INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `token`, `abilities`, `last_used_at`, `expires_at`, `created_at`, `updated_at`) VALUES
(1, 'App\\Models\\Pengguna', 1, 'admin-token', '87286417f65a7279ebc391aa378a40781c7c224ab618c14b13e9021b21b75d02', '[\"server:admin\"]', '2025-10-16 10:16:36', NULL, '2025-10-16 10:09:35', '2025-10-16 10:16:36');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tentang`
--

CREATE TABLE `tentang` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `judul` varchar(255) NOT NULL,
  `sub_judul` varchar(255) NOT NULL,
  `deskripsi` text NOT NULL,
  `gambar` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tentang`
--

INSERT INTO `tentang` (`id`, `judul`, `sub_judul`, `deskripsi`, `gambar`) VALUES
(1, 'Visi dan Misi Kami', 'Memberdayakan UMKM di Era Digital', 'Sejak 2023, kami berkomitmen menjadi mitra terdepan dalam transformasi digital bagi ribuan bisnis di Indonesia.', 'url/gambar/about.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `webinar`
--

CREATE TABLE `webinar` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `kategori_id` bigint(20) UNSIGNED NOT NULL,
  `status_acara` int(11) NOT NULL,
  `judul_webinar` varchar(255) NOT NULL,
  `nama_mentor` varchar(255) NOT NULL,
  `tanggal_acara` date NOT NULL,
  `waktu_mulai` datetime NOT NULL,
  `url_cta` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `webinar`
--

INSERT INTO `webinar` (`id`, `kategori_id`, `status_acara`, `judul_webinar`, `nama_mentor`, `tanggal_acara`, `waktu_mulai`, `url_cta`) VALUES
(1, 101, 2, 'Teknik SEO Lokal untuk UMKM 2025', 'Budi Setiawan', '2025-10-23', '2025-10-23 17:05:53', 'https://webinar.com/seo-lokal');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `home`
--
ALTER TABLE `home`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `kategori_bisnis`
--
ALTER TABLE `kategori_bisnis`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kategori_bisnis_kategori_id_unique` (`kategori_id`);

--
-- Indexes for table `layanan_bisnis`
--
ALTER TABLE `layanan_bisnis`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `layanan_umum`
--
ALTER TABLE `layanan_umum`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `lowongan_karir`
--
ALTER TABLE `lowongan_karir`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mitra_broker`
--
ALTER TABLE `mitra_broker`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `paket_kemitraan`
--
ALTER TABLE `paket_kemitraan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `pengguna`
--
ALTER TABLE `pengguna`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `pengguna_email_unique` (`email`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `tentang`
--
ALTER TABLE `tentang`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `webinar`
--
ALTER TABLE `webinar`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `home`
--
ALTER TABLE `home`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `kategori_bisnis`
--
ALTER TABLE `kategori_bisnis`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `layanan_bisnis`
--
ALTER TABLE `layanan_bisnis`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `layanan_umum`
--
ALTER TABLE `layanan_umum`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `lowongan_karir`
--
ALTER TABLE `lowongan_karir`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `mitra_broker`
--
ALTER TABLE `mitra_broker`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `paket_kemitraan`
--
ALTER TABLE `paket_kemitraan`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `pengguna`
--
ALTER TABLE `pengguna`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tentang`
--
ALTER TABLE `tentang`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `webinar`
--
ALTER TABLE `webinar`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;