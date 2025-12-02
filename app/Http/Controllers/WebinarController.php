<?php

namespace App\Http\Controllers;

use App\Models\Webinar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB; // Menggunakan Facade penuh

class WebinarController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Eager Loading dan Sorting sudah benar
        $webinar = Webinar::with('kategori')->orderBy('tanggal_acara', 'desc')->get();
        return response()->json([
            "message" => "Data webinar berhasil diambil",
            "data" => $webinar
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kategori_id' => 'required|integer|exists:kategori_bisnis,id', // Tambah: Cek FK valid
            'status_acara' => 'required|integer',
            'judul_webinar' => 'required',
            'tanggal_acara' => 'required|date',
            'waktu_mulai' => 'required|date_format:Y-m-d H:i:s', // Contoh format datetime
            'url_cta' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $webinar = Webinar::create($request->all());

        return response()->json([
            "message" => "Data webinar berhasil ditambahkan",   
            "data" => $webinar
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Webinar $webinar)
    {
        return response()->json([
            "message" => "Data webinar berhasil diambil",
            "data" => $webinar
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Webinar $webinar)
    {
        $validator = Validator::make($request->all(), [
            'kategori_id' => 'sometimes|required|integer|exists:kategori_bisnis,id',
            'status_acara' => 'sometimes|required|integer',
            'judul_webinar' => 'sometimes|required',
            'nama_mentor' => 'sometimes|required',
            'tanggal_acara' => 'sometimes|required|date',
            'waktu_mulai' => 'sometimes|required|date_format:Y-m-d H:i:s',
            'url_cta' => 'sometimes|required',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        $webinar->update($request->all()); 
        return response()->json([
            "message" => "Data webinar berhasil diupdate",
            "data" => $webinar
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Webinar $webinar)
    {
        $webinar->delete(); 
        // Konsistensi Response: Hanya message
        return response()->json([
            "message" => "Data webinar berhasil dihapus"
        ], 200);
    }

    /**
     * Mengambil statistik status acara webinar.
     */
    public function statistik()
    {
        // Menggunakan Facade DB yang diimport
        $data_status = Webinar::select('status_acara', DB::raw('count(*) as total'))
            ->groupBy('status_acara')
            ->get();
            
        return response()->json([
            'label' => ['Selesai', 'Sedang Berlangsung', 'Akan Datang'],
            'message' => 'Statistik status acara webinar',
            'data' => [
                   $data_status->where('status_acara', 0)->first()->total ?? 0, 
                   $data_status->where('status_acara', 1)->first()->total ?? 0, 
                   $data_status->where('status_acara', 2)->first()->total ?? 0
            ]
        ], 200);
    }
}