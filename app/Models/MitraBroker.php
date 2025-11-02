<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MitraBroker extends Model
{
    use HasFactory;

    protected $table = 'mitra_broker';
    protected $fillable = [
        'tipe_broker',
        'kategori_id',
        'judul_broker',
        'gambar',
        // REVISI KRITIS: 'nama_kategori' dihapus
        'deskripsi',
        'fitur_unggulan',
        'url_cta',
    ];
    public $timestamps = false;
    
    public function kategori()
    {
        return $this->belongsTo(KategoriBisnis::class, 'kategori_id');
    }
}