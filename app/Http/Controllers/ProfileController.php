<?php

namespace App\Http\Controllers;

use App\Models\IpLock;
use App\Models\Notification;
use App\Models\SessionLogin;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class ProfileController extends Controller
{
    public function settings()
    {
        $user = Auth::user();
        return view('settings.index', compact('user'));
    }

    public function activity()
    {
        $user = Auth::user();

        $activities = ActivityLog::where('causer_id', $user->id)
            ->orderByDesc('created_at')
            ->take(30)
            ->get();

        return view('settings.activity', compact('user', 'activities'));
    }

    public function session()
    {
        $user = Auth::user();
        $sessions = SessionLogin::where('user_id', $user->id)
            ->orderByDesc('logged_in_at')
            ->take(10)->get();
        $ipLocks = IpLock::where('ip_address', $user->last_ip)->get();
        return view('settings.session', compact('user', 'sessions', 'ipLocks'));
    }

    public function notifications()
    {
        $user = Auth::user();
        $notifications = Notification::where('user_id', $user->id)->latest()->paginate(10);
        return view('settings.notifications', compact('user', 'notifications'));
    }

    public function updateNotifications(Request $request)
    {
        $user = Auth::user();
        Notification::where('user_id', $user->id)->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
        return back()->with('success', 'Semua notifikasi telah ditandai dibaca.');
    }

    public function edit()
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
        ]);

        $user->update($validated);

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'Password berhasil diperbarui.');
    }

    public function show($encryptedId)
    {
        $id = decrypt($encryptedId);
        $user = User::findOrFail($id);
        return view('profile.show', compact('user'));
    }

    public function sessionLogs()
    {
        $sessions = SessionLogin::where('user_id', auth()->id())
            ->latest('logged_in_at')
            ->limit(10)
            ->get();

        return view('profile.sessions', compact('sessions'));
    }
}
