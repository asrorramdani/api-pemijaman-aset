<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    /**
     * REGISTER USER
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:6',
        ]);

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role'     => 'user',
        ]);

        ActivityLog::create([
            'user_id'     => $user->id,
            'action'      => 'REGISTER',
            'description' => 'User melakukan registrasi',
        ]);

        $token = JWTAuth::fromUser($user);

        return response()->json([
            'status'  => true,
            'message' => 'Register berhasil',
            'token'   => $token,
        ], 201);
    }

    /**
     * LOGIN USER
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (! $token = Auth::guard('api')->attempt($credentials)) {
            return response()->json([
                'status'  => false,
                'message' => 'Email atau password salah',
            ], 401);
        }

        $user = Auth::guard('api')->user();

        ActivityLog::create([
            'user_id'     => $user->id,
            'action'      => 'LOGIN',
            'description' => 'User login ke sistem',
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Login berhasil',
            'token'   => $token,
        ]);
    }
}