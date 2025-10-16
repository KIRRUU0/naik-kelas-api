<?php

namespace App\Http\Controllers;

use App\Models\Home;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class HomeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Ambil data home, asumsikan hanya ada satu record
        $home = Home::all();
        
        if ($home->isEmpty()) {
            return response()->json([
                "message" => "Pengaturan Home belum diinisialisasi",
                "data" => null
            ], 200);
        }

        
        return response()->json([
            "message" => "Data home berhasil diambil",
            "data" => $home
        ], 200);

    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tagline_brand' => 'required',
            'deskripsi_brand' => 'required',
            'gambar_brand' => 'required',
            'url_cta' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $home = Home::create($request->only([
            'tagline_brand',
            'deskripsi_brand',
            'gambar_brand',
            'url_cta',
        ]));

        return response()->json([
            "message" => "Pengaturan Home berhasil diinisialisasi",
            "data" => $home
        ], 201);
        
    }

    /**
     * Display the specified resource.
     */
    public function show(Home $home)
    {
        return response()->json([
            "message" => "Data home berhasil diambil",
            "data" => $home
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Home $home)
    {
        $validator = Validator::make($request->all(), [
            'tagline_brand' => 'required|string|max:255',
            'deskripsi_brand' => 'required|string',
            'gambar_brand' => 'required|string|max:255',
            'url_cta' => 'required|string',
        ]);
        
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Cari data pertama (ID 1), jika ada perbarui, jika tidak ada buat baru.
        $home = Home::updateOrCreate(
            ['id' => 1],
            $request->only([
                'tagline_brand',
                'deskripsi_brand',
                'gambar_brand',
                'url_cta',
            ])
        );

        return response()->json([
            "message" => "Pengaturan Home berhasil diperbarui",
            "data" => $home
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Home $home) // RMD aktif
    {
        // Query redundan dihapus
        $home->delete();
        return response()->json([
            "message" => "Data home berhasil dihapus"
        ], 200);
    }
}