<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('layanan_umum', function (Blueprint $table) {
            $table->id();
            // $table->unsignedBigInteger('kategori_id');
            $table->string('judul_layanan', 50);
            $table->text('deskripsi');
            $table->string('highlight', 255)->nullable();
            $table->string('url_cta', 255);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('layanan_umum');
    }
};