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
    public function show(LayananBisnis $layananBisnis)
    {
        $modul = LayananBisnis::find($layananBisnis->id);

        if (is_null($modul)) {
            return response()->json([
                "message" => "Data modul bisnis tidak ditemukan"
            ], 404);
        }

        return response()->json([
            "message" => "Data modul bisnis berhasil diambil",
            "data" => $modul
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ModulBisnis $modulBisnis)
    {
        $modul = LayananBisnis::find($layananBisnis->id);
        if (is_null($modul)) {
            return response()->json([
                "message" => "Data modul bisnis tidak ditemukan"
            ], 404);
        }

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

        $modul->update($request->all());

        return response()->json([
            "message" => "Data modul bisnis berhasil diupdate",
            "data" => $modul
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(LayananBisnis $layananBisnis)
    {
        $modul = LayananBisnis::find($layananBisnis->id);
        if (is_null($modul)) {
            return response()->json([
                "message" => "Data modul bisnis tidak ditemukan"
            ], 404);
        }
        $modul->delete();
        return response()->json([
            "message" => "Data modul bisnis berhasil dihapus"
        ], 200);
    }
}
