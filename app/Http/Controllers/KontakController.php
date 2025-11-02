<?php

namespace App\Http\Controllers;

use App\Models\Kontak;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class KontakController extends Controller
{
    /**
     * ✅ PUBLIC: Submit form kontak TANPA LOGIN
     * POST /api/v1/kontak
     */
    public function store(Request $request)
    {
        \Log::info('Kontak Store Request:', $request->all());
        
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'nomor_telepon' => 'nullable|string|max:20',
            'pesan' => 'required|string|max:2000',
        ]);

        if ($validator->fails()) {
            \Log::error('Validation Failed:', $validator->errors()->toArray());
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            \Log::info('Attempting to create Kontak...');
            
            $kontak = Kontak::create([
                'nama' => $request->nama,
                'email' => $request->email,
                'nomor_telepon' => $request->nomor_telepon,
                'pesan' => $request->pesan
            ]);

            \Log::info('Kontak Created Successfully:', $kontak->toArray());

            return response()->json([
                'status' => 'success',
                'message' => 'Pesan Anda berhasil dikirim. Kami akan menghubungi Anda segera.',
                'data' => $kontak
            ], 201);

        } catch (\Exception $e) {
            \Log::error('Kontak Store Error Details:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan server. Silakan coba lagi.'
            ], 500);
        }
    }

    /**
     * ✅ PROTECTED: Admin lihat semua pesan
     * GET /api/v1/kontak
     */
    public function index(Request $request)
{
    if (!Auth::check() || !in_array(Auth::user()->role, ['admin', 'super_admin'])) {
        return response()->json([
            'status' => 'error',
            'message' => 'Unauthorized. Hanya admin yang dapat mengakses.'
        ], 403);
    }

    try {
        $query = Kontak::query();

        // Filter berdasarkan status baca
        if ($request->has('status') && $request->status !== '') {
            if ($request->status === 'unread') {
                $query->unread();
            } elseif ($request->status === 'read') {
                $query->read();
            }
        }

        // ✅ UBAH: Default sorting dari 'created_at' ke 'id'
        $sortBy = $request->get('sort_by', 'id'); // GANTI 'created_at' -> 'id'
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $pesan = $query->get();

        return response()->json([
        'status' => 'success',
        'message' => 'Data pesan kontak berhasil diambil',
        'data' => $pesan,
        'meta' => [
            'total' => $pesan->count(),
            'unread_count' => Kontak::unread()->count(),
            'read_count' => Kontak::read()->count()
        ]
    ], 200);

    } catch (\Exception $e) {
        \Log::error('Kontak Index Error: ' . $e->getMessage());
        
        return response()->json([
            'status' => 'error',
            'message' => 'Terjadi kesalahan server'
        ], 500);
    }
}

    /**
     * ✅ PROTECTED: Admin lihat detail pesan
     * GET /api/v1/kontak/{id}
     */
    public function show($id)
    {
        // KEMBALIKAN KE KODE SEMULA - Auth::check()
        if (!Auth::check() || !in_array(Auth::user()->role, ['admin', 'super_admin'])) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized. Hanya admin yang dapat mengakses.'
            ], 403);
        }

        try {
            $kontak = Kontak::find($id);

            if (!$kontak) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Pesan tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Detail pesan berhasil diambil',
                'data' => $kontak
            ], 200);

        } catch (\Exception $e) {
            \Log::error('Kontak Show Error: ' . $e->getMessage());
            
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan server'
            ], 500);
        }
    }

    /**
     * ✅ PROTECTED: Admin tandai pesan sudah dibaca
     * PUT /api/v1/kontak/{id}/baca
     */
    public function markAsRead($id)
    {
        // KEMBALIKAN KE KODE SEMULA - Auth::check()
        if (!Auth::check() || !in_array(Auth::user()->role, ['admin', 'super_admin'])) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized. Hanya admin yang dapat mengakses.'
            ], 403);
        }

        try {
            $kontak = Kontak::find($id);
            
            if (!$kontak) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Pesan tidak ditemukan'
                ], 404);
            }

            if (!$kontak->dibaca) {
                $kontak->update(['dibaca' => now()]);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Pesan ditandai sudah dibaca',
                'data' => $kontak
            ], 200);

        } catch (\Exception $e) {
            \Log::error('Kontak MarkAsRead Error: ' . $e->getMessage());
            
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan server'
            ], 500);
        }
    }

    /**
     * ✅ PROTECTED: Hapus pesan
     * DELETE /api/v1/kontak/{id}
     */
    public function destroy($id)
    {
        // KEMBALIKAN KE KODE SEMULA - Auth::check()
        if (!Auth::check() || !in_array(Auth::user()->role, ['admin', 'super_admin'])) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized. Hanya admin yang dapat mengakses.'
            ], 403);
        }

        try {
            $kontak = Kontak::find($id);
            
            if (!$kontak) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Pesan tidak ditemukan'
                ], 404);
            }

            $kontak->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Pesan berhasil dihapus'
            ], 200);

        } catch (\Exception $e) {
            \Log::error('Kontak Destroy Error: ' . $e->getMessage());
            
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan server'
            ], 500);
        }
    }

    /**
     * ✅ PROTECTED: Tandai semua pesan sebagai sudah dibaca
     * PUT /api/v1/kontak/mark-all-read
     */
       public function markAllAsRead()
    {
        // Cek auth dan role admin
        if (!Auth::check() || !in_array(Auth::user()->role, ['admin', 'super_admin'])) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized. Hanya admin yang dapat mengakses.'
            ], 403);
        }
    
        try {
            // Ambil pesan yang belum dibaca sebelum diupdate
            $unreadMessages = Kontak::unread()->get();
            
            if ($unreadMessages->count() === 0) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Tidak ada pesan yang belum dibaca',
                    'data' => [
                        'updated_count' => 0,
                        'read_messages' => []
                    ]
                ], 200);
            }
    
            // Update semua pesan belum dibaca
            $updated = Kontak::unread()->update(['dibaca' => now()]);
    
            // Ambil pesan yang baru saja diupdate
            $updatedMessages = Kontak::whereIn('id', $unreadMessages->pluck('id'))
                ->get(['id', 'nama', 'email', 'nomor_telepon', 'pesan', 'dibaca']);
    
            return response()->json([
                'status' => 'success',
                'message' => "{$updated} pesan berhasil ditandai sebagai sudah dibaca",
                'data' => [
                    'updated_count' => $updated,
                    'read_messages' => $updatedMessages->map(function ($message) {
                        // Buat preview pesan manual tanpa Str::limit
                        $pesanPreview = strlen($message->pesan) > 100 
                            ? substr($message->pesan, 0, 100) . '...' 
                            : $message->pesan;
                        
                        return [
                            'id' => $message->id,
                            'nama' => $message->nama,
                            'email' => $message->email,
                            'nomor_telepon' => $message->nomor_telepon,
                            'pesan_preview' => $pesanPreview,
                            'pesan_lengkap' => $message->pesan,
                            'dibaca' => $message->dibaca ? $message->dibaca->format('d M Y H:i') : null,
                            'status' => 'sudah_dibaca'
                        ];
                    })
                ]
            ], 200);
    
        } catch (\Exception $e) {
            \Log::error('Kontak MarkAllAsRead Error: ' . $e->getMessage());
            
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan server: ' . $e->getMessage()
            ], 500);
        }
    }
        /**
     * ✅ PROTECTED: Menampilkan semua pesan yang belum dibaca
     * GET /api/v1/kontak/unread
     */
    public function unreadMessages()
    {
        \Log::info('unreadMessages method called');
        try {
            // Cek authentication
            if (!Auth::check() || !in_array(Auth::user()->role, ['admin', 'super_admin'])) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized. Hanya admin yang dapat mengakses.'
                ], 403);
            }
    
            // ✅ GUNAKAN MODEL & SCOPE - bukan query raw
            $unreadMessages = Kontak::unread()
                ->orderBy('id', 'desc')
                ->get();
    
            // ✅ RESPONSE YANG BENAR
            return response()->json([
                'status' => 'success',
                'message' => 'Data pesan belum dibaca berhasil diambil',
                'data' => $unreadMessages,
                'total' => $unreadMessages->count()
            ], 200);
    
        } catch (\Exception $e) {
            \Log::error('Kontak UnreadMessages Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan server: ' . $e->getMessage()
            ], 500);
            \Log::info('Unread messages count: ' . $unreadMessages->count());
        }
    }
}