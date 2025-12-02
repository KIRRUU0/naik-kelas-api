<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LayananUmum extends Model
{
    use HasFactory;

    protected $table = 'layanan_umum';
    protected $fillable = [
        // 'kategori_id',
        'judul_layanan',
        'deskripsi',
        'gambar',
        'highlight',
        'url_cta',
    ];

    protected $appends = ['gambar_url'];
    public $timestamps = false;
    public function getGambarUrlAttribute()
    {
        if (!$this->gambar) {
            return null;
        }
        
        // ✅ URL yang BENAR untuk struktur hosting
        return url('upload/layanan-umum/' . $this->gambar);
    }
}
