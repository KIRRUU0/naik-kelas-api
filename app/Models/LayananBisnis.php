<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LayananBisnis extends Model
{
    use HasFactory;

    protected $table = 'layanan_bisnis';
    protected $fillable = [
        'kategori_id',
        'judul_bisnis',
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