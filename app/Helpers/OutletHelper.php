<?php

namespace App\Helpers;

use App\Models\Outlet;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

/**
 * Outlet Helper
 * Common helper methods for outlet operations
 */
class OutletHelper
{
    /**
     * Get current outlet for authenticated user
     *
     * @return Outlet|null
     */
    public static function getCurrentOutlet(): ?Outlet
    {
        $user = Auth::user();

        if (!$user) {
            return null;
        }

        // For outlet role, get outlet directly
        if ($user->role_type === 'outlet') {
            return Outlet::where('user_id', $user->id)->first();
        }

        // For admin role, get admin's outlets (or first outlet)
        if ($user->role_type === 'admin' && $user->admin) {
            return Outlet::where('admin_id', $user->admin->id)->first();
        }

        return null;
    }

    /**
     * Get current outlet or fail with 403
     *
     * @param string $errorMessage
     * @return Outlet
     */
    public static function getCurrentOutletOrFail(string $errorMessage = 'Outlet tidak ditemukan'): Outlet
    {
        $outlet = self::getCurrentOutlet();

        if (!$outlet) {
            abort(403, $errorMessage);
        }

        return $outlet;
    }

    /**
     * Get all outlets for current user
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getAllOutlets()
    {
        $user = Auth::user();

        if (!$user) {
            return collect();
        }

        // For outlet role, get single outlet
        if ($user->role_type === 'outlet') {
            $outlet = Outlet::where('user_id', $user->id)->first();
            return $outlet ? collect([$outlet]) : collect();
        }

        // For admin role, get all admin's outlets
        if ($user->role_type === 'admin' && $user->admin) {
            return Outlet::where('admin_id', $user->admin->id)->get();
        }

        // For superadmin, get all outlets
        if ($user->role_type === 'superadmin') {
            return Outlet::all();
        }

        return collect();
    }

    /**
     * Check if user has access to specific outlet
     *
     * @param int $outletId
     * @param User|null $user
     * @return bool
     */
    public static function hasAccessToOutlet(int $outletId, ?User $user = null): bool
    {
        $user = $user ?? Auth::user();

        if (!$user) {
            return false;
        }

        // Superadmin has access to all outlets
        if ($user->role_type === 'superadmin') {
            return true;
        }

        // Outlet role - check if user_id matches
        if ($user->role_type === 'outlet') {
            $outlet = Outlet::find($outletId);
            return $outlet && $outlet->user_id === $user->id;
        }

        // Admin role - check if outlet belongs to admin
        if ($user->role_type === 'admin' && $user->admin) {
            $outlet = Outlet::find($outletId);
            return $outlet && $outlet->admin_id === $user->admin->id;
        }

        return false;
    }

    /**
     * Get outlet IDs accessible by current user
     *
     * @return array
     */
    public static function getAccessibleOutletIds(): array
    {
        $outlets = self::getAllOutlets();
        return $outlets->pluck('id')->toArray();
    }
}
