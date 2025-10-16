<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LowonganKarir extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'lowongan_karir';
    protected $fillable = [
        'posisi',
        'status',
        'posisi',
        'deskripsi',
        'url_cta',
    ];
}
