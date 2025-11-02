<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Webinar extends Model
{
    use HasFactory;

    protected $table = 'webinar';
    protected $fillable = [
        'kategori_id',
        'status_acara',
        'judul_webinar',
        'nama_mentor',
        'tanggal_acara',
        'waktu_mulai',
        'url_cta',
    ];
    public $timestamps = false;

    public function kategori()
    {
        return $this->belongsTo(KategoriBisnis::class, 'kategori_id');
    }
}
