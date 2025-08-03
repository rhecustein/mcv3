<?php

namespace App\Http\Controllers;

use App\Models\IpLock;
use App\Models\Notification;
use App\Models\SessionLogin;
use App\Models\User;
use App\Models\ActivityLog;
use App\Models\Outlet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules;

class OutletProfileController extends Controller
{
    /**
     * Show outlet settings dashboard
     */
    public function settings()
    {
        $user = Auth::user();
        $outlet = Outlet::where('user_id', $user->id)->first();
        
        if (!$outlet) {
            return redirect()->route('outlet.dashboard')
                ->with('error', 'Data outlet tidak ditemukan.');
        }
        
        return view('outlets.settings.index', compact('user', 'outlet'));
    }

    /**
     * Show outlet activity logs
     */
    public function activity()
    {
        $user = Auth::user();
        $outlet = Outlet::where('user_id', $user->id)->first();

        if (!$outlet) {
            return redirect()->route('outlet.dashboard')
                ->with('error', 'Data outlet tidak ditemukan.');
        }

        $activities = ActivityLog::where('causer_id', $user->id)
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('outlets.settings.activity', compact('user', 'outlet', 'activities'));
    }

    /**
     * Show outlet notifications
     */
    public function notifications()
    {
        $user = Auth::user();
        $outlet = Outlet::where('user_id', $user->id)->first();
        
        if (!$outlet) {
            return redirect()->route('outlet.dashboard')
                ->with('error', 'Data outlet tidak ditemukan.');
        }
        
        $notifications = Notification::where('user_id', $user->id)
            ->latest()
            ->paginate(15);
            
        return view('outlets.settings.notifications', compact('user', 'outlet', 'notifications'));
    }

    /**
     * Mark all notifications as read
     */
    public function updateNotifications(Request $request)
    {
        $user = Auth::user();
        
        Notification::where('user_id', $user->id)->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
        
        return back()->with('success', 'Semua notifikasi telah ditandai sebagai dibaca.');
    }

    /**
     * Show outlet profile edit form
     */
    public function edit()
    {
        $user = Auth::user();
        $outlet = Outlet::where('user_id', $user->id)->first();
        
        if (!$outlet) {
            return redirect()->route('outlet.dashboard')
                ->with('error', 'Data outlet tidak ditemukan.');
        }
        
        return view('outlets.profile.edit', compact('user', 'outlet'));
    }

    /**
     * Update outlet profile information
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        $outlet = Outlet::where('user_id', $user->id)->first();
        
        if (!$outlet) {
            return back()->with('error', 'Data outlet tidak ditemukan.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'province' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:10',
        ]);

        DB::beginTransaction();
        try {
            // Update user data
            $user->update([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
            ]);

            // Update outlet data
            $outlet->update([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'address' => $validated['address'],
                'city' => $validated['city'],
                'province' => $validated['province'],
                'postal_code' => $validated['postal_code'],
            ]);

            DB::commit();
            
            return back()->with('success', 'Profil outlet berhasil diperbarui.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating outlet profile: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memperbarui profil.');
        }
    }

    /**
     * Update outlet password
     */
    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        try {
            $user->update([
                'password' => Hash::make($request->password),
            ]);

            return back()->with('success', 'Password berhasil diperbarui.');
            
        } catch (\Exception $e) {
            Log::error('Error updating outlet password: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memperbarui password.');
        }
    }

    /**
     * Show outlet information page
     */
    public function show()
    {
        $user = Auth::user();
        $outlet = Outlet::where('user_id', $user->id)->first();
        
        if (!$outlet) {
            return redirect()->route('outlet.dashboard')
                ->with('error', 'Data outlet tidak ditemukan.');
        }

        // Get outlet statistics
        $stats = [
            'total_doctors' => $outlet->doctors()->count(),
            'active_doctors' => $outlet->doctors()->whereHas('user', function($q) {
                $q->where('is_active', true);
            })->count(),
            'total_patients' => $outlet->patients()->count(),
            'this_month_patients' => $outlet->patients()->whereMonth('created_at', now()->month)->count(),
        ];
        
        return view('outlets.profile.show', compact('user', 'outlet', 'stats'));
    }

    /**
     * Update outlet location coordinates
     */
    public function updateLocation(Request $request)
    {
        $user = Auth::user();
        $outlet = Outlet::where('user_id', $user->id)->first();
        
        if (!$outlet) {
            return response()->json(['error' => 'Data outlet tidak ditemukan.'], 404);
        }

        $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);

        try {
            $outlet->update([
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Lokasi outlet berhasil diperbarui.'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error updating outlet location: ' . $e->getMessage());
            return response()->json([
                'error' => 'Terjadi kesalahan saat memperbarui lokasi.'
            ], 500);
        }
    }

    /**
     * Get outlet profile API data
     */
    public function apiProfile()
    {
        $user = Auth::user();
        $outlet = Outlet::where('user_id', $user->id)->first();
        
        if (!$outlet) {
            return response()->json(['error' => 'Data outlet tidak ditemukan.'], 404);
        }

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'role_type' => $user->role_type,
                'is_active' => $user->is_active,
                'created_at' => $user->created_at,
            ],
            'outlet' => [
                'id' => $outlet->id,
                'name' => $outlet->name,
                'address' => $outlet->address,
                'city' => $outlet->city,
                'province' => $outlet->province,
                'postal_code' => $outlet->postal_code,
                'latitude' => $outlet->latitude,
                'longitude' => $outlet->longitude,
                'is_active' => $outlet->is_active,
            ]
        ]);
    }
}
