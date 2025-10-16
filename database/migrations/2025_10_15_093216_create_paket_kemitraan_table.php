<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
    Schema::create('paket_kemitraan', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('kategori_id'); // Ditambahkan
        $table->string('nama_paket', 255);
        $table->string('gambar', 255);
        $table->text('deskripsi');
        $table->text('fitur_unggulan'); // Ditambahkan (Diasumsikan TEXT, periksa tipe data Anda)
        $table->integer('harga'); // Ditambahkan (Diasumsikan INTEGER, periksa tipe data Anda)
        $table->integer('status'); 
        $table->string('url_cta', 255); // Ditambahkan

        // Tambahkan foreign key jika Anda menggunakan relasi antar tabel:
        // $table->foreign('kategori_id')->references('id')->on('kategori_bisnis');
    });
}

    public function down(): void
    {
        Schema::dropIfExists('paket_kemitraan');
    }
};