<?php

namespace App\Http\Controllers;

use App\Models\LayananUmum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LayananUmumController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
    $layananUmum = LayananUmum::all();
    return response()->json([
        "message" => "Data layanan umum berhasil diambil",
        "data" => $layananUmum
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // 'kategori_id' => 'required|integer',
            'judul_layanan' => 'required',
            'deskripsi' => 'required',
            'highlight' => 'nullable|string|max:100',
            'url_cta' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        $layananUmum = LayananUmum::create($request->all());
        return response()->json([
            "message" => "Data layanan umum berhasil ditambahkan",
            "data" => $layananUmum
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(LayananUmum $layananUmum) // RMD aktif
    {
        // Query redundan dihapus
        return response()->json([
            "message" => "Data layanan umum berhasil diambil",
            "data" => $layananUmum
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, LayananUmum $layananUmum) // RMD aktif
    {
        $validator = Validator::make($request->all(), [
            // 'kategori_id' => 'sometimes|required|integer', // FIX: sometimes
            'judul_layanan' => 'sometimes|required',        // FIX: sometimes
            'deskripsi' => 'sometimes|required',            // FIX: sometimes
           'highlight' => 'sometimes|required|string|max:100',            // FIX: sometimes
            'url_cta' => 'sometimes|required',              // FIX: sometimes
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $layananUmum->update($request->all());
        return response()->json([
            "message" => "Data layanan umum berhasil diupdate",
            "data" => $layananUmum
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(LayananUmum $layananUmum) // RMD aktif
    {
        // Query redundan dihapus
        $layananUmum->delete();
        return response()->json([
            "message" => "Data layanan umum berhasil dihapus"
        ], 200);
    }
}