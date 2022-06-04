<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    public function daftar(Request $request)
    {
        $pesan = [
            'name.required'      => 'Nama wajib diisi.',

            'password.required'  => 'Password wajib diisi.',
            'password.confirmed' => 'Password konfirmasi tidak sesuai.',
            'password.min'       => 'Password minimal diisi dengan 5 karakter.',

            'email.required'     => 'Email wajib diisi.',
            'email.email'        => 'Email tidak valid.',
            'email.unique'       => 'Email sudah terdaftar.',

            'telp.required'      => 'Telepon wajib diisi.',
            'telp.numeric'       => 'Telepon harus berupa angka.',
            'telp.unique'        => 'Nomor telepon tertaut pada akun lain.',
        ];

        $request->validate([
            'name'               => ['required', 'string', 'max:255'],
            'email'              => ['required', 'string', 'email', 'unique:users'],
            'telp'               => ['required', 'numeric', 'unique:users'],
            'password'           => ['required', 'min:5', 'string', 'confirmed']
        ], $pesan);

        User::create([
            'name'               => $request->name,
            'email'              => $request->email,
            'telp'               => $request->telp,
            'password'           => Hash::make($request->password)
        ]);

        return response()->json([
            'status'             => 'Success',
            'msg'                => 'Registrasi Berhasil',
        ], Response::HTTP_OK);
    }

    public function masuk(Request $request)
    {
        $pesan = [
            'password.required'  => 'Password wajib diisi.',
            'password.min'       => 'Password minimal diisi dengan 5 karakter.',

            'email.required'     => 'Email wajib diisi.',
            'email.email'        => 'Email tidak valid.',
        ];

        $request->validate([
            'email'              => ['required', 'string', 'email'],
            'password'           => ['required', 'min:5', 'string']
        ], $pesan);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email'          => ['Email belum terdaftar.'],
                'password'       => ['Password salah.'],
            ]);
        }

        $token = $user->createToken($user->name)->plainTextToken;

        return response()->json([
            'status'             => 'Success',
            'msg'                => 'Login Berhasil',
            'token'              => $token,
            'data user'          => $user,
        ], Response::HTTP_OK);
    }

    public function keluar(Request $request)
    {
        $user = $request->user();
        $user->currentAccessToken()->delete();

        return response()->json([
            'status'             => 'Success',
            'msg'                => 'Logout Berhasil',
        ], Response::HTTP_OK);
    }
}
