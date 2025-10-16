<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mitra_broker', function (Blueprint $table) {
            $table->id();
            $table->integer('tipe_broker');
            $table->unsignedBigInteger('kategori_id');
            $table->string('judul_broker', 255);
            $table->string('gambar', 255);
            $table->string('nama_kategori', 125);
            $table->text('deskripsi');
            $table->integer('fitur_unggulan');
            $table->string('url_cta', 255);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mitra_broker');
    }
};