<?php

namespace App\Http\Controllers;

use App\Models\Pengguna;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash; 

class PenggunaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Mengandalkan properti $hidden di Model User untuk menyembunyikan password
        $pengguna = Pengguna::all();
        return response()->json([
            "message" => "Data pengguna berhasil diambil",
            "data" => $pengguna
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'role' => 'required|max:255',
            'foto_profil' => 'nullable|url',
            'nama' => 'required|max:255',
            'email' => 'required|email|unique:pengguna,email',
            'password' => 'required|min:6',
        ]);

        $user = Pengguna::create([
            'role' => $validatedData['role'],
            'foto_profil' => $validatedData['foto_profil'] ?? null,
            'nama' => $validatedData['nama'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
        ]);

        return response()->json([
            'message' => 'Data pengguna berhasil ditambahkan',
            'data' => $user
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(User $pengguna) // Laravel sudah menemukan modelnya
    {
        return response()->json([
            "message" => "Data pengguna berhasil diambil",
            "data" => $pengguna
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $pengguna)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'sometimes|required',
            'role' => 'sometimes|required|max:255',
            'foto_profil' => 'sometimes|nullable|url',
            // FIX: Menggunakan tabel 'pengguna' yang benar
            'email' => 'sometimes|required|email|unique:pengguna,email,'.$pengguna->id, 
            'password' => 'sometimes|required|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if ($request->has('password')) {
            $request->merge(['password' => Hash::make($request->password)]); 
        }

        $pengguna->update($request->all());

        return response()->json([
            "message" => "Data pengguna berhasil diupdate",
            "data" => $pengguna
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pengguna $pengguna)
    {
        $pengguna->delete(); // Langsung delete model yang sudah di-bind
        return response()->json([
            "message" => "Data pengguna berhasil dihapus"
        ], 200);
    }
}
