<?php

namespace App\Http\Controllers;

use App\Models\LayananBisnis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LayananBisnisController extends Controller
{
    /**
     * BASE PATH untuk upload file gambar layanan bisnis
     * Sesuai dengan struktur hosting (public_html/upload/layanan-bisnis/)
     */
    private const UPLOAD_DIR = '../public_html/upload/layanan-bisnis/';
    
    /**
     * Validasi rules untuk store
     */
    private const STORE_RULES = [
        'kategori_id' => 'sometimes|nullable|integer',
        'type' => 'required|in:trading,webinar,jasa_recruitment,modal_bisnis,workshop',
        'tipe_broker' => 'required_if:type,trading',
        'judul_bisnis' => 'sometimes|required',
        'gambar' => 'sometimes|required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        'deskripsi' => 'required',
        'fitur_unggulan' => 'required',
        'harga' => 'sometimes|nullable|integer',
        'url_cta' => 'required',
    ];
    
    /**
     * Validasi rules untuk update
     */
    private const UPDATE_RULES = [
        'kategori_id' => 'sometimes|nullable|integer',
        'type' => 'required|in:trading,webinar,jasa_recruitment,modul_bisnis,workshop',
        'tipe_broker' => 'sometimes|required_if:type,trading',
        'judul_bisnis' => 'sometimes|required',
        'gambar' => 'sometimes|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        'deskripsi' => 'sometimes|required',
        'fitur_unggulan' => 'sometimes|required',
        'harga' => 'sometimes|nullable|integer',
        'url_cta' => 'sometimes|required',
    ];
    
    /**
     * Tipe layanan bisnis yang valid
     */
    private const VALID_TYPES = [
        'trading', 'webinar', 'jasa_recruitment', 
        'modal_bisnis', 'workshop', 'modul_bisnis'
    ];

    /**
     * Mendapatkan base path untuk upload
     */
    private function getUploadBasePath(): string
    {
        return base_path(self::UPLOAD_DIR);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = LayananBisnis::query();
        
        if ($request->filled('type')) {
            $types = explode(',', $request->type);
            $query->whereIn('type', $types);
        }
        
        $layananBisnis = $query->get();
        
        return response()->json([
            'message' => 'Data layanan bisnis berhasil diambil',
            'data' => $layananBisnis
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), self::STORE_RULES);
        
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
    
        try {
            // Upload gambar
            $gambarPath = $this->uploadGambar($request->file('gambar'));
            
            // Persiapkan data
            $data = $request->all();
            $data['gambar'] = $gambarPath;
            
            // Buat record
            $layananBisnis = LayananBisnis::create($data);
            
            $typeLabel = ucfirst($request->type);
            
            return response()->json([
                'message' => "{$typeLabel} berhasil ditambahkan",
                'data' => $layananBisnis
            ], 201);
            
        } catch (\Exception $e) {
            Log::error('Store Error: ' . $e->getMessage());
            
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan server',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload gambar
     */
    private function uploadGambar($file): string
    {
        $uploadDir = $this->getUploadBasePath();
        
        // Buat direktori jika belum ada
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
            Log::info("Created upload directory: {$uploadDir}");
        }
        
        // Cek permission
        if (!is_writable($uploadDir)) {
            Log::error("Directory not writable: {$uploadDir}");
            throw new \Exception('Directory tidak writable');
        }
        
        // Generate nama file unik
        $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $fullPath = $uploadDir . $fileName;
        
        // Log info upload
        Log::info('Uploading file', [
            'original_name' => $file->getClientOriginalName(),
            'file_size' => $file->getSize(),
            'target_path' => $fullPath
        ]);
        
        // Pindahkan file
        if (!$file->move($uploadDir, $fileName)) {
            Log::error('Failed to move uploaded file');
            throw new \Exception('Gagal memindahkan file');
        }
        
        // Set permission
        chmod($fullPath, 0644);
        
        // Verifikasi file
        if (!file_exists($fullPath)) {
            Log::error("File not found after upload: {$fullPath}");
            throw new \Exception('File tidak ditemukan setelah upload');
        }
        
        Log::info("Image uploaded successfully: {$fileName}");
        
        return $fileName;
    }

    /**
     * Hapus gambar dari storage
     */
    private function deleteGambar(string $fileName): bool
    {
        if (empty($fileName)) {
            return false;
        }
        
        $filePath = $this->getUploadBasePath() . $fileName;
        
        try {
            if (file_exists($filePath)) {
                if (unlink($filePath)) {
                    Log::info("Gambar deleted successfully: {$filePath}");
                    return true;
                }
                
                Log::error("Failed to delete gambar: {$filePath}");
                return false;
            }
            
            Log::warning("Gambar not found: {$filePath}");
            return false;
            
        } catch (\Exception $e) {
            Log::error("Delete error: {$e->getMessage()}");
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
                'message' => 'Data modul bisnis tidak ditemukan',
                'data' => null
            ], 404);
        }
        
        return response()->json([
            'message' => 'Data modul bisnis berhasil diambil',
            'data' => $layananBisnis
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $layananBisnis = LayananBisnis::find($id);
        
        if (!$layananBisnis) {
            return response()->json([
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        // Log request untuk debugging
        $this->logUpdateRequest($request);
        
        $validator = Validator::make($request->all(), self::UPDATE_RULES);
        
        if ($validator->fails()) {
            Log::error('Validation failed', $validator->errors()->toArray());
            return response()->json($validator->errors(), 422);
        }

    try {
        $data = $request->all();
        
        // Handle gambar upload
        if ($request->hasFile('gambar')) {
            $file = $request->file('gambar');
            if ($file->isValid()) {
                // Hapus gambar lama
                $this->deleteOldGambar($layananBisnis->gambar);
                // Upload gambar baru
                $gambarPath = $this->uploadGambar($file);
                $data['gambar'] = $gambarPath;
            }
        }
        
        // Update data
        $layananBisnis->update($data);
        
        return response()->json([
            'message' => 'Data layanan bisnis berhasil diupdate',
            'data' => $layananBisnis->fresh()
        ], 200);
        
    } catch (\Exception $e) {
            Log::error('Update error: ' . $e->getMessage());
            
            return response()->json([
                'message' => 'Terjadi kesalahan server',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Log informasi request untuk debugging
     */
    private function logUpdateRequest(Request $request): void
    {
        $fileInfo = $request->hasFile('gambar') ? [
            'name' => $request->file('gambar')->getClientOriginalName(),
            'size' => $request->file('gambar')->getSize(),
            'mime' => $request->file('gambar')->getMimeType(),
            'is_valid' => $request->file('gambar')->isValid(),
        ] : null;
        
        Log::info('Update request details', [
            'method' => $request->method(),
            'content_type' => $request->header('Content-Type'),
            'has_file' => $request->hasFile('gambar'),
            'file_info' => $fileInfo,
            'input_data' => $request->except(['gambar'])
        ]);
    }
    
    /**
     * Hapus gambar lama jika ada
     */
    private function deleteOldGambar(?string $gambarName): void
    {
        if ($gambarName) {
            $this->deleteGambar($gambarName);
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
            
            // Hapus gambar terkait
            $this->deleteOldGambar($layananBisnis->gambar);
            
            // Hapus data
            $layananBisnis->delete();
            
            return response()->json([
                'message' => 'Data modul bisnis berhasil dihapus',
                'data' => $layananBisnis
            ], 200);
            
        } catch (\Exception $e) {
            Log::error('Destroy error: ' . $e->getMessage());
            
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
        // Cek autentikasi
        if (!Auth::check()) {
            return response()->json([
                'message' => 'Unauthorized. Silakan login terlebih dahulu.'
            ], 401);
        }
        
        // Cek authorization
        $user = Auth::user();
        $allowedRoles = ['admin', 'super_admin'];
        
        if (!in_array($user->role, $allowedRoles)) {
            return response()->json([
                'message' => 'Unauthorized. Hanya admin/super_admin yang dapat menghapus.'
            ], 403);
        }
        
        // Validasi input
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:trading,webinar,reseller,modal bisnis'
        ]);
        
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        
        $type = $request->type;
        
        // Ambil data yang akan dihapus
        $dataToDelete = LayananBisnis::where('type', $type)->get();
        $count = $dataToDelete->count();
        
        if ($count === 0) {
            return response()->json([
                'message' => "Tidak ada data dengan type '{$type}'"
            ], 404);
        }
        
        // Hapus gambar terlebih dahulu
        foreach ($dataToDelete as $item) {
            $this->deleteOldGambar($item->gambar);
        }
        
        // Hapus data dari database
        LayananBisnis::where('type', $type)->delete();
        
        return response()->json([
            'message' => "Berhasil menghapus {$count} data dengan type '{$type}'",
            'deleted_count' => $count,
            'type' => $type
        ], 200);
    }
    
    /**
     * Helper untuk memeriksa file yang valid
     */
    private function hasValidFile(Request $request, string $field): bool
    {
        return $request->hasFile($field) && $request->file($field)->isValid();
    }
}