<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PaketKemitraan extends Model
{
    protected $table = 'paket_kemitraan';
    
    protected $fillable = [
        'nama_paket',
        'gambar',
        'deskripsi',
        'fitur_unggulan',
        'harga',
        'url_cta',
    ];
    
    public $timestamps = false;
    
    // ✅ TAMBAHKAN ACCESSOR untuk URL gambar otomatis
    protected $appends = ['gambar_url'];

    /**
     * Accessor untuk URL gambar
     */
    public function getGambarUrlAttribute()
    {
        if (!$this->gambar) {
            return null;
        }
        
        // ✅ URL yang BENAR untuk struktur hosting
        return url('upload/paket-kemitraan/' . $this->gambar);
    }
}