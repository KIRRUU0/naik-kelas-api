<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param string ...$roles  // Menerima daftar role yang diizinkan (e.g., 'super_admin', 'admin')
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        // 1. Periksa apakah pengguna terautentikasi
        if (! Auth::guard('sanctum')->check()) {
            return response()->json(['message' => 'Tambahkan Token Super Admin'], 401);
        }

        $user = $request->user();

        // 2. Periksa apakah role pengguna ada dalam daftar role yang diizinkan
        if (!in_array($user->role, $roles)) {
            return response()->json([
                'message' => 'Anda tidak memiliki izin untuk mengakses ini'
            ], 403);
        }

        return $next($request);
    }
}