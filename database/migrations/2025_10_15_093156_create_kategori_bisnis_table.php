<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
    Schema::create('kategori_bisnis', function (Blueprint $table) {
        $table->id();
        // $table->integer('kategori_id')->unique()->nullable(); // Ditambahkan: Harus unik sesuai kebutuhan
        $table->string('nama_kategori', 50);
        $table->string('gambar', 255); // Ditambahkan
        $table->text('deskripsi'); // Ditambahkan
    });
}

    public function down(): void
    {
        Schema::dropIfExists('kategori_bisnis');
    }
};