<?php

namespace App\Policies;

use App\Models\Patient;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PatientPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any patients.
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role_type, ['superadmin', 'admin', 'outlet', 'doctor', 'company']);
    }

    /**
     * Determine whether the user can view the patient.
     */
    public function view(User $user, Patient $patient): bool
    {
        // Superadmin can view all
        if ($user->role_type === 'superadmin') {
            return true;
        }

        // Admin can view all patients
        if ($user->role_type === 'admin') {
            return true;
        }

        // Outlet can only view patients from their outlet
        if ($user->role_type === 'outlet') {
            $userOutlet = $user->outlet;
            if (!$userOutlet) {
                return false;
            }
            return $userOutlet->id === $patient->outlet_id;
        }

        // Doctor can view patients from their outlet
        if ($user->role_type === 'doctor') {
            $doctor = $user->doctor;
            if (!$doctor) {
                return false;
            }
            return $doctor->outlet_id === $patient->outlet_id;
        }

        // Company can view their own employees/patients
        if ($user->role_type === 'company') {
            return $patient->company_id && $patient->company->user_id === $user->id;
        }

        // Patient can view themselves
        if ($user->role_type === 'patient') {
            $userPatient = $user->patient;
            if (!$userPatient) {
                return false;
            }
            return $userPatient->id === $patient->id;
        }

        return false;
    }

    /**
     * Determine whether the user can create patients.
     */
    public function create(User $user): bool
    {
        return in_array($user->role_type, ['superadmin', 'admin', 'outlet', 'doctor']);
    }

    /**
     * Determine whether the user can update the patient.
     */
    public function update(User $user, Patient $patient): bool
    {
        // Superadmin can update all
        if ($user->role_type === 'superadmin') {
            return true;
        }

        // Admin can update all
        if ($user->role_type === 'admin') {
            return true;
        }

        // Outlet can only update patients from their outlet
        if ($user->role_type === 'outlet') {
            $userOutlet = $user->outlet;
            if (!$userOutlet) {
                return false;
            }
            return $userOutlet->id === $patient->outlet_id;
        }

        // Doctor can update patients from their outlet
        if ($user->role_type === 'doctor') {
            $doctor = $user->doctor;
            if (!$doctor) {
                return false;
            }
            return $doctor->outlet_id === $patient->outlet_id;
        }

        // Patient can update themselves (limited fields)
        if ($user->role_type === 'patient') {
            $userPatient = $user->patient;
            if (!$userPatient) {
                return false;
            }
            return $userPatient->id === $patient->id;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the patient.
     */
    public function delete(User $user, Patient $patient): bool
    {
        // Only superadmin and admin can delete patients
        if ($user->role_type === 'superadmin' || $user->role_type === 'admin') {
            return true;
        }

        // Outlet can delete patients from their outlet
        if ($user->role_type === 'outlet') {
            $userOutlet = $user->outlet;
            if (!$userOutlet) {
                return false;
            }
            return $userOutlet->id === $patient->outlet_id;
        }

        return false;
    }

    /**
     * Determine whether the user can restore the patient.
     */
    public function restore(User $user, Patient $patient): bool
    {
        return in_array($user->role_type, ['superadmin', 'admin']);
    }

    /**
     * Determine whether the user can permanently delete the patient.
     */
    public function forceDelete(User $user, Patient $patient): bool
    {
        return $user->role_type === 'superadmin';
    }
}
