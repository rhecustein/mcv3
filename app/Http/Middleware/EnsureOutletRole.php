<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureOutletRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $user = Auth::user();
        
        // Check if user is outlet role
        if ($user->role_type !== 'outlet') {
            abort(403, 'Akses ditolak. Halaman ini khusus untuk outlet.');
        }

        // Check if user account is active
        if (!$user->is_active) {
            Auth::logout();
            return redirect()->route('login')->with('error', 'Akun Anda telah dinonaktifkan. Hubungi administrator.');
        }

        // Check if outlet exists and is active
        $outlet = $user->outlet;
        if (!$outlet) {
            abort(404, 'Data outlet tidak ditemukan.');
        }

        if (!$outlet->is_active) {
            Auth::logout();
            return redirect()->route('login')->with('error', 'Outlet Anda telah dinonaktifkan. Hubungi administrator.');
        }

        return $next($request);
    }
}