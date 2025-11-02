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
        'highlight',
        'url_cta',
    ];
    public $timestamps = false;
}
