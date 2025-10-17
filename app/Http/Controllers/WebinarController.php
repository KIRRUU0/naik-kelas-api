<?php

namespace App\Http\Controllers;

use App\Models\Webinar;
use Illuminate\Http\Request;
use Illuminate\support\Facades\Validator;
use DB; // Menggunakan DB untuk fungsi statistik

class WebinarController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
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
            'kategori_id' => 'required',
            'status_acara' => 'required',
            'judul_webinar' => 'required',
            'nama_mentor' => 'required',
            'tanggal_acara' => 'required',
            'waktu_mulai' => 'required',
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
    public function show(Webinar $webinar) // RMD aktif
    {
        // Logic find() dan is_null() otomatis ditangani oleh RMD
        return response()->json([
            "message" => "Data webinar berhasil diambil",
            "data" => $webinar
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Webinar $webinar) // RMD aktif
    {
        $validator = Validator::make($request->all(), [
            'kategori_id' => 'sometimes|required|integer', // FIX: sometimes dan integer
            'status_acara' => 'sometimes|required|integer', // FIX: sometimes dan integer
            'judul_webinar' => 'sometimes|required', // FIX: sometimes
            'nama_mentor' => 'sometimes|required', // FIX: sometimes
            'tanggal_acara' => 'sometimes|required', // FIX: sometimes
            'waktu_mulai' => 'sometimes|required', // FIX: sometimes
            'url_cta' => 'sometimes|required', // FIX: sometimes
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
    public function destroy(Webinar $webinar) // RMD aktif
    {
        $webinar->delete(); // Langsung delete model yang sudah di-bind
        return response()->json([
            "message" => "Data webinar berhasil dihapus"
        ], 200);
    }

    public function statistik()
    {
        $data_status = Webinar::select('status_acara', \DB::raw('count(*) as total'))
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