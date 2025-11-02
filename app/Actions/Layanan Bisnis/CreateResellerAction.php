<?php

namespace App\Actions\LayananBisnis;

use App\Models\LayananBisnis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CreateResellerAction
{
    public function execute(Request $request)
    {
        // Validasi KHUSUS untuk Layanan Reseller
        $validator = Validator::make($request->all(), [
            'judul_bisnis' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'harga' => 'required|integer', // Harga paket reseller
            'fitur_unggulan' => 'nullable|string', // Contoh: Persentase Komisi
            'url_cta' => 'required|url',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        
        $data = $validator->validated();
        $data['judul_bisnis'] = '[RESELLER] ' . $data['judul_bisnis']; 

        $layanan = LayananBisnis::create($data);

        return response()->json([
            "message" => "Layanan Reseller berhasil ditambahkan",
            "data" => $layanan
        ], 201);
    }
}