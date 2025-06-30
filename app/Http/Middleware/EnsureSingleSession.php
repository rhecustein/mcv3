<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\SessionLogin;
use Illuminate\Support\Facades\Auth;

class EnsureSingleSession // bisa ganti nama jadi EnsureLimitedSessions jika lebih deskriptif
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $currentSessionId = session()->getId();
            $userId = Auth::id();

            // Ambil 3 sesi aktif terakhir berdasarkan waktu login
            $activeSessions = SessionLogin::where('user_id', $userId)
                ->where('is_active', true)
                ->orderByDesc('logged_in_at')
                ->limit(3)
                ->pluck('session_id');

            // Jika sesi sekarang tidak termasuk dalam 3 sesi aktif tersebut, logout
            if (!$activeSessions->contains($currentSessionId)) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')
                    ->withErrors(['email' => 'Sesi Anda telah digantikan oleh login lain.']);
            }
        }

        return $next($request);
    }
}
