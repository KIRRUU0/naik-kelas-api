<?php

namespace App\Http\Controllers;

use App\Models\LayananBisnis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LayananBisnisController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
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
            'kategori_id' => 'required',
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
    public function show(LayananBisnis $layananBisnis) // RMD aktif
    {
        $layananBisnis->load('kategori'); // Memuat relasi
        
        return response()->json([
            "message" => "Data modul bisnis berhasil diambil",
            "data" => $layananBisnis
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, LayananBisnis $layananBisnis) // PERBAIKAN: Mengganti parameter model yang salah
    {
        // Query redundan dihapus

        $validator = Validator::make($request->all(), [
            'kategori_id' => 'required',
            'judul_bisnis' => 'required',
            'deskripsi' => 'required',
            'fitur_unggulan' => 'required',
            'url_cta' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $layananBisnis->update($request->all()); // Menggunakan model yang sudah di-bind

        return response()->json([
            "message" => "Data modul bisnis berhasil diupdate",
            "data" => $layananBisnis
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(LayananBisnis $layananBisnis) // RMD aktif
    {
        $layananBisnis->delete();
        return response()->json([
            "message" => "Data modul bisnis berhasil dihapus"
        ], 200);
    }
}