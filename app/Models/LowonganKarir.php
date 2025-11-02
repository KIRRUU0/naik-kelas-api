<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Enums\StatusLowongan;

class LowonganKarir extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'lowongan_karir';
    
    protected $fillable = [
        'posisi',
        'status',
        'deskripsi',
        'url_cta',
    ];
    
    protected $casts = [
        'status' => StatusLowongan::class,
    ];
}