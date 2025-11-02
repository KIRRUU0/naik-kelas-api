<?php

namespace App\Http\Controllers;

use App\Models\Tentang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class TentangController extends Controller
{
    /**
     * Display a listing of the resource.
     * GET /api/tentang
     */
    public function index()
    {
        try {
            $tentangData = Tentang::all();
            
            return response()->json([
                'status' => 'success',
                'message' => 'Data tentang berhasil diambil',
                'data' => $tentangData
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan server',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     * POST /api/tentang
     */
    public function store(Request $request)
    {
        // Cek autentikasi - hanya admin/super_admin
        if (!Auth::check() || !in_array(Auth::user()->role, ['admin', 'super_admin'])) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized. Hanya Admin yang dapat menambah data tentang.'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'judul' => 'required|string|max:255',
            'sub_judul' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'gambar' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $data = $request->all();

            // Handle file upload untuk gambar
            if ($request->hasFile('gambar')) {
                $data['gambar'] = $request->file('gambar')->store('tentang', 'public');
            }

            $tentang = Tentang::create($data);

            return response()->json([
                'status' => 'success',
                'message' => 'Data tentang berhasil ditambahkan',
                'data' => $tentang
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan server',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     * GET /api/tentang/{id}
     */
    public function show($id)
    {
        try {
            $tentang = Tentang::find($id);

            if (!$tentang) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data tentang tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Data tentang berhasil diambil',
                'data' => $tentang
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan server',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     * PUT /api/tentang atau PUT /api/tentang/{id}
     */
    public function update(Request $request, $id = null)
    {
        // Cek autentikasi - hanya admin/super_admin
        if (!Auth::check() || !in_array(Auth::user()->role, ['admin', 'super_admin'])) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized. Hanya Admin yang dapat mengupdate data tentang.'
            ], 403);
        }

        // Jika tidak ada ID, update record pertama
        if (!$id) {
            $tentang = Tentang::first();
            if (!$tentang) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data tentang tidak ditemukan untuk diupdate'
                ], 404);
            }
            $id = $tentang->id;
        }

        $tentang = Tentang::find($id);
        if (!$tentang) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data tentang tidak ditemukan'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'judul' => 'sometimes|string|max:255',
            'sub_judul' => 'sometimes|string|max:255',
            'deskripsi' => 'sometimes|string',
            'gambar' => 'sometimes|image|mimes:jpeg,png,jpg,gif,webp|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $data = $request->all();

            // Handle file upload untuk gambar
            if ($request->hasFile('gambar')) {
                // Hapus gambar lama jika ada
                if ($tentang->gambar && Storage::disk('public')->exists($tentang->gambar)) {
                    Storage::disk('public')->delete($tentang->gambar);
                }
                $data['gambar'] = $request->file('gambar')->store('tentang', 'public');
            }

            $tentang->update($data);

            return response()->json([
                'status' => 'success',
                'message' => 'Data tentang berhasil diupdate',
                'data' => $tentang
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan server',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     * DELETE /api/tentang/{id}
     */
    public function destroy($id)
    {
        // Cek autentikasi - hanya admin/super_admin
        if (!Auth::check() || !in_array(Auth::user()->role, ['admin', 'super_admin'])) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized. Hanya Admin yang dapat menghapus data tentang.'
            ], 403);
        }

        try {
            $tentang = Tentang::find($id);

            if (!$tentang) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data tentang tidak ditemukan'
                ], 404);
            }

            // Hapus gambar jika ada
            if ($tentang->gambar && Storage::disk('public')->exists($tentang->gambar)) {
                Storage::disk('public')->delete($tentang->gambar);
            }

            $tentang->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Data tentang berhasil dihapus'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan server',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}