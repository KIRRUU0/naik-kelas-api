<?php

namespace App\Http\Controllers;

use App\Models\Artikel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ArtikelController extends Controller
{
    // --- Helper Methods (File Handling) ---
    private function getUploadBasePath()
    {
        $path = public_path('upload/artikel/');
        
        if (!is_dir($path)) {
            if (!mkdir($path, 0755, true) && !is_dir($path)) {
                 throw new \RuntimeException(sprintf('Directory "%s" was not created', $path));
            }
        }
        return $path;
    }

    private function uploadGambar($file)
    {
        try {
            $uploadDir = $this->getUploadBasePath();
            $extension = $file->getClientOriginalExtension();
            $fileName = 'article-' . time() . '_' . uniqid() . '.' . $extension;
            $fullPath = $uploadDir . $fileName;
            
            if ($file->move($uploadDir, $fileName)) {
                chmod($fullPath, 0644);
                return $fileName;
            } else {
                throw new \Exception('Gagal memindahkan file');
            }
        } catch (\Exception $e) {
            Log::error("Upload error: " . $e->getMessage());
            throw new \Exception('Upload gagal: ' . $e->getMessage());
        }
    }

    private function deleteGambar($fileName)
    {
        if (!$fileName) return false;

        try {
            $filePath = $this->getUploadBasePath() . $fileName;
            
            if (file_exists($filePath) && unlink($filePath)) {
                Log::info("Article image deleted: " . $filePath);
                return true;
            }
            return false;
        } catch (\Exception $e) {
            Log::error("Delete error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Format tanggal dari DD-MM-YYYY ke YYYY-MM-DD untuk database
     */
    private function formatTanggalUntukDatabase($tanggalInput)
    {
        try {
            // Format DD-MM-YYYY
            if (preg_match('/^\d{2}-\d{2}-\d{4}$/', $tanggalInput)) {
                return Carbon::createFromFormat('d-m-Y', $tanggalInput)->format('Y-m-d');
            }
            // Format YYYY-MM-DD (sudah benar)
            elseif (preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggalInput)) {
                return $tanggalInput;
            }
            throw new \Exception('Format tanggal tidak valid');
        } catch (\Exception $e) {
            Log::error("Error parsing tanggal: " . $tanggalInput . " - " . $e->getMessage());
            throw new \Exception('Format tanggal tidak valid. Gunakan format DD-MM-YYYY');
        }
    }

    // --- CRUD Methods ---

    /**
     * GET /api/article (Publik)
     */
    public function index(Request $request)
    {
        $query = Artikel::query();

        // Filter berdasarkan slug jika ada
        if ($request->has('slug')) {
            $query->where('slug', $request->slug);
        }
        
        // Filter berdasarkan nama pengirim jika ada
        if ($request->has('nama_pengirim')) {
            $query->where('nama_pengirim', $request->nama_pengirim);
        }
        
        // Filter artikel yang sudah terbit (tanggal_terbit <= hari ini)
        if ($request->has('filter') && $request->filter === 'published') {
            $query->where('tanggal_terbit', '<=', now()->format('Y-m-d'))
                  ->whereNotNull('tanggal_terbit');
        }
        
        // Urutkan
        $query->orderBy('tanggal_terbit', 'desc')
              ->orderBy('id', 'desc');
        
        $articles = $query->get();
        
        // Transformasi data: tambahkan gambar_url secara manual
        // TANPA tanggal_terbit_formatted dan status
        $articles->transform(function ($article) {
            // Hanya tambahkan gambar_url jika diperlukan
            $articleData = $article->toArray();
            $articleData['gambar_url'] = $article->gambar ? url('upload/artikel/' . $article->gambar) : null;
            
            // HAPUS field yang tidak diinginkan dari response
            unset(
                $articleData['tanggal_terbit_formatted'], // Jika ada
                $articleData['status'] // Jika ada
            );
            
            return $articleData;
        });
        
        return response()->json([
            "status" => "success",
            "message" => "Data artikel berhasil diambil",
            "data" => $articles
        ], 200);
    }

    /**
     * POST /api/article (Admin)
     */
    public function store(Request $request)
    {
        Log::info('STORE ARTICLE REQUEST:', $request->all());
        
        $validator = Validator::make($request->all(), [
            'judul' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:artikel,slug',
            'deskripsi' => 'required|string',
            'tanggal_terbit' => 'required|date_format:d-m-Y',
            'url_cta' => 'nullable|url|max:500',
            'gambar' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
        ]);
        
        if ($validator->fails()) {
            Log::error('Validation Failed:', $validator->errors()->toArray());
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();
            
            // Upload gambar
            $gambarFileName = $this->uploadGambar($request->file('gambar'));
            
            // Format tanggal untuk database
            $tanggalFormatted = $this->formatTanggalUntukDatabase($request->tanggal_terbit);
            
            // Data untuk database
            $data = [
                'judul' => $request->judul,
                'deskripsi' => $request->deskripsi,
                'tanggal_terbit' => $tanggalFormatted,
                'url_cta' => $request->url_cta,
                'gambar' => $gambarFileName,
            ];

            if ($request->has('slug') && !empty($request->slug)) {
                $data['slug'] = Str::slug($request->slug, '-', 'id');
            }
            
            // Simpan ke database
            $article = Artikel::create($data);
            
            DB::commit();
            
            Log::info('✅ Artikel berhasil dibuat dengan ID: ' . $article->id);
            Log::info('📝 Slug: ' . $article->slug);
            
            // Siapkan response data
            $responseData = $article->toArray();
            $responseData['gambar_url'] = url('upload/artikel/' . $article->gambar);
            
            return response()->json([
                "status" => "success",
                "message" => "Artikel berhasil ditambahkan", 
                "data" => $responseData
            ], 201);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('❌ STORE ERROR Artikel: ' . $e->getMessage());
            
            return response()->json([
                'status' => 'error', 
                'message' => 'Terjadi kesalahan server saat menyimpan data',
                'error_detail' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * GET /api/article/{id} (Publik) - by ID
     * GET /api/article/slug/{slug} (Publik) - by slug
     */
    public function show($identifier)
    {
        // Cek jika identifier adalah slug (bukan numeric)
        if (!is_numeric($identifier)) {
            $article = Artikel::where('slug', $identifier)->first();
        } else {
            $article = Artikel::find($identifier);
        }
        
        if (!$article) {
            return response()->json([
                "status" => "error",
                "message" => "Artikel tidak ditemukan", 
                "data" => null
            ], 404);
        }
        
        // Siapkan response data
        $responseData = $article->toArray();
        $responseData['gambar_url'] = $article->gambar ? url('upload/artikel/' . $article->gambar) : null;
        
        return response()->json([
            "status" => "success",
            "message" => "Detail artikel berhasil dimuat", 
            "data" => $responseData
        ], 200);
    }

    /**
     * POST /api/article/{id} (Admin)
     */
    public function update(Request $request, $id)
    {
        $article = Artikel::find($id);
        
        if (!$article) {
            return response()->json([
                'status' => 'error',
                'message' => 'Artikel tidak ditemukan'
            ], 404);
        }

        Log::info('UPDATE REQUEST artikel ID ' . $id . ':', $request->all());
        
        $validator = Validator::make($request->all(), [
            'judul' => 'sometimes|string|max:255',
            'slug' => 'sometimes|string|max:255|unique:artikel,slug,' . $id, // Unique kecuali untuk dirinya sendiri
            'deskripsi' => 'sometimes|string',
            'tanggal_terbit' => 'sometimes|date_format:d-m-Y',
            'url_cta' => 'nullable|url|max:500',
            'gambar' => 'sometimes|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();
            
            $data = [];
            
            // Update field yang ada di request
            if ($request->has('judul')) {
                $data['judul'] = $request->judul;
            }
            
            if ($request->has('slug') && !empty($request->slug)) {
                $data['slug'] = Str::slug($request->slug, '-', 'id');
            }
            
            if ($request->has('deskripsi')) {
                $data['deskripsi'] = $request->deskripsi;
            }
            
            if ($request->has('tanggal_terbit') && !empty($request->tanggal_terbit)) {
                $data['tanggal_terbit'] = $this->formatTanggalUntukDatabase($request->tanggal_terbit);
            }
            
            if ($request->has('url_cta')) {
                $data['url_cta'] = $request->url_cta;
            }
            
            // Handle gambar jika ada
            if ($request->hasFile('gambar') && $request->file('gambar')->isValid()) {
                if ($article->gambar) {
                    $this->deleteGambar($article->gambar);
                }
                
                $gambarFileName = $this->uploadGambar($request->file('gambar'));
                $data['gambar'] = $gambarFileName;
            }
            
            // Update data jika ada perubahan
            if (!empty($data)) {
                $article->update($data);
            }
            
            DB::commit();
            
            // Ambil data terbaru
            $updatedArticle = Artikel::find($id);
            
            // Siapkan response data
            $responseData = $updatedArticle->toArray();
            $responseData['gambar_url'] = $updatedArticle->gambar ? url('upload/artikel/' . $updatedArticle->gambar) : null;
            
            return response()->json([
                "status" => "success",
                "message" => "Artikel berhasil diupdate", 
                "data" => $responseData
            ], 200);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('UPDATE ERROR Artikel ID ' . $id . ': ' . $e->getMessage());
            
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan server saat mengupdate data',
                'error_detail' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * DELETE /api/article/{id} (Admin)
     */
    public function destroy($id)
    {
        try {
            $article = Artikel::find($id);
            
            if (!$article) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Artikel tidak ditemukan'
                ], 404);
            }

            // Hapus gambar
            if ($article->gambar) {
                $this->deleteGambar($article->gambar);
            }
            
            // Hapus data
            $article->delete();
            
            return response()->json([
                "status" => "success",
                "message" => "Artikel berhasil dihapus"
            ], 200);
            
        } catch (\Exception $e) {
            Log::error('DELETE ERROR Artikel ID ' . $id . ': ' . $e->getMessage());
            
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan server saat menghapus data'
            ], 500);
        }
    }
}