<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('lowongan_karir', function (Blueprint $table) {
            $table->id();
            $table->string('posisi_jabatan');
            $table->string('lokasi_kerja');
            $table->string('tipe_pekerjaan');
            $table->text('deskripsi_pekerjaan');
            $table->text('kualifikasi');
            $table->string('url_cta');
            // $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lowongan_karir');
    }
};
