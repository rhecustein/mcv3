<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AutoLogoutIfInactive
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $ip = $request->ip();

            $session = DB::table('session_logins')
                ->where('user_id', $user->id)
                ->where('ip_address', $ip)
                ->where('is_active', true)
                ->first();

            if ($session) {
                $lastActivity = Carbon::parse($session->last_activity_at);
                $timeout = now()->subMinutes(30);

                if ($lastActivity->lessThan($timeout)) {
                    DB::table('session_logins')
                        ->where('id', $session->id)
                        ->update(['is_active' => false]);

                    Auth::logout();
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();

                    return redirect()->route('login')->withErrors([
                        'email' => 'Anda telah logout otomatis karena tidak aktif selama 30 menit.',
                    ]);
                }

                // Update waktu aktivitas terakhir
                DB::table('session_logins')
                    ->where('id', $session->id)
                    ->update(['last_activity_at' => now()]);
            }
        }

        return $next($request);
    }
}
