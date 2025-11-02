<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Tentang extends Model
{
    protected $table = 'tentang';
    protected $fillable = [
        'judul',
        'sub_judul',
        'deskripsi',
        'gambar',
    ];
    public $timestamps = false;
    public function getGambarUrlAttribute()
    {
        return $this->gambar ? asset('storage/' . $this->gambar) : null;
    }
}
