<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\ActivityLog;

class AuthController extends Controller
{
    /**
     * REGISTER
     */
    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:6'
        ]);

        // ✅ BUAT USER SEKALI SAJA
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => 'user',
        ]);

        // ✅ ACTIVITY LOG
        ActivityLog::create([
            'user_id'     => $user->id,
            'action'      => 'REGISTER',
            'description' => 'User melakukan registrasi'
        ]);

        // JWT token
        $token = JWTAuth::fromUser($user);

        return response()->json([
            'message' => 'Register berhasil',
            'token'   => $token
        ], 201);
    }

    /**
     * LOGIN
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required'
        ]);

        if (!$token = Auth::guard('api')->attempt($credentials)) {
            return response()->json([
                'message' => 'Email atau password salah'
            ], 401);
        }

        $user = Auth::guard('api')->user();

        // ✅ ACTIVITY LOG LOGIN
        ActivityLog::create([
            'user_id'     => $user->id,
            'action'      => 'LOGIN',
            'description' => 'User login ke sistem'
        ]);

        return response()->json([
            'message' => 'Login berhasil',
            'token'   => $token
        ]);
    }
}
