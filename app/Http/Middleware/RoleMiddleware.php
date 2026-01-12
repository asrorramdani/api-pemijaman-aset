<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, $role)
    {
        $user = $request->user();

        // admin selalu boleh akses
        if ($user->role === 'admin') {
            return $next($request);
        }

        // selain admin, harus sesuai role
        if ($user->role !== $role) {
            return response()->json([
                'status' => false,
                'message' => 'Akses ditolak (role)'
            ], 403);
        }

        return $next($request);
    }
}
