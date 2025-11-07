<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // $user = auth()->user();
        $user = Auth::user();
        // \Log::info('User:', ['user' => $user]);

        if (!$user || $user->role_id !== 1) {
            return response()->json(['message' => 'Bạn không có quyền truy cập'], 403);
        }
        return $next($request);
    }
}
