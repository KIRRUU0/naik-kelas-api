<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PaketKemitraan extends Model
{
    protected $table = 'paket_kemitraan';
    protected $fillable = [
        'nama_paket',
        'deskripsi',
        'fitur_unggulan',
        'harga',
        'url_cta',
    ];
    public $timestamps = false;
}
