// database/migrations/xxxx_create_articles_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->text('deskripsi');
            $table->string('gambar')->nullable();
            $table->date('tanggal_terbit')->nullable();
            $table->string('url_cta', 500)->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('artikel');
    }
};