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
            $table->unsignedBigInteger('kategori_id')->index();
            $table->string('judul_broker', 255);
            $table->string('gambar', 255);
            // REVISI KRITIS: Kolom 'nama_kategori' dihapus karena redundan
            // $table->string('nama_kategori', 125); 
            $table->text('deskripsi');
            // REVISI KRITIS: Menggunakan tipe STRING untuk 'fitur_unggulan'
            $table->string('fitur_unggulan', 255); 
            $table->string('url_cta', 255);

            $table->foreign('kategori_id')
                  ->references('id')
                  ->on('kategori_bisnis')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('mitra_broker', function (Blueprint $table) {
            $table->dropForeign(['kategori_id']);
        });
        Schema::dropIfExists('mitra_broker');
    }
};