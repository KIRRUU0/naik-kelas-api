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
        $mitraBrokers = MitraBroker::all();
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
            'tipe_broker' => 'required',
            'kategori_id' => 'required',
            'judul_broker' => 'required', // FIX: Menambahkan validasi field 'judul_broker'
            'gambar' => 'required',
            'nama_kategori' => 'required',
            'deskripsi' => 'required',
            'fitur_unggulan' => 'required',
            'url_cta' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        $mitraBroker = MitraBroker::create($request->all());
        return response()->json([
            "message" => "Data mitra broker berhasil ditambahkan",
            "data" => $mitraBroker
        ], 201);

    }

    /**
     * Display the specified resource.
     */
    public function show(MitraBroker $mitraBroker) // RMD aktif
    {
        // Query redundan dihapus
        return response()->json([
            "message" => "Data mitra broker berhasil diambil",
            "data" => $mitraBroker
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MitraBroker $mitraBroker) // RMD aktif
    {
        $validator = Validator::make($request->all(), [
            'tipe_broker' => 'required',
            'kategori_id' => 'required',
            'judul_broker' => 'required', // FIX: Menambahkan validasi field 'judul_broker'
            'gambar' => 'required',
            'nama_kategori' => 'required',
            'deskripsi' => 'required',
            'fitur_unggulan' => 'required',
            'url_cta' => 'required',
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
    public function destroy(MitraBroker $mitraBroker) // RMD aktif
    {
        // Query redundan dihapus
        $mitraBroker->delete();
        return response()->json([
            "message" => "Data mitra broker berhasil dihapus"
        ], 200);
    }
}