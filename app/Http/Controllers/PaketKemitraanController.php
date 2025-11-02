<?php

namespace App\Http\Controllers;

use App\Models\PaketKemitraan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PaketKemitraanController extends Controller
{
    /**
     * BASE PATH untuk upload file gambar paket kemitraan
     * Sesuai dengan struktur hosting (public_html/upload/paket-kemitraan/)
     */
    private function getUploadBasePath()
    {
        // ✅ Untuk hosting environment (public_html/upload/paket-kemitraan/)
        return base_path('../public_html/upload/paket-kemitraan/');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $modul = PaketKemitraan::all();
        return response()->json([
            "message" => "Data paket kemitraan berhasil diambil",
            "data" => $modul
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_paket' => 'required',
            'gambar' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048', // ✅ UPDATE: file validation
            'deskripsi' => 'required',
            'fitur_unggulan' => 'required',
            'harga' => 'required|integer',
            'url_cta' => 'required',
        ]);
        
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        
        try {
            // ✅ UPLOAD GAMBAR
            $gambarPath = null;
            
            if ($request->hasFile('gambar')) {
                $gambarPath = $this->uploadGambar($request->file('gambar'));
            }

            // ✅ BUAT DATA
            $data = $request->only([
                'nama_paket', 
                'deskripsi', 
                'fitur_unggulan', 
                'harga', 
                'url_cta'
            ]);
            $data['gambar'] = $gambarPath; // Simpan path gambar

            $paketKemitraan = PaketKemitraan::create($data);
            
            return response()->json([
                "message" => "Data paket kemitraan berhasil ditambahkan",
                "data" => $paketKemitraan
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
     */
    public function show(PaketKemitraan $paketKemitraan)
    {
        return response()->json([
            "message" => "Data paket kemitraan berhasil diambil",
            "data" => $paketKemitraan
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id) // ✅ PASTIKAN pakai $id
    {
        // ✅ FIND DATA TERLEBIH DAHULU
        $paketKemitraan = PaketKemitraan::find($id);
        
        if (!$paketKemitraan) {
            return response()->json([
                'message' => 'Data paket kemitraan tidak ditemukan'
            ], 404);
        }
    
        $validator = Validator::make($request->all(), [
            'nama_paket' => 'sometimes|required',
            'gambar' => 'sometimes|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'deskripsi' => 'sometimes|required', 
            'fitur_unggulan' => 'sometimes|required',
            'harga' => 'sometimes|required|integer',
            'url_cta' => 'sometimes|required',
        ]);
        
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        
        try {
            \Log::info('🔄 UPDATE PaketKemitraan Request', [
                'id' => $id,
                'has_file' => $request->hasFile('gambar'),
                'all_data' => $request->all()
            ]);
    
            $data = $request->all(); // ✅ AMBIL SEMUA DATA
    
            // ✅ HANDLE GAMBAR UPLOAD
            if ($request->hasFile('gambar') && $request->file('gambar')->isValid()) {
                \Log::info('📸 Processing image upload for update');
                
                // Hapus gambar lama jika ada
                if ($paketKemitraan->gambar) {
                    $this->deleteGambar($paketKemitraan->gambar);
                }
    
                // Upload gambar baru
                $gambarPath = $this->uploadGambar($request->file('gambar'));
                $data['gambar'] = $gambarPath;
                
                \Log::info('✅ New image uploaded: ' . $gambarPath);
            }
    
            \Log::info('💾 Updating data with: ', $data);
            
            $paketKemitraan->update($data);
    
            return response()->json([
                "message" => "Data paket kemitraan berhasil diupdate",
                "data" => $paketKemitraan->fresh()
            ], 200);
    
        } catch (\Exception $e) {
            \Log::error('❌ UPDATE ERROR PaketKemitraan: ' . $e->getMessage());
            return response()->json([
                'message' => 'Terjadi kesalahan server',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PaketKemitraan $paketKemitraan)
    {
        try {
            // ✅ HAPUS GAMBAR jika ada
            if ($paketKemitraan->gambar) {
                $this->deleteGambar($paketKemitraan->gambar);
            }
            
            $paketKemitraan->delete();
            
            return response()->json([
                "message" => "Data paket kemitraan berhasil dihapus"
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal menghapus data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * UPLOAD GAMBAR - untuk Paket Kemitraan
     */
    private function uploadGambar($file)
    {
        try {
            $uploadDir = $this->getUploadBasePath();
            
            // ✅ Pastikan folder upload ada
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            // ✅ Generate unique filename
            $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $fullPath = $uploadDir . $fileName;
            
            // ✅ Upload file
            if ($file->move($uploadDir, $fileName)) {
                // ✅ Set permission agar file bisa diakses via web
                chmod($fullPath, 0644);
                return $fileName;
            } else {
                throw new \Exception('Gagal memindahkan file');
            }
            
        } catch (\Exception $e) {
            throw new \Exception('Upload gagal: ' . $e->getMessage());
        }
    }

    /**
     * DELETE GAMBAR - untuk Paket Kemitraan
     */
    private function deleteGambar($fileName)
    {
        if (!$fileName) {
            return false;
        }

        try {
            $filePath = $this->getUploadBasePath() . $fileName;
            
            if (file_exists($filePath)) {
                return unlink($filePath);
            }
            return false;
            
        } catch (\Exception $e) {
            return false;
        }
    }
}