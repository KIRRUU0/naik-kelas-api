<?php

namespace App\Actions\LayananBisnis;

use App\Models\LayananBisnis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CreateModalBisnisAction
{
    public function execute(Request $request)
    {
        // Validasi KHUSUS untuk Layanan Kelas/Pelatihan
        $validator = Validator::make($request->all(), [
            'judul_bisnis' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'harga' => 'required|integer', // Harga Modal
            'fitur_unggulan' => 'required|string', 
            'url_cta' => 'required|url',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        
        $data = $validator->validated();
        $data['judul_bisnis'] = ' ' . $data['judul_bisnis']; 

        $layanan = LayananBisnis::create($data);

        return response()->json([
            "message" => "Layanan Modal Bisnis berhasil ditambahkan",
            "data" => $layanan
        ], 201);
    }
}