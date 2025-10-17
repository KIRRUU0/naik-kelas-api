<?php

namespace App\Http\Controllers;

use App\Models\LayananBisnis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB; // <-- BARIS BARU: Import DB Facade

class LayananBisnisController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // PERBAIKAN: Eager Loading untuk mencegah N+1
        $modul = LayananBisnis::with('kategori')->get();
        return response()->json([
        "message" => "Data modul bisnis berhasil diambil",
        "data" => $modul
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kategori_id' => 'required|integer',
            'judul_bisnis' => 'required',
            'deskripsi' => 'required',
            'fitur_unggulan' => 'required',
            'url_cta' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        
        $modul = LayananBisnis::create($request->all());

        return response()->json([
            "message" => "Data modul bisnis berhasil ditambahkan",
            "data" => $modul
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(LayananBisnis $layananBisnis)
    {
        // Menggunakan toArray() untuk memastikan semua atribut Model dikembalikan
        $layananBisnis->load('kategori');
        
        return response()->json([
            "message" => "Data modul bisnis berhasil diambil",
            "data" => $layananBisnis->toArray() 
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, LayananBisnis $layananBisnis)
    {
        // FIX: Mengubah semua validasi menjadi 'sometimes|required' untuk partial update
        $validator = Validator::make($request->all(), [
            'kategori_id' => 'sometimes|required|integer',
            'judul_bisnis' => 'sometimes|required',
            'deskripsi' => 'sometimes|required',
            'fitur_unggulan' => 'sometimes|required',
            'url_cta' => 'sometimes|required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // PERBAIKAN AKHIR UNTUK SILENT FAILURE
        // 1. Ambil hanya field yang dikirim di request
        $dataToUpdate = $request->only([
            'kategori_id', 'judul_bisnis', 'deskripsi', 'fitur_unggulan', 'url_cta'
        ]);

        // 2. Gunakan DB Facade (Query Builder) untuk update eksplisit
        DB::table('layanan_bisnis')
            ->where('id', $layananBisnis->id)
            ->update($dataToUpdate);

        // 3. Muat ulang model dari DB untuk dikembalikan di respons
        $layananBisnis->refresh(); 
        
        return response()->json([
            "message" => "Data modul bisnis berhasil diupdate",
            "data" => $layananBisnis
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(LayananBisnis $layananBisnis)
    {
        $layananBisnis->delete();
        return response()->json([
            "message" => "Data modul bisnis berhasil dihapus"
        ], 200);
    }
}