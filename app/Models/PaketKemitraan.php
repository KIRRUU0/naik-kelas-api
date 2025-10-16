<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PaketKemitraan extends Model
{
    protected $table = 'paket_kemitraan';
    // PERBAIKAN: Menambahkan field yang hilang sesuai yang divalidasi di Controller
    protected $fillable = [
        'kategori_id', // BARU DITAMBAH
        'nama_paket',
        'gambar', // BARU DITAMBAH
        'deskripsi',
        'fitur_unggulan',
        'harga',
        'status', // BARU DITAMBAH
        'url_cta',
    ];
    public $timestamps = false;
}