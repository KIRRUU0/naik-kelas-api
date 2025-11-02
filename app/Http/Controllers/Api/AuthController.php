<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Pengguna; // Menggunakan Model Pengguna Anda

class AuthController extends Controller
{
    public function login(Request $request)
    {
        // 1. Validasi Input
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // 2. Cek Kredensial (Menggunakan Guard Web default)
        // Kita menggunakan guard 'web' di sini, tetapi Sanctum akan menggunakannya untuk mencari user
        if (!Auth::guard('web')->attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'Email atau password salah.'
            ], 401);
        }

        // 3. Ambil Objek Pengguna
        $user = Auth::guard('web')->user();

        // 4. Generate Token Sanctum
        // Nama token (misalnya 'admin-token') harus deskriptif.
        // abilities (permissions) biasanya digunakan untuk membedakan token.
        $token = $user->createToken('admin-token', ['server:admin'])->plainTextToken;

        // 5. Kembalikan Response Token dan Data Pengguna
        return response()->json([
            'message' => 'Login berhasil. Token API diberikan.',
            'user' => [
                'id' => $user->id,
                'nama' => $user->nama,
                'email' => $user->email,
                'role' => $user->role, // Penting untuk verifikasi role di frontend
            ],
            'access_token' => $token,
            'token_type' => 'Bearer',
            // Tambahkan expired_at jika Anda mengatur masa berlaku token
        ], 200);
    }
    
    public function logout(Request $request)
    {
        // Hapus token yang sedang digunakan oleh user saat ini
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout berhasil. Token API dihapus.'
        ], 200);
    }
}