<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Pastikan nama tabel adalah 'tentang'
        Schema::create('tentang', function (Blueprint $table) {
            $table->id();
            $table->string('judul', 255);
            $table->string('sub_judul', 255);
            $table->text('deskripsi');
            $table->string('gambar', 255);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tentang');
    }
};