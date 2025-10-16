<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('layanan_bisnis', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('kategori_id');
            $table->string('judul_bisnis', 255);
            $table->text('deskripsi');
            $table->string('fitur_unggulan', 255);
            $table->string('url_cta', 255);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('layanan_bisnis');
    }
};