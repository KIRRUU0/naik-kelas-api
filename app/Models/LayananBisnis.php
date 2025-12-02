<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LayananBisnis extends Model
{
    use HasFactory;

    protected $table = 'layanan_bisnis';
    
    protected $fillable = [
        'kategori_id',
        'type', 
        'tipe_broker',
        'judul_bisnis',
        'gambar',
        'deskripsi',
        'fitur_unggulan',
        'harga',
        'url_cta',
        'tanggal_acara',  // ✅ TAMBAHKAN
        'waktu_mulai',    // ✅ TAMBAHKAN  
    ];

    // ✅ TAMBAHKAN ACCESSOR untuk URL gambar otomatis
    protected $appends = ['gambar_url'];
    public $timestamps = false;


    /**
     * Accessor untuk URL gambar
     */
    public function getGambarUrlAttribute()
    {
        if (!$this->gambar) {
            return null;
        }
        
        // ✅ URL yang BENAR untuk struktur hosting
        return url('upload/layanan-bisnis/' . $this->gambar);
    }
}