<?php

namespace App\Actions\LayananBisnis;

use App\Models\LayananBisnis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CreateWebinarAction
{
    public function execute(Request $request)
    {
        // Validasi KHUSUS untuk Webinar
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:webinar',
            'judul_bisnis' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            
            // Field Webinar Spesifik: Tanggal dan Waktu
            'tanggal_acara' => 'required|date_format:Y-m-d',
            'waktu_mulai' => 'required|date_format:H:i:s', // Asumsi frontend mengirim HH:MM:SS
            'nama_mentor' => 'required|string|max:255',
            
            'fitur_unggulan' => 'nullable|string', // Dapat diisi dengan Nama Mentor
            'url_cta' => 'required|url',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        
        $data = $validator->validated();
        $data['judul_bisnis'] = '' . $data['judul_bisnis']; 

        $layanan = LayananBisnis::create($data);

        return response()->json([
            "message" => "Layanan Webinar berhasil ditambahkan",
            "data" => $layanan
        ], 201);
    }
}