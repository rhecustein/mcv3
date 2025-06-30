<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRoleType
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = auth()->user();

        // Cek apakah user tidak login atau tidak punya role yang diizinkan
        if (!$user || !$user->hasRoleType($roles)) {
            abort(403, 'Akses ditolak'); // atau redirect()->route('login')
        }

        return $next($request);
    }
}
