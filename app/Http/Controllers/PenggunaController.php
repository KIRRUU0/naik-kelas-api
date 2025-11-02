<?php

namespace App\Http\Controllers;

use App\Models\Pengguna;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash; 
use Illuminate\Support\Facades\Auth;

class PenggunaController extends Controller
{
    /**
     * BASE PATH untuk upload file
     * Sesuai dengan struktur hosting (public_html/upload/foto-profil/)
     */
    private function getUploadBasePath()
    {
        // ✅ Untuk hosting environment (public_html/upload/foto-profil/)
        return base_path('../public_html/upload/foto-profil/');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Hanya super_admin yang bisa akses
        if (!Auth::check() || Auth::user()->role !== 'super_admin') {
            return response()->json([
                'message' => 'Unauthorized. Hanya Super Admin yang dapat mengakses.'
            ], 403);
        }
    
        $query = Pengguna::query();
        
        // FILTER BY ROLE (langsung ke column role di database)
        if ($request->has('role') && !empty($request->role)) {
            $roles = explode(',', $request->role);
            $query->whereIn('role', $roles);
        }
        
        $pengguna = $query->get();
        
        // ✅ OTOMATIS menggunakan accessor foto_profil_url dari Model
        return response()->json([
            "message" => "Data pengguna berhasil diambil",
            "data" => $pengguna
        ], 200);
    }
    
    /**
     * METHOD KHUSUS: Create Super Admin tanpa authentication
     * Untuk keperluan initial setup/seeder
     */
    public function createSuperAdmin(Request $request)
    {
        // ✅ VALIDATION RULES
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:pengguna,email',
            'password' => 'required|min:6',
            'foto_profil' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Handle file upload
            $fotoProfilPath = null;
            
            if ($request->hasFile('foto_profil')) {
                $fotoProfilPath = $this->uploadFotoProfil($request->file('foto_profil'));
            }

            // Create SUPER ADMIN
            $pengguna = Pengguna::create([
                'nama' => $request->nama,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'foto_profil' => $fotoProfilPath,
                'role' => 'super_admin' // ✅ ROLE SUPER_ADMIN
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Super Admin berhasil dibuat!',
                'data' => $pengguna
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
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Cek apakah user yang login adalah Super Admin
        $currentUser = Auth::user();
        if (!$currentUser || $currentUser->role !== 'super_admin') {
            return response()->json([
                'status' => 'error',
                'message' => 'Hanya Super Admin yang dapat menambahkan admin'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:pengguna,email',
            'password' => 'required|min:6',
            'foto_profil' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'role' => 'required|in:super_admin,admin'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Handle file upload
            $fotoProfilPath = null;
            
            if ($request->hasFile('foto_profil')) {
                $fotoProfilPath = $this->uploadFotoProfil($request->file('foto_profil'));
            }

            // Create user
            $pengguna = Pengguna::create([
                'nama' => $request->nama,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'foto_profil' => $fotoProfilPath,
                'role' => $request->role
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'User berhasil ditambahkan dengan role: ' . $request->role,
                'data' => $pengguna // ✅ Langsung return model, accessor otomatis bekerja
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
     * UPLOAD FILE - FIXED VERSION untuk Hosting Environment
     */
    private function uploadFotoProfil($file)
    {
        try {
            $uploadDir = $this->getUploadBasePath();
            
            // ✅ Pastikan folder upload ada
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
                error_log("✅ Created upload directory: " . $uploadDir);
            }
            
            // ✅ Generate unique filename
            $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $fullPath = $uploadDir . $fileName;
            
            // Debug info
            error_log("📁 Upload Directory: " . $uploadDir);
            error_log("📄 File Name: " . $fileName);
            error_log("🎯 Full Path: " . $fullPath);
            
            // ✅ Upload file
            if ($file->move($uploadDir, $fileName)) {
                // ✅ Set permission agar file bisa diakses via web
                chmod($fullPath, 0644);
                
                error_log("✅ File uploaded successfully: " . $fileName);
                error_log("🌐 Accessible URL: " . url('upload/foto-profil/' . $fileName));
                
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
     * DELETE FILE - FIXED VERSION untuk Hosting Environment
     */
    private function deleteFotoProfil($fileName)
    {
        if (!$fileName) {
            return false;
        }

        try {
            $filePath = $this->getUploadBasePath() . $fileName;
            
            error_log("🗑️ Attempting to delete: " . $filePath);
            
            if (file_exists($filePath)) {
                if (unlink($filePath)) {
                    error_log("✅ File deleted successfully: " . $filePath);
                    return true;
                } else {
                    error_log("❌ Failed to delete file: " . $filePath);
                    return false;
                }
            } else {
                error_log("⚠️ File not found: " . $filePath);
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
    public function show(Pengguna $pengguna)
    {
        // ✅ DEBUG: Log informasi file
        error_log("🔍 DEBUG FOTO PROFIL:");
        error_log("📸 Database filename: " . $pengguna->foto_profil);
        error_log("🌐 Generated URL: " . $pengguna->foto_profil_url);
        
        if ($pengguna->foto_profil) {
            $filePath = $this->getUploadBasePath() . $pengguna->foto_profil;
            error_log("📁 Physical path: " . $filePath);
            error_log("✅ File exists: " . (file_exists($filePath) ? 'YES' : 'NO'));
        }
        
        return response()->json([
            "message" => "Data pengguna berhasil diambil",
            "data" => $pengguna
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        
        $pengguna = Pengguna::find($id);
    
    if (!$pengguna) {
        return response()->json([
            'status' => 'error',
            'message' => 'Data pengguna tidak ditemukan'
        ], 404);
    }
    
        // Cek autentikasi
        if (!Auth::check()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized. Silakan login terlebih dahulu.'
            ], 401);
        }

        $currentUser = Auth::user();
        
        // Authorization check
        if ($currentUser->role === 'admin' && $pengguna->role === 'super_admin') {
            return response()->json([
                'status' => 'error',
                'message' => 'Admin tidak dapat mengupdate Super Admin'
            ], 403);
        }

        if ($currentUser->role !== 'super_admin' && $currentUser->id !== $pengguna->id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Anda hanya dapat mengupdate profil sendiri'
            ], 403);
        }

        // Validation rules
        $validationRules = [
            'nama' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:pengguna,email,'.$pengguna->id,
            'password' => 'sometimes|min:6',
            'foto_profil' => 'sometimes|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ];

        // Hanya super_admin yang bisa mengubah role
        if ($currentUser->role === 'super_admin' && $currentUser->id !== $pengguna->id) {
            $validationRules['role'] = 'sometimes|in:super_admin,admin';
        }

        $validator = Validator::make($request->all(), $validationRules);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $data = $request->all();

            // Handle password update
            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            } else {
                unset($data['password']);
            }

            // Handle file upload
            if ($request->hasFile('foto_profil')) {
                // Hapus foto lama jika ada
                if ($pengguna->foto_profil) {
                    $this->deleteFotoProfil($pengguna->foto_profil);
                }

                // Simpan foto baru
                $data['foto_profil'] = $this->uploadFotoProfil($request->file('foto_profil'));
            }

            // Jika bukan super_admin, jangan izinkan ubah role
            if ($currentUser->role !== 'super_admin' && isset($data['role'])) {
                unset($data['role']);
            }

            $pengguna->update($data);

            return response()->json([
                "status" => "success",
                "message" => "Data pengguna berhasil diupdate",
                "data" => $pengguna
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
     */
    public function destroy(Pengguna $pengguna)
    {
        // Cek autentikasi
        if (!Auth::check()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized. Silakan login terlebih dahulu.'
            ], 401);
        }

        $currentUser = Auth::user();
        
        // Hanya super_admin yang bisa hapus
        if ($currentUser->role !== 'super_admin') {
            return response()->json([
                'status' => 'error',
                'message' => 'Hanya Super Admin yang dapat menghapus pengguna'
            ], 403);
        }

        // Cegah penghapusan diri sendiri
        if ($currentUser->id === $pengguna->id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tidak dapat menghapus akun sendiri'
            ], 400);
        }

        try {
            // Hapus foto profil jika ada
            if ($pengguna->foto_profil) {
                $this->deleteFotoProfil($pengguna->foto_profil);
            }

            $pengguna->delete();

            return response()->json([
                "status" => "success",
                "message" => "Data pengguna berhasil dihapus"
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
     * Get profile user yang sedang login
     */
    public function profile()
    {
        if (!Auth::check()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized. Silakan login terlebih dahulu.'
            ], 401);
        }

        $user = Auth::user();
        
        return response()->json([
            'status' => 'success',
            'data' => $user
        ]);
    }

    /**
     * Method khusus untuk super_admin membuat admin baru
     */
    public function storeAdmin(Request $request)
    {
        if (!Auth::check() || Auth::user()->role !== 'super_admin') {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized. Hanya Super Admin yang dapat menambahkan admin.'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:pengguna,email',
            'password' => 'required|min:6',
            'foto_profil' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Handle file upload
            $fotoProfilPath = null;
            
            if ($request->hasFile('foto_profil')) {
                $fotoProfilPath = $this->uploadFotoProfil($request->file('foto_profil'));
                
                // ✅ EXTRA DEBUG: Pastikan file benar-benar ada
                $debugPath = $this->getUploadBasePath() . $fotoProfilPath;
                error_log("🔍 POST-UPLOAD VERIFICATION:");
                error_log("📁 Path: " . $debugPath);
                error_log("✅ Exists: " . (file_exists($debugPath) ? 'YES' : 'NO'));
                error_log("🔐 Permissions: " . substr(sprintf('%o', fileperms($debugPath)), -4));
            }

            // Create admin (default role admin)
            $pengguna = Pengguna::create([
                'nama' => $request->nama,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'foto_profil' => $fotoProfilPath,
                'role' => 'admin'
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Admin berhasil ditambahkan',
                'data' => $pengguna
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan server',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}