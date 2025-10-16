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
            $table->string('nama_paket', 255);
            $table->text('deskripsi');
            $table->string('fitur_unggulan', 255);
            $table->decimal('harga', 10, 2);
            $table->string('url_cta', 255);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('paket_kemitraan');
    }
};