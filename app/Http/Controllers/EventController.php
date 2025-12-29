<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class EventController extends Controller
{
    // --- Helper Methods (File Handling) ---
    private function getUploadBasePath()
    {
        // Target: public_html/upload/event/
        $path = base_path('../public_html/upload/event/');
        
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
            $fileName = 'event-' . time() . '_' . uniqid() . '.' . $extension;
            $fullPath = $uploadDir . $fileName;
            
            if ($file->move($uploadDir, $fileName)) {
                chmod($fullPath, 0644);
                return $fileName; // Hanya simpan nama file di DB
            } else {
                throw new \Exception('Gagal memindahkan file');
            }
        } catch (\Exception $e) {
            Log::error("❌ Upload error: " . $e->getMessage());
            throw new \Exception('Upload gagal: ' . $e->getMessage());
        }
    }

    private function deleteGambar($fileName)
    {
        if (!$fileName) return false;

        try {
            $filePath = $this->getUploadBasePath() . $fileName;
            
            if (file_exists($filePath) && unlink($filePath)) {
                Log::info("✅ Event poster deleted successfully: " . $filePath);
                return true;
            }
            return false;
        } catch (\Exception $e) {
            Log::error("❌ Delete error: " . $e->getMessage());
            return false;
        }
    }

    // --- Utility Methods ---
    
    /**
     * Konversi format tanggal dari DD-MM-YYYY ke YYYY-MM-DD
     */
    private function parseTanggal($tanggalInput)
    {
        try {
            // Coba format DD-MM-YYYY
            if (preg_match('/^\d{2}-\d{2}-\d{4}$/', $tanggalInput)) {
                return Carbon::createFromFormat('d-m-Y', $tanggalInput)->format('Y-m-d');
            }
            // Coba format YYYY-MM-DD
            elseif (preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggalInput)) {
                return $tanggalInput; // Sudah benar
            }
            // Coba parse dengan Carbon
            else {
                return Carbon::parse($tanggalInput)->format('Y-m-d');
            }
        } catch (\Exception $e) {
            Log::error("❌ Error parsing tanggal: " . $tanggalInput . " - " . $e->getMessage());
            throw new \Exception('Format tanggal tidak valid. Gunakan format DD-MM-YYYY');
        }
    }

    /**
     * Cek kolom yang ada di tabel events
     */
    private function getDBAttributes($request)
    {
        $attributes = [];
        
        // Debug: Log semua input
        Log::info('📥 Request data:', $request->all());
        
        // Mapping field berdasarkan struktur tabel yang sebenarnya
        // Cek di database: DESCRIBE events;
        // Adjust sesuai struktur tabel Anda
        
        // Kemungkinan struktur berdasarkan error sebelumnya:
        // 1. judul - OK
        // 2. deskripsi (di DB mungkin 'desc' atau 'deskripsi')
        // 3. tanggal_mulai (di DB mungkin 'tanggal' atau 'tanggal_mulai')
        // 4. waktu_mulai - OK
        // 5. gambar_poster - OK
        // 6. status - OK
        
        if ($request->has('judul')) {
            $attributes['judul'] = $request->judul;
        }
        
        if ($request->has('deskripsi')) {
            // Cek dulu nama kolom di database
            $attributes['deskripsi'] = $request->deskripsi;
            // Jika di DB namanya 'desc', uncomment baris ini:
            // $attributes['desc'] = $request->deskripsi;
        }
        
        if ($request->has('tanggal_mulai') && $request->tanggal_mulai) {
            try {
                $tanggalFormatted = $this->parseTanggal($request->tanggal_mulai);
                $attributes['tanggal_mulai'] = $tanggalFormatted;
                // Jika di DB namanya 'tanggal', uncomment baris ini:
                // $attributes['tanggal'] = $tanggalFormatted;
            } catch (\Exception $e) {
                throw $e;
            }
        }
        
        if ($request->has('waktu_mulai') && !empty($request->waktu_mulai)) {
            $attributes['waktu_mulai'] = $request->waktu_mulai;
        }
        
        if ($request->has('status')) {
            $attributes['status'] = $request->status;
        }
        
        // Gambar dihandle terpisah
        return $attributes;
    }

    // --- CRUD Methods ---

    /**
     * GET /api/event (Publik)
     */
    public function index(Request $request)
    {
        $query = Event::query();
        
        if ($request->has('status') && in_array($request->status, ['buka', 'tutup'])) {
            $query->where('status', $request->status);
        }
        
        // Pastikan kolom 'tanggal' ada di tabel
        try {
            $query->orderBy('tanggal_mulai', 'desc'); 
            // Jika di DB namanya 'tanggal', ganti menjadi:
            // $query->orderBy('tanggal', 'desc');
        } catch (\Exception $e) {
            // Fallback ke created_at jika error
            $query->orderBy('created_at', 'desc');
        }
        
        $events = $query->get();
        
        // Format tanggal untuk response
        $events->transform(function ($event) {
            if ($event->tanggal_mulai) {
                try {
                    $event->tanggal_mulai_formatted = Carbon::parse($event->tanggal_mulai)->format('d-m-Y');
                } catch (\Exception $e) {
                    $event->tanggal_mulai_formatted = $event->tanggal_mulai;
                }
            }
            return $event;
        });
        
        return response()->json([
            "message" => "Data event berhasil diambil",
            "data" => $events
        ], 200);
    }

    /**
     * POST /api/event (Admin/Super Admin)
     */
    public function store(Request $request)
    {
        // Debug request
        Log::info('🔍 STORE REQUEST:', $request->all());
        
        $validator = Validator::make($request->all(), [
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required', 
            'tanggal_mulai' => 'required|sometimes|nullable |date_format:d-m-Y',
            'waktu_mulai' => 'sometimes|nullable|date_format:H:i',
            'gambar_poster' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
            'status' => 'required|in:buka,tutup',
        ]);
        
        if ($validator->fails()) {
            Log::error('❌ Validation Failed:', $validator->errors()->toArray());
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();
            
            // 1. Upload gambar
            $gambarFileName = $this->uploadGambar($request->file('gambar_poster'));
            
            // 2. Siapkan data untuk database
            $data = $this->getDBAttributes($request);
            
            // 3. Tambahkan gambar ke data
            $data['gambar_poster'] = $gambarFileName;
            
            // 4. Debug data sebelum insert
            Log::info('📝 Data untuk insert:', $data);
            
            // 5. Simpan ke database
            $event = Event::create($data);
            
            DB::commit();
            
            Log::info('✅ Event berhasil dibuat dengan ID: ' . $event->id);
            
            return response()->json([
                "status" => "success",
                "message" => "Event berhasil ditambahkan", 
                "data" => $event
            ], 201);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('❌ STORE ERROR Event: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);
            
            return response()->json([
                'status' => 'error', 
                'message' => 'Terjadi kesalahan server saat menyimpan data',
                'error_detail' => $e->getMessage(),
                'debug_info' => 'Pastikan format tanggal DD-MM-YYYY'
            ], 500);
        }
    }

    /**
     * GET /api/event/{id} (Publik)
     */
    public function show($id)
    {
        $event = Event::find($id);
        
        if (!$event) {
            return response()->json([
                "status" => "error",
                "message" => "Event tidak ditemukan", 
                "data" => null
            ], 404);
        }
        
        // Format tanggal untuk response
        if ($event->tanggal_mulai) {
            try {
                $event->tanggal_mulai_formatted = Carbon::parse($event->tanggal_mulai)->format('d-m-Y');
            } catch (\Exception $e) {
                $event->tanggal_mulai_formatted = $event->tanggal_mulai;
            }
        }
        
        return response()->json([
            "status" => "success",
            "message" => "Detail event berhasil dimuat", 
            "data" => $event
        ], 200);
    }

    /**
     * POST /api/event/{id} (Admin/Super Admin)
     */
    public function update(Request $request, $id)
    {
        $event = Event::find($id);
        
        if (!$event) {
            return response()->json([
                'status' => 'error',
                'message' => 'Event tidak ditemukan'
            ], 404);
        }

        // Debug
        Log::info('🔍 UPDATE REQUEST untuk ID ' . $id . ':', $request->all());
        
        $validator = Validator::make($request->all(), [
            'judul' => 'sometimes|string|max:255',
            'deskripsi' => 'sometimes|string',
            'tanggal_mulai' => 'sometimes|nullable|date_format:d-m-Y',
            'waktu_mulai' => 'sometimes|nullable|date_format:H:i',
            'gambar_poster' => 'sometimes|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
            'status' => 'sometimes|in:buka,tutup',
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
            
            // 1. Siapkan data untuk update
            $data = $this->getDBAttributes($request);
            
            // 2. Handle gambar jika ada
            if ($request->hasFile('gambar_poster') && $request->file('gambar_poster')->isValid()) {
                // Hapus gambar lama jika ada
                if ($event->gambar_poster) {
                    $this->deleteGambar($event->gambar_poster);
                }
                
                // Upload gambar baru
                $gambarFileName = $this->uploadGambar($request->file('gambar_poster'));
                $data['gambar_poster'] = $gambarFileName;
            }
            
            // 3. Debug data sebelum update
            Log::info('📝 Data untuk update:', $data);
            
            // 4. Update data
            if (!empty($data)) {
                $event->update($data);
            }
            
            DB::commit();
            
            // 5. Ambil data terbaru
            $updatedEvent = Event::find($id);
            
            // Format tanggal untuk response
            if ($updatedEvent->tanggal_mulai) {
                try {
                    $updatedEvent->tanggal_mulai_formatted = Carbon::parse($updatedEvent->tanggal_mulai)->format('d-m-Y');
                } catch (\Exception $e) {
                    $updatedEvent->tanggal_mulai_formatted = $updatedEvent->tanggal_mulai;
                }
            }
            
            return response()->json([
                "status" => "success",
                "message" => "Event berhasil diupdate", 
                "data" => $updatedEvent
            ], 200);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('❌ UPDATE ERROR Event ID ' . $id . ': ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan server saat mengupdate data',
                'error_detail' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * DELETE /api/event/{id} (Admin/Super Admin)
     */
    public function destroy($id)
    {
        try {
            $event = Event::find($id);
            
            if (!$event) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Event tidak ditemukan'
                ], 404);
            }

            // Hapus gambar jika ada
            if ($event->gambar_poster) {
                $this->deleteGambar($event->gambar_poster);
            }
            
            // Hapus data
            $event->delete();
            
            return response()->json([
                "status" => "success",
                "message" => "Event berhasil dihapus"
            ], 200);
            
        } catch (\Exception $e) {
            Log::error('❌ DELETE ERROR Event ID ' . $id . ': ' . $e->getMessage());
            
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan server saat menghapus data'
            ], 500);
        }
    }
}