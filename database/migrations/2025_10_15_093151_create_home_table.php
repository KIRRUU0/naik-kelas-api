<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('home', function (Blueprint $table) {
            $table->id();
            $table->string('tagline_brand', 255);
            $table->text('deskripsi_brand');
            $table->string('gambar_brand', 255); 
            $table->string('url_cta', 255);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('home');
    }
};