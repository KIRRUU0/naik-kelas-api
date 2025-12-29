<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kontak extends Model
{
    protected $table = 'kontak';
    
    protected $fillable = [
        'nama',
        'email', 
        'nomor_telepon',
        'pesan',
        'dibaca',
        'jenis_layanan'
    ];
    
    public $timestamps = true;
    const CREATED_AT = 'dikirim';
    const UPDATED_AT = null;

    protected $casts = [
        'dibaca' => 'datetime',
        'dikirim' => 'datetime'
    ];

    // Scope untuk pesan belum dibaca
    public function scopeUnread($query)
    {
        return $query->whereNull('dibaca');
    }

    // Scope untuk pesan sudah dibaca
    public function scopeRead($query)
    {
        return $query->whereNotNull('dibaca');
    }
}