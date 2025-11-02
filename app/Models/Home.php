<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Home extends Model
{
    use HasFactory;

    protected $table = 'home'; // Pastikan nama tabel sesuai

    protected $fillable = [
        'tagline_brand',
        'deskripsi_brand', 
        'gambar_brand',
        'url_cta'
    ];

    // Nonaktifkan timestamps jika tidak ada created_at/updated_at
    public $timestamps = false;

    // Accessor untuk URL gambar_brand
    public function getGambarBrandUrlAttribute()
    {
        return $this->gambar_brand ? asset('storage/' . $this->gambar_brand) : null;
    }

    // Accessor untuk URL CTA lengkap
    public function getUrlCtaFullAttribute()
    {
        if (!$this->url_cta) return null;
        
        // Jika URL tidak diawali http, tambahkan
        if (!preg_match("~^(?:f|ht)tps?://~i", $this->url_cta)) {
            return "https://" . $this->url_cta;
        }
        
        return $this->url_cta;
    }
}