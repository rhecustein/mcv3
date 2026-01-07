<?php

namespace App\Policies;

use App\Models\Company;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CompanyPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any companies.
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role_type, ['superadmin', 'admin', 'outlet', 'company']);
    }

    /**
     * Determine whether the user can view the company.
     */
    public function view(User $user, Company $company): bool
    {
        // Superadmin can view all
        if ($user->role_type === 'superadmin') {
            return true;
        }

        // Admin can view all companies
        if ($user->role_type === 'admin') {
            return true;
        }

        // Outlet can view companies they work with
        if ($user->role_type === 'outlet') {
            return true; // Outlets can view all companies for patient assignment
        }

        // Company can only view themselves
        if ($user->role_type === 'company') {
            return $company->user_id === $user->id;
        }

        return false;
    }

    /**
     * Determine whether the user can create companies.
     */
    public function create(User $user): bool
    {
        return in_array($user->role_type, ['superadmin', 'admin']);
    }

    /**
     * Determine whether the user can update the company.
     */
    public function update(User $user, Company $company): bool
    {
        // Superadmin can update all
        if ($user->role_type === 'superadmin') {
            return true;
        }

        // Admin can update all
        if ($user->role_type === 'admin') {
            return true;
        }

        // Company can update themselves (limited fields)
        if ($user->role_type === 'company') {
            return $company->user_id === $user->id;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the company.
     */
    public function delete(User $user, Company $company): bool
    {
        // Only superadmin can delete companies
        return $user->role_type === 'superadmin';
    }

    /**
     * Determine whether the user can restore the company.
     */
    public function restore(User $user, Company $company): bool
    {
        return $user->role_type === 'superadmin';
    }

    /**
     * Determine whether the user can permanently delete the company.
     */
    public function forceDelete(User $user, Company $company): bool
    {
        return $user->role_type === 'superadmin';
    }

    /**
     * Determine whether the user can view company reports.
     */
    public function viewReports(User $user, Company $company): bool
    {
        // Superadmin and Admin can view all reports
        if (in_array($user->role_type, ['superadmin', 'admin'])) {
            return true;
        }

        // Company can view their own reports
        if ($user->role_type === 'company') {
            return $company->user_id === $user->id;
        }

        return false;
    }

    /**
     * Determine whether the user can manage company employees.
     */
    public function manageEmployees(User $user, Company $company): bool
    {
        // Superadmin and Admin can manage all
        if (in_array($user->role_type, ['superadmin', 'admin'])) {
            return true;
        }

        // Company can manage their own employees
        if ($user->role_type === 'company') {
            return $company->user_id === $user->id;
        }

        return false;
    }
}
