<?php

namespace App\Policies;

use App\Models\Doctor;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class DoctorPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any doctors.
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role_type, ['superadmin', 'admin', 'outlet', 'doctor']);
    }

    /**
     * Determine whether the user can view the doctor.
     */
    public function view(User $user, Doctor $doctor): bool
    {
        // Superadmin can view all
        if ($user->role_type === 'superadmin') {
            return true;
        }

        // Admin can view all doctors
        if ($user->role_type === 'admin') {
            return true;
        }

        // Outlet can only view doctors from their outlet
        if ($user->role_type === 'outlet') {
            $userOutlet = $user->outlet;
            if (!$userOutlet) {
                return false;
            }
            return $userOutlet->id === $doctor->outlet_id;
        }

        // Doctor can view themselves and doctors from same outlet
        if ($user->role_type === 'doctor') {
            $userDoctor = $user->doctor;
            if (!$userDoctor) {
                return false;
            }
            return $userDoctor->id === $doctor->id || $userDoctor->outlet_id === $doctor->outlet_id;
        }

        return false;
    }

    /**
     * Determine whether the user can create doctors.
     */
    public function create(User $user): bool
    {
        return in_array($user->role_type, ['superadmin', 'admin', 'outlet']);
    }

    /**
     * Determine whether the user can update the doctor.
     */
    public function update(User $user, Doctor $doctor): bool
    {
        // Superadmin can update all
        if ($user->role_type === 'superadmin') {
            return true;
        }

        // Admin can update all
        if ($user->role_type === 'admin') {
            return true;
        }

        // Outlet can only update doctors from their outlet
        if ($user->role_type === 'outlet') {
            $userOutlet = $user->outlet;
            if (!$userOutlet) {
                return false;
            }
            return $userOutlet->id === $doctor->outlet_id;
        }

        // Doctor can update themselves (limited fields)
        if ($user->role_type === 'doctor') {
            $userDoctor = $user->doctor;
            if (!$userDoctor) {
                return false;
            }
            return $userDoctor->id === $doctor->id;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the doctor.
     */
    public function delete(User $user, Doctor $doctor): bool
    {
        // Only superadmin and admin can delete doctors
        if ($user->role_type === 'superadmin' || $user->role_type === 'admin') {
            return true;
        }

        // Outlet can delete doctors from their outlet
        if ($user->role_type === 'outlet') {
            $userOutlet = $user->outlet;
            if (!$userOutlet) {
                return false;
            }
            return $userOutlet->id === $doctor->outlet_id;
        }

        return false;
    }

    /**
     * Determine whether the user can restore the doctor.
     */
    public function restore(User $user, Doctor $doctor): bool
    {
        return in_array($user->role_type, ['superadmin', 'admin']);
    }

    /**
     * Determine whether the user can permanently delete the doctor.
     */
    public function forceDelete(User $user, Doctor $doctor): bool
    {
        return $user->role_type === 'superadmin';
    }
}
