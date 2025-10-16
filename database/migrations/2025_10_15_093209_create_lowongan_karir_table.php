<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lowongan_karir', function (Blueprint $table) {
            $table->id();
            $table->string('posisi', 255);
            $table->integer('status');
            $table->text('deskripsi');
            $table->string('url_cta', 255);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lowongan_karir');
    }
};