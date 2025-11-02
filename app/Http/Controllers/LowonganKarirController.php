<?php

namespace App\Http\Controllers;

use App\Models\LowonganKarir;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LowonganKarirController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $lowonganKarir = LowonganKarir::all();
        return response()->json([
        "message" => "Data lowongan karir berhasil diambil",
        "data" => $lowonganKarir
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'posisi' => 'required',
            'status' => 'required',
            'deskripsi' => 'required',
            'url_cta' => 'required',
        ]); 
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        $lowonganKarir = LowonganKarir::create($request->all());
        return response()->json([
            "message" => "Data lowongan karir berhasil ditambahkan",
            "data" => $lowonganKarir
        ], 201);       
    }

    /**
     * Display the specified resource.
     */
    public function show(LowonganKarir $lowonganKarir) // RMD aktif
    {
        // Query redundan dihapus
        return response()->json([
            "message" => "Data lowongan karir berhasil diambil",
            "data" => $lowonganKarir
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, LowonganKarir $lowonganKarir) // RMD aktif
    {
        $validator = Validator::make($request->all(), [
            'posisi' => 'sometimes|required', // FIX: sometimes
            'status' => 'sometimes|required|in:dibuka,ditutup',
            'deskripsi' => 'sometimes|required', // FIX: sometimes
            'url_cta' => 'sometimes|required', // FIX: sometimes
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        $lowonganKarir->update($request->all());
        return response()->json([
            "message" => "Data lowongan karir berhasil diupdate",
            "data" => $lowonganKarir
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(LowonganKarir $lowonganKarir) // RMD aktif
    {
        // Query redundan dihapus
        $lowonganKarir->delete();
        return response()->json([
            "message" => "Data lowongan karir berhasil dihapus"
        ], 200);
    }
}