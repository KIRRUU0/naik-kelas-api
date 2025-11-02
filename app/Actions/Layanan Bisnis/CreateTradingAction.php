<?php

namespace App\Actions\LayananBisnis;

use App\Models\LayananBisnis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Enums\TipeBroker;

class CreateTradingAction
{
    public function execute(Request $request)
    {
        // 1. Validasi KHUSUS untuk Layanan Trading/Broker
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:trading',
            'tipe_broker' => 'required|in:' . implode(',', [TipeBroker::NASIONAL->value, TipeBroker::INTERNASIONAL->value]),
            'judul_bisnis' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'fitur_unggulan' => 'required|string', // Komisi, Keamanan, dll.
            'url_cta' => 'required|url',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        
        $data = $validator->validated();
        
        // Opsional: Tambahkan penanda tipe di judul atau kolom terpisah jika diperlukan
        // $data['judul_bisnis'] = '[TRADING] ' . $data['judul_bisnis']; 

        // 2. Create Model Layanan Bisnis
        $layanan = LayananBisnis::create($data);

        return response()->json([
            "message" => "Layanan Trading berhasil ditambahkan",
            "data" => $layanan
        ], 201);
    }
}