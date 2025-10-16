<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Home extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'home';
    protected $fillable = [
        'tagline_home',
        'deskripsi_home',
        'gambar_home',
        'url_cta',
    ];
}
