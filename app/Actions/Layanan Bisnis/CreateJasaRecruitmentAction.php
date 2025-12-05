<?php

namespace App\Actions\LayananBisnis;

use App\Models\LayananBisnis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CreateJasaRecruitmentAction
{
    public function execute(Request $request)
    {
        // Validasi KHUSUS untuk Layanan Reseller
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:jasa_recruitment',
            'judul_bisnis' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'fitur_unggulan' => 'nullable|string', // Contoh: Persentase Komisi
            'url_cta' => 'required|url',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        
        $data = $validator->validated();
        $data['judul_bisnis'] = '[JASARECRUIMENT] ' . $data['judul_bisnis']; 

        $layanan = LayananBisnis::create($data);

        return response()->json([
            "message" => "Layanan Jasa Recruitment berhasil ditambahkan",
            "data" => $layanan
        ], 201);
    }
}