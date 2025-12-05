<?php

namespace App\Http\Controllers;

use App\Models\LayananUmum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LayananUmumController extends Controller
{
    private function getUploadBasePath()
    {
        // ✅ Untuk hosting environment (public_html/upload/layanan-umum/)
        return base_path('../../upload/layanan-umum/');
    }

    private function uploadGambar($file)
    {
        $uploadPath = $this->getUploadBasePath();
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }
        $filename = time() . '_' . $file->getClientOriginalName();
        $file->move($uploadPath, $filename);
        return 'layanan-umum/' . $filename;
    }

    private function deleteGambar($path)
    {
        if (!$path) return;
        $filename = basename($path);
        $fullPath = $this->getUploadBasePath() . $filename;
        if (file_exists($fullPath)) {
            @unlink($fullPath);
        }
    }
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
            'gambar' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'highlight' => 'nullable|string|max:100',
            'url_cta' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        try {
            // ✅ UPLOAD GAMBAR menggunakan method yang sama dengan PenggunaController
            $gambarPath = null;
            
            if ($request->hasFile('gambar')) {
                $gambarPath = $this->uploadGambar($request->file('gambar'));
            }

            // ✅ BUAT DATA
            $data = $request->all();
            $data['type'] = $request->type;
            $data['gambar'] = $gambarPath; // Simpan path gambar

            $layananUmum = LayananUmum::create($data);

            return response()->json([
                "message" => ucfirst($request->type) . " berhasil ditambahkan",
                "data" => $layananUmum
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
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, LayananUmum $layananUmum) // RMD aktif
    {
        $validator = Validator::make($request->all(), [
            // 'kategori_id' => 'sometimes|required|integer', // FIX: sometimes
            'judul_layanan' => 'sometimes|required',        // FIX: sometimes
            'deskripsi' => 'sometimes|required',            // FIX: sometimes
            'gambar' => 'sometimes|image|mimes:jpeg,png,jpg,gif,webp|max:2048', // FIX: sometimes
           'highlight' => 'sometimes|required|string|max:100',            // FIX: sometimes
            'url_cta' => 'sometimes|required',              // FIX: sometimes
        ]);

        if ($validator->fails()) {
        \Log::error('❌ VALIDATION FAILED', [$validator->errors()]);
        return response()->json($validator->errors(), 422);
    }

    try {
        $data = $request->all();

        // ✅ HANDLE GAMBAR UPLOAD - DENGAN CHECK LEBIH DETAIL
        if ($request->hasFile('gambar') && $request->file('gambar')->isValid()) {
            \Log::info('📸 VALID FILE DETECTED - Starting upload process');
            
            // Hapus gambar lama jika ada
            if ($layananUmum->gambar) {
                $this->deleteGambar($layananUmum->gambar);
            }

            // Upload gambar baru
            $gambarPath = $this->uploadGambar($request->file('gambar'));
            $data['gambar'] = $gambarPath;
            
            \Log::info('✅ NEW IMAGE UPLOADED: ' . $gambarPath);
        } else {
            \Log::warning('⚠️ FILE UPLOAD ISSUE', [
                'hasFile' => $request->hasFile('gambar'),
                'isValid' => $request->hasFile('gambar') ? $request->file('gambar')->isValid() : false,
                'error' => $request->hasFile('gambar') ? $request->file('gambar')->getError() : 'No file'
            ]);
        }

            \Log::info('💾 FINAL DATA FOR UPDATE:', $data);
            
            $layananUmum->update($data);
    
            return response()->json([
                "message" => "Data layanan umum berhasil diupdate",
                "data" => $layananUmum->fresh()
            ], 200);
    
        } catch (\Exception $e) {
            \Log::error('❌ UPDATE ERROR: ' . $e->getMessage());
            return response()->json([
                'message' => 'Terjadi kesalahan server',
                'error' => $e->getMessage()
            ], 500);
        }
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