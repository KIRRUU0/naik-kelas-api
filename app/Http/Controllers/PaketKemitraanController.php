<?php

namespace App\Http\Controllers;

use App\Models\PaketKemitraan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PaketKemitraanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $paketKemitraan = PaketKemitraan::with('kategori')->get();
        return response()->json([
        "message" => "Data paket kemitraan berhasil diambil",
        "data" => $paketKemitraan
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kategori_id' => 'required|integer',
            'nama_paket' => 'required',
            'gambar' => 'required',
            'deskripsi' => 'required',
            'fitur_unggulan' => 'required',
            'harga' => 'required|integer',
            'status' => 'required|integer',
            'url_cta' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        $paketKemitraan = PaketKemitraan::create($request->all());
        return response()->json([
            "message" => "Data paket kemitraan berhasil ditambahkan",
            "data" => $paketKemitraan
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(PaketKemitraan $paketKemitraan) // RMD aktif
    {
        // Query redundan dihapus
        return response()->json([
            "message" => "Data paket kemitraan berhasil diambil",
            "data" => $paketKemitraan
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PaketKemitraan $paketKemitraan) // RMD aktif
    {
        $validator = Validator::make($request->all(), [
            'kategori_id' => 'sometimes|required|integer', // FIX: sometimes dan integer
            'nama_paket' => 'sometimes|required', // FIX: sometimes
            'gambar' => 'sometimes|required', // FIX: sometimes
            'deskripsi' => 'sometimes|required', // FIX: sometimes
            'fitur_unggulan' => 'sometimes|required', // FIX: sometimes
            'harga' => 'sometimes|required|integer', // FIX: sometimes dan integer
            'status' => 'sometimes|required|integer', // FIX: sometimes dan integer
            'url_cta' => 'sometimes|required', // FIX: sometimes
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        
        $paketKemitraan->update($request->all());
        return response()->json([
            "message" => "Data paket kemitraan berhasil diupdate",
            "data" => $paketKemitraan
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PaketKemitraan $paketKemitraan) // RMD aktif
    {
        // Query redundan dihapus
        $paketKemitraan->delete();
        return response()->json([
            "message" => "Data paket kemitraan berhasil dihapus"
        ], 200);
    }
}