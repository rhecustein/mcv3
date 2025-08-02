<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string $role)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        
        // Check if user has the required role
        if ($user->role_type !== $role) {
            // Log unauthorized access attempt
            Log::warning('Unauthorized role access attempt', [
                'user_id' => $user->id,
                'user_role' => $user->role_type,
                'required_role' => $role,
                'url' => $request->fullUrl(),
                'ip' => $request->ip(),
            ]);

            abort(403, 'Unauthorized. You do not have permission to access this resource.');
        }

        return $next($request);
    }
}

// Registrasi di Kernel.php
// Tambahkan di $routeMiddleware array:
// 'role' => \App\Http\Middleware\CheckRole::class,