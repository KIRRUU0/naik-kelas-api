<?php

namespace App\Http\Controllers;

use App\Models\LayananBisnis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class LayananBisnisController extends Controller
{
    /**
     * BASE PATH untuk upload file gambar layanan bisnis
     * Sesuai dengan struktur hosting (public_html/upload/layanan-bisnis/)
     */
    private function getUploadBasePath()
    {
        // ✅ Untuk hosting environment (public_html/upload/layanan-bisnis/)
        return base_path('../public_html/upload/layanan-bisnis/');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = LayananBisnis::query();
        
        if ($request->type && $request->type !== '') {
            $types = explode(',', $request->type);
            $query->whereIn('type', $types);
        }
        
        $layananBisnis = $query->get();
        
        return response()->json([
            "message" => "Data layanan bisnis berhasil diambil",
            "data" => $layananBisnis
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
        'kategori_id' => 'sometimes|nullable|integer',
        'type' => 'required|in:trading,webinar,reseller,modal bisnis',
        'tipe_broker' => 'required_if:type,trading',
        'judul_bisnis' => 'required',
        'gambar' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        'deskripsi' => 'required',
        'fitur_unggulan' => 'required',
        'harga' => 'nullable|integer',
        'url_cta' => 'required',
        // ✅ FIELD KHUSUS WEBINAR
        'tanggal_acara' => 'required_if:type,webinar|date',
        'waktu_mulai' => 'required_if:type,webinar',
        'nama_mentor' => 'required_if:type,webinar',
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

            $layananBisnis = LayananBisnis::create($data);

            return response()->json([
                "message" => ucfirst($request->type) . " berhasil ditambahkan",
                "data" => $layananBisnis
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
     * UPLOAD GAMBAR - FIXED VERSION untuk Hosting Environment
     * Cara kerja sama dengan upload foto_profil di PenggunaController
     */
    private function uploadGambar($file)
{
    try {
        $uploadDir = $this->getUploadBasePath();
        
        // ✅ Pastikan folder upload ada
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
            error_log("✅ Created upload directory: " . $uploadDir);
        }
        
        // ✅ Cek permission folder
        if (!is_writable($uploadDir)) {
            error_log("❌ Directory not writable: " . $uploadDir);
            throw new \Exception('Directory tidak writable');
        }
        
        // ✅ Generate unique filename
        $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $fullPath = $uploadDir . $fileName;
        
        // Debug info
        error_log("📁 Upload Directory: " . $uploadDir);
        error_log("📄 File Name: " . $fileName);
        error_log("📄 Original Name: " . $file->getClientOriginalName());
        error_log("📄 File Size: " . $file->getSize());
        error_log("🎯 Full Path: " . $fullPath);
        
        // ✅ Upload file
        if ($file->move($uploadDir, $fileName)) {
            // ✅ Set permission agar file bisa diakses via web
            chmod($fullPath, 0644);
            
            // ✅ Verify file exists after upload
            if (file_exists($fullPath)) {
                error_log("✅ Gambar uploaded successfully: " . $fileName);
                error_log("✅ File exists at: " . $fullPath);
                error_log("🌐 Accessible URL: " . url('upload/layanan-bisnis/' . $fileName));
            } else {
                error_log("❌ File not found after upload: " . $fullPath);
                throw new \Exception('File tidak ditemukan setelah upload');
            }
            
            return $fileName;
        } else {
            error_log("❌ Failed to move uploaded file");
            throw new \Exception('Gagal memindahkan file');
        }
        
    } catch (\Exception $e) {
        error_log("❌ Upload error: " . $e->getMessage());
        throw new \Exception('Upload gagal: ' . $e->getMessage());
    }
}

    /**
     * DELETE GAMBAR - FIXED VERSION untuk Hosting Environment
     */
    private function deleteGambar($fileName)
    {
        if (!$fileName) {
            return false;
        }

        try {
            $filePath = $this->getUploadBasePath() . $fileName;
            
            error_log("🗑️ Attempting to delete gambar: " . $filePath);
            
            if (file_exists($filePath)) {
                if (unlink($filePath)) {
                    error_log("✅ Gambar deleted successfully: " . $filePath);
                    return true;
                } else {
                    error_log("❌ Failed to delete gambar: " . $filePath);
                    return false;
                }
            } else {
                error_log("⚠️ Gambar not found: " . $filePath);
                return false;
            }
        } catch (\Exception $e) {
            error_log("❌ Delete error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $layananBisnis = LayananBisnis::find($id);
        
        if (!$layananBisnis) {
            return response()->json([
                "message" => "Data modul bisnis tidak ditemukan",
                "data" => null
            ], 404);
        }
        
        return response()->json([
            "message" => "Data modul bisnis berhasil diambil",
            "data" => $layananBisnis
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
   public function update(Request $request, $id)
{
    $layananBisnis = LayananBisnis::find($id);
    
    if (!$layananBisnis) {
        return response()->json(['message' => 'Data tidak ditemukan'], 404);
    }

    // ✅ DEBUG DETAILED REQUEST
    \Log::info('🔍 DETAILED UPDATE REQUEST DEBUG', [
        'method' => $request->method(),
        'headers' => $request->headers->all(),
        'content_type' => $request->header('Content-Type'),
        'all_input' => $request->all(),
        'files' => $request->allFiles(),
        'has_file_gambar' => $request->hasFile('gambar'),
        'file_gambar' => $request->file('gambar') ? [
            'name' => $request->file('gambar')->getClientOriginalName(),
            'size' => $request->file('gambar')->getSize(),
            'mime' => $request->file('gambar')->getMimeType(),
            'isValid' => $request->file('gambar')->isValid(),
        ] : null
    ]);

    $validator = Validator::make($request->all(), [
        'kategori_id' => 'sometimes|nullable|integer',
        'type' => 'sometimes|required|in:trading,webinar,reseller,modal bisnis',
        'tipe_broker' => 'sometimes|required_if:type,trading',
        'judul_bisnis' => 'sometimes|required',
        'gambar' => 'sometimes|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        'deskripsi' => 'sometimes|required',
        'fitur_unggulan' => 'sometimes|required',
        'harga' => 'sometimes|nullable|integer',
        'url_cta' => 'sometimes|required',
        'tanggal_acara' => 'sometimes|required_if:type,webinar|date',
        'waktu_mulai' => 'sometimes|required_if:type,webinar',
        'nama_mentor' => 'sometimes|required_if:type,webinar',
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
            if ($layananBisnis->gambar) {
                $this->deleteGambar($layananBisnis->gambar);
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
        
        $layananBisnis->update($data);

        return response()->json([
            "message" => "Data layanan bisnis berhasil diupdate",
            "data" => $layananBisnis->fresh()
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
    public function destroy($id)
    {
        try {
            $layananBisnis = LayananBisnis::find($id);
            
            if (!$layananBisnis) {
                return response()->json([
                    'message' => 'Data tidak ditemukan'
                ], 404);
            }
            
            // ✅ HAPUS GAMBAR jika ada (menggunakan method yang sama)
            if ($layananBisnis->gambar) {
                $this->deleteGambar($layananBisnis->gambar);
            }
            
            $layananBisnis->delete();
            
            return response()->json([
                'message' => 'Data modul bisnis berhasil dihapus',
                'data' => $layananBisnis
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal menghapus data',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Delete By Type
     */
    public function deleteByType(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'message' => 'Unauthorized. Silakan login terlebih dahulu.'
            ], 401);
        }

        $user = Auth::user();
        if (!in_array($user->role, ['admin', 'super_admin'])) {
            return response()->json([
                'message' => 'Unauthorized. Hanya admin/super_admin yang dapat menghapus.'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'type' => 'required|in:trading,webinar,reseller,modal bisnis'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $type = $request->type;
        
        // ✅ HAPUS GAMBAR TERLEBIH DAHULU sebelum delete data
        $dataToDelete = LayananBisnis::where('type', $type)->get();
        
        foreach ($dataToDelete as $item) {
            if ($item->gambar) {
                $this->deleteGambar($item->gambar);
            }
        }
        
        $count = LayananBisnis::where('type', $type)->count();
        
        if ($count === 0) {
            return response()->json([
                'message' => "Tidak ada data dengan type '$type'"
            ], 404);
        }

        // Delete semua data dengan type tersebut
        LayananBisnis::where('type', $type)->delete();

        return response()->json([
            'message' => "Berhasil menghapus $count data dengan type '$type'",
            'deleted_count' => $count,
            'type' => $type
        ], 200);
    }
}