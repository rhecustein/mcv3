<?php

namespace App\Http\Controllers;

use App\Models\SessionLogin;
use App\Models\IpLock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SessionLoginController extends Controller
{
    /**
     * Menampilkan daftar aktivitas login, group by IP.
     */
    public function index(Request $request)
    {
        $query = SessionLogin::select(
                'ip_address',
                DB::raw('MAX(city) as city'),
                DB::raw('MAX(province) as province'),
                DB::raw('MAX(user_agent) as user_agent'),
                DB::raw('COUNT(*) as total_login'),
                DB::raw('MAX(logged_in_at) as last_login')
            )
            ->groupBy('ip_address')
            ->orderByDesc('last_login');

        // Optional filter
        if ($request->filled('ip')) {
            $query->where('ip_address', 'like', '%' . $request->ip . '%');
        }

        $logins = $query->paginate(20);

        return view('superadmin.session_logins.index', compact('logins'));
    }

    /**
     * Blokir IP secara manual (temporary / permanent).
     */
    public function blockIp(Request $request)
    {
        $request->validate([
            'ip_address' => ['required', 'ip'],
            'lock_type' => ['required', 'in:temporary,permanent'],
            'reason' => ['nullable', 'string', 'max:255'],
        ]);

        IpLock::create([
            'ip_address' => $request->ip_address,
            'lock_type' => $request->lock_type,
            'reason' => $request->reason ?? 'Blokir manual dari admin',
            'locked_by' => Auth::id(),
            'locked_at' => now(),
            'unlocked_at' => $request->lock_type === 'temporary' ? now()->addMinutes(30) : null,
            'city' => $request->city ?? null,
            'province' => $request->province ?? null,
            'logged_in_at' => now(),
            'success' => false,
            'is_active' => true,
        ]);

        return redirect()->back()->with('success', 'IP berhasil diblokir.');
    }

    /**
     * Unblock IP yang diblokir.
     */
    public function unblockIp(Request $request)
    {
        $request->validate([
            'ip_address' => ['required', 'ip'],
        ]);

        IpLock::where('ip_address', $request->ip_address)
            ->where('is_active', true)
            ->update([
                'is_active' => false,
                'unlocked_at' => now(),
            ]);

        return redirect()->back()->with('success', 'IP berhasil di-unblock.');
    }


    public function indexOutlet()
    {
        $user = auth()->user();

        // Batasi akses hanya untuk role outlet
        if ($user->role_type !== 'outlet') {
            abort(403, 'Akses ditolak. Hanya outlet yang dapat melihat halaman ini.');
        }

        // Ambil sesi login milik user ini (paginate 5)
        $sessions = SessionLogin::where('user_id', $user->id)
            ->orderByDesc('is_active')
            ->orderByDesc('last_activity_at')
            ->paginate(5);

        return view('profile.sessions', compact('sessions'));
    }

    public function forceLogoutOutlet($id)
    {
        $user = auth()->user();


        // Batasi akses hanya untuk role outlet
        if ($user->role_type !== 'outlet') {
            abort(403, 'Akses ditolak.');
        }

        // Ambil sesi aktif milik user sendiri
        $session = SessionLogin::where('id', $id)
            ->where('user_id', $user->id)
            ->where('is_active', true)
            ->firstOrFail();

        $session->update([
            'is_active' => false,
            'logged_out_at' => now(),
        ]);

        return back()->with('status', 'Sesi berhasil diputus.');
    }
}
