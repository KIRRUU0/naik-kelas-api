<?php

namespace App\Http\Controllers;

use App\Models\Home;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * BASE PATH untuk upload file gambar home
     * Sesuai dengan struktur hosting (public_html/upload/home/)
     */
    private function getUploadBasePath()
    {
        // ✅ Untuk hosting environment (public_html/upload/home/)
        return base_path('../public_html/upload/home/');
    }

    /**
     * Display a listing of the resource.
     * GET /api/home
     */
    public function index()
    {
        try {
            $homeData = Home::all()->map(function ($item) {
                return $this->formatHomeData($item);
            });
            
            return response()->json([
                'status' => 'success',
                'message' => 'Data home berhasil diambil',
                'data' => $homeData
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
     * POST /api/home
     */
    public function store(Request $request)
    {
        // Cek autentikasi - hanya admin/super_admin
        if (!Auth::check() || !in_array(Auth::user()->role, ['admin', 'super_admin'])) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized. Hanya Admin yang dapat menambah data home.'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'tagline_brand' => 'required|string|max:255',
            'deskripsi_brand' => 'required|string',
            'gambar_brand' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048', // ✅ UPDATE: file validation
            'url_cta' => 'nullable|url'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // ✅ UPLOAD GAMBAR
            $gambarPath = null;
            
            if ($request->hasFile('gambar_brand')) {
                $gambarPath = $this->uploadGambar($request->file('gambar_brand'));
            }

            // ✅ BUAT DATA
            $data = $request->only([
                'tagline_brand', 
                'deskripsi_brand', 
                'url_cta'
            ]);
            $data['gambar_brand'] = $gambarPath; // Simpan nama file gambar

            $home = Home::create($data);

            return response()->json([
                'status' => 'success',
                'message' => 'Data home berhasil ditambahkan',
                'data' => $this->formatHomeData($home)
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
     * GET /api/home/{id}
     */
    public function show($id)
    {
        try {
            $home = Home::find($id);

            if (!$home) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data home tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Data home berhasil diambil',
                'data' => $this->formatHomeData($home)
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
     * PUT /api/home atau PUT /api/home/{id}
     */
    public function update(Request $request, $id = null)
    {
        // Cek autentikasi - hanya admin/super_admin
        if (!Auth::check() || !in_array(Auth::user()->role, ['admin', 'super_admin'])) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized. Hanya Admin yang dapat mengupdate data home.'
            ], 403);
        }

        // Jika tidak ada ID, update record pertama
        if (!$id) {
            $home = Home::first();
            if (!$home) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data home tidak ditemukan untuk diupdate'
                ], 404);
            }
            $id = $home->id;
        }

        $home = Home::find($id);
        if (!$home) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data home tidak ditemukan'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'tagline_brand' => 'sometimes|string|max:255',
            'deskripsi_brand' => 'sometimes|string',
            'gambar_brand' => 'sometimes|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'url_cta' => 'sometimes|url'
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

            // ✅ HANDLE GAMBAR UPLOAD
            if ($request->hasFile('gambar_brand') && $request->file('gambar_brand')->isValid()) {
                // Hapus gambar lama jika ada
                if ($home->gambar_brand) {
                    $this->deleteGambar($home->gambar_brand);
                }

                // Upload gambar baru
                $gambarPath = $this->uploadGambar($request->file('gambar_brand'));
                $data['gambar_brand'] = $gambarPath;
            }

            $home->update($data);

            return response()->json([
                'status' => 'success',
                'message' => 'Data home berhasil diupdate',
                'data' => $this->formatHomeData($home)
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
     * DELETE /api/home/{id}
     */
    public function destroy($id)
    {
        // Cek autentikasi - hanya admin/super_admin
        if (!Auth::check() || !in_array(Auth::user()->role, ['admin', 'super_admin'])) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized. Hanya Admin yang dapat menghapus data home.'
            ], 403);
        }

        try {
            $home = Home::find($id);

            if (!$home) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data home tidak ditemukan'
                ], 404);
            }

            // ✅ HAPUS GAMBAR jika ada
            if ($home->gambar_brand) {
                $this->deleteGambar($home->gambar_brand);
            }

            $home->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Data home berhasil dihapus'
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
     * UPLOAD GAMBAR - untuk Home
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
     * DELETE GAMBAR - untuk Home
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

    /**
     * Format home data dengan field terpisah untuk nama file dan URL lengkap
     */
    private function formatHomeData($home)
    {
        $baseUrl = rtrim(env('APP_URL', 'http://localhost'), '/');
        $gambarUrl = $home->gambar_brand ? $baseUrl . '/upload/home/' . $home->gambar_brand : null;

        return [
            'id' => $home->id,
            'tagline_brand' => $home->tagline_brand,
            'deskripsi_brand' => $home->deskripsi_brand,
            'url_cta' => $home->url_cta,
            'gambar_brand' => $home->gambar_brand, // nama file saja
            'gambar' => $gambarUrl // full URL yang bisa diakses
        ];
    }
}