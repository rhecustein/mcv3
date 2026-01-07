<?php

namespace App\Policies;

use App\Models\Result;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ResultPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any results.
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role_type, ['superadmin', 'admin', 'outlet', 'doctor', 'company']);
    }

    /**
     * Determine whether the user can view the result.
     */
    public function view(User $user, Result $result): bool
    {
        // Superadmin can view all
        if ($user->role_type === 'superadmin') {
            return true;
        }

        // Admin can view all results
        if ($user->role_type === 'admin') {
            return true;
        }

        // Outlet can only view their own results
        if ($user->role_type === 'outlet') {
            $userOutlet = $user->outlet;
            if (!$userOutlet) {
                return false;
            }
            return $userOutlet->id === $result->outlet_id;
        }

        // Doctor can view results they created
        if ($user->role_type === 'doctor') {
            $doctor = $user->doctor;
            if (!$doctor) {
                return false;
            }
            return $doctor->id === $result->doctor_id;
        }

        // Company can view results for their patients
        if ($user->role_type === 'company') {
            return $result->company_id && $result->company->user_id === $user->id;
        }

        return false;
    }

    /**
     * Determine whether the user can create results.
     */
    public function create(User $user): bool
    {
        return in_array($user->role_type, ['superadmin', 'admin', 'outlet', 'doctor']);
    }

    /**
     * Determine whether the user can update the result.
     */
    public function update(User $user, Result $result): bool
    {
        // Superadmin can update all
        if ($user->role_type === 'superadmin') {
            return true;
        }

        // Admin can update all
        if ($user->role_type === 'admin') {
            return true;
        }

        // Outlet can only update their own results
        if ($user->role_type === 'outlet') {
            $userOutlet = $user->outlet;
            if (!$userOutlet) {
                return false;
            }
            return $userOutlet->id === $result->outlet_id;
        }

        // Doctor can update results they created (within same outlet)
        if ($user->role_type === 'doctor') {
            $doctor = $user->doctor;
            if (!$doctor) {
                return false;
            }
            return $doctor->id === $result->doctor_id && $doctor->outlet_id === $result->outlet_id;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the result.
     */
    public function delete(User $user, Result $result): bool
    {
        // Same logic as update
        return $this->update($user, $result);
    }

    /**
     * Determine whether the user can restore the result.
     */
    public function restore(User $user, Result $result): bool
    {
        // Superadmin and Admin can restore
        return in_array($user->role_type, ['superadmin', 'admin']);
    }

    /**
     * Determine whether the user can permanently delete the result.
     */
    public function forceDelete(User $user, Result $result): bool
    {
        // Only superadmin can force delete
        return $user->role_type === 'superadmin';
    }

    /**
     * Determine whether the user can download/print the result.
     */
    public function download(User $user, Result $result): bool
    {
        // Anyone who can view can download
        return $this->view($user, $result);
    }
}
