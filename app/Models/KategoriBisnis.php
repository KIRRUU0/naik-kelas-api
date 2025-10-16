<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KategoriBisnis extends Model
{
    protected $table = 'kategori_bisnis';
    protected $fillable = [
        'kategori_id',
        'gambar',
        'nama_kategori',
        'deskripsi',
    ];
    public $timestamps = false;
}
