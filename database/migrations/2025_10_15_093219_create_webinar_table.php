<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('webinar', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('kategori_id');
            $table->integer('status_acara');
            $table->string('judul_webinar', 255);
            $table->string('nama_mentor', 255);
            $table->date('tanggal_acara');
            $table->dateTime('waktu_mulai');
            $table->string('url_cta', 255);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('webinar');
    }
};