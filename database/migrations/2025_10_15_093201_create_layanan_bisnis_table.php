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
                $table->string('type')->nullable();
                $table->string('tipe_broker',50)->nullable()->change();
                $table->string('judul_bisnis', 255);
                $table->text('deskripsi');
                $table->string('fitur_unggulan', 255)->nullable();
                $table->string('url_cta', 255);
                $table->string('gambar')->nullable();
                $table->integer('harga')->nullable()->default(null);
                $table->date('tanggal_acara')->nullable();
                $table->time('waktu_mulai')->nullable();
                
                // // TAMBAHAN KRITIS: DEFINISI FOREIGN KEY
                // $table->foreign('kategori_id')
                //       ->references('id')
                //       ->on('kategori_bisnis')
                //       ->onDelete('set null'); // Gunakan set null jika kategori dihapus
            });
        }

        public function down(): void
        {
            Schema::dropIfExists('layanan_bisnis');
        }
    };