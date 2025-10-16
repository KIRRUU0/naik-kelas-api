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
        'tagline_brand',
        'deskripsi_brand',
        'gambar_brand',
        'url_cta',
    ];
}