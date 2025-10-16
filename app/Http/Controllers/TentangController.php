<?php

namespace App\Http\Controllers;

use App\Models\Tentang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TentangController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tentang = Tentang::all();
        return response()->json([
        "message" => "Data tentang berhasil diambil",
        "data" => $tentang
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'judul' => 'required',
            'sub_judul' => 'required',
            'deskripsi' => 'required',
            'gambar' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        $tentang = Tentang::create($request->all());
        return response()->json([
            "message" => "Data tentang berhasil ditambahkan",
            "data" => $tentang
        ], 201);
        
    }

    /**
     * Display the specified resource.
     */
    public function show(Tentang $tentang) // RMD aktif
    {
        // Query redundan dihapus
        return response()->json([
            "message" => "Data tentang berhasil diambil",
            "data" => $tentang
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Tentang $tentang) // RMD aktif
    {
        $validator = Validator::make($request->all(), [
            'judul' => 'sometimes|required',
            'sub_judul' => 'sometimes|required',
            'deskripsi' => 'sometimes|required',
            'gambar' => 'sometimes|required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $tentang->update($request->all());

        return response()->json([
            "message" => "Data tentang berhasil diupdate",
            "data" => $tentang
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tentang $tentang) // RMD aktif
    {
        $tentang->delete();
        return response()->json([
            "message" => "Data tentang berhasil dihapus",
            "data" => null
        ], 200);
    }
}