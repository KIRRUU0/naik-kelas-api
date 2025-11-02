<?php

namespace App\Http\Controllers;

use App\Models\MitraBroker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MitraBrokerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $mitraBrokers = MitraBroker::with('kategori')->get();
        return response()->json([
        "message" => "Data mitra broker berhasil diambil",
        "data" => $mitraBrokers
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tipe_broker' => 'required|integer',
            'kategori_id' => 'required|integer|exists:kategori_bisnis,id',
            'judul_broker' => 'required',
            'gambar' => 'required',
            'deskripsi' => 'required',
            'fitur_unggulan' => 'required|string',
            'url_cta' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        
        // Logika sederhana: Model::create($request->all()) - Aman karena kolom redundan sudah dihapus dari DB
        $mitraBroker = MitraBroker::create($validator->validated()); 
        
        return response()->json([
            "message" => "Data mitra broker berhasil ditambahkan",
            "data" => $mitraBroker
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(MitraBroker $mitraBroker)
    {
        return response()->json([
            "message" => "Data mitra broker berhasil diambil",
            "data" => $mitraBroker
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MitraBroker $mitraBroker)
    {
        $validator = Validator::make($request->all(), [
            'tipe_broker' => 'sometimes|required|integer',
            'kategori_id' => 'sometimes|required|integer|exists:kategori_bisnis,id',
            'judul_broker' => 'sometimes|required',
            'gambar' => 'sometimes|required',
            // REVISI KRITIS: Validasi 'nama_kategori' dihapus
            'deskripsi' => 'sometimes|required',
            'fitur_unggulan' => 'sometimes|required|string', 
            'url_cta' => 'sometimes|required',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        $mitraBroker->update($request->all());
        return response()->json([
            "message" => "Data mitra broker berhasil diupdate",
            "data" => $mitraBroker
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MitraBroker $mitraBroker)
    {
        $mitraBroker->delete();
        return response()->json([
            "message" => "Data mitra broker berhasil dihapus"
        ], 200);
    }
}