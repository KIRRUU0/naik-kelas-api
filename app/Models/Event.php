<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $table = 'events';

    protected $fillable = [
        'id',
        'judul',
        'deskripsi', // ✅ Sesuai dengan mapping desc
        'tanggal_mulai', // ✅ Sesuai dengan mapping tanggal
        'waktu_mulai', // ✅ TAMBAH FIELD INI
        'gambar_poster',
        'status'
    ];
    protected $casts = [
        'tanggal' => 'date',
        'waktu_mulai' => 'string',
    ];
    public $timestamps = false;
    protected $appends = ['gambar_url'];

    public function getGambarUrlAttribute()
    {
        if (!$this->gambar_poster) {
            return null;
        }
        
        // ✅ URL yang BENAR untuk struktur hosting
        return url('upload/events/' . $this->gambar);
    }

}
