<?php

namespace App\Http\Controllers;

use App\Models\KategoriBisnis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator; // FIX: Menambahkan Validator yang hilang

class KategoriBisnisController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $kategoriBisnis = KategoriBisnis::all();
        return response()->json([
        "message" => "Data kategori bisnis berhasil diambil",
        "data" => $kategoriBisnis
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kategori_id' => 'required|integer',
            'gambar' => 'required',
            'nama_kategori' => 'required',
            'deskripsi' => 'required',
        ]);
        
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        
        // GANTI LOGIC CREATE DENGAN METODE SAVE EKSPLISIT:
        $kategoriBisnis = new KategoriBisnis();
        $kategoriBisnis->kategori_id = $request->kategori_id;
        $kategoriBisnis->nama_kategori = $request->nama_kategori;
        $kategoriBisnis->gambar = $request->gambar;
        $kategoriBisnis->deskripsi = $request->deskripsi;
        
        // Coba simpan data dan cek hasilnya
        if ($kategoriBisnis->save()) {
            return response()->json([
                "message" => "Data kategori bisnis berhasil ditambahkan",
                "data" => $kategoriBisnis
            ], 201);
        } else {
            // Jika save() mengembalikan false (silent failure)
            return response()->json(["message" => "Gagal menyimpan data ke database, coba lagi."], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(KategoriBisnis $kategoriBisnis) // RMD aktif
    {
        // Query redundan dihapus
        return response()->json([
            "message" => "Data kategori bisnis berhasil diambil",
            "data" => $kategoriBisnis
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, KategoriBisnis $kategoriBisnis) // RMD aktif
    {
        $validator = Validator::make($request->all(), [
            'kategori_id' => 'sometimes|required|integer',
            'gambar' => 'sometimes|required',
            'nama_kategori' => 'sometimes|required',
            'deskripsi' => 'sometimes|required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $kategoriBisnis->update($request->all());

        return response()->json([
            "message" => "Data kategori bisnis berhasil diperbarui",
            "data" => $kategoriBisnis
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(KategoriBisnis $kategoriBisnis) // RMD aktif
    {
        $kategoriBisnis->delete();
        return response()->json([
            "message" => "Data kategori bisnis berhasil dihapus",
            "data" => null
        ], 200);
    }
}