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
            // Menambahkan 3 kolom yang dibutuhkan Controller
            $table->string('nama_paket', 255);
            $table->text('deskripsi');
            $table->string('gambar', 255)->after('nama_paket');
            $table->integer('status')->after('deskripsi');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('paket_kemitraan');
    }
};