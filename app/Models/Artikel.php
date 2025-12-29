<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Carbon\Carbon;

class Artikel extends Model
{
    use HasFactory;

    protected $table = 'artikel';
    
    // Field yang bisa diisi massal
    protected $fillable = [
        'judul',
        'slug',          // Tambahkan slug
        'deskripsi',
        'gambar',
        'tanggal_terbit',
        'url_cta'
    ];

    public $timestamps = false;

    protected $casts = [
        'tanggal_terbit' => 'date'
    ];
    
    /**
     * Boot method untuk generate slug otomatis
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($artikel) {
            // Generate slug dari judul jika slug kosong
            if (empty($artikel->slug)) {
                $artikel->slug = $artikel->generateSlug($artikel->judul);
            }
        });
        
        static::updating(function ($artikel) {
            // Update slug jika judul berubah
            if ($artikel->isDirty('judul') && empty($artikel->slug)) {
                $artikel->slug = $artikel->generateSlug($artikel->judul);
            }
        });
    }
    
    /**
     * Generate slug dari judul
     */
    public function generateSlug($judul)
    {
        $slug = Str::slug($judul, '-', 'id');
        
        // Cek jika slug sudah ada, tambahkan angka
        $count = 1;
        $originalSlug = $slug;
        
        while (static::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $count;
            $count++;
        }
        
        return $slug;
    }
    
    /**
     * Accessor untuk gambar_url
     */
    public function getGambarUrlAttribute()
    {
        if (!$this->gambar) {
            return null;
        }
        
        return url('upload/artikel/' . $this->gambar);
    }
    
    /**
     * Scope untuk mencari berdasarkan slug
     */
    public function scopeFindBySlug($query, $slug)
    {
        return $query->where('slug', $slug)->first();
    }
    
    /**
     * Scope untuk artikel dengan slug tertentu
     */
    public function scopeWhereSlug($query, $slug)
    {
        return $query->where('slug', $slug);
    }
}