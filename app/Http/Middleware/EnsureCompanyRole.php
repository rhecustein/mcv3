<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureCompanyRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $user = Auth::user();
        
        // Check if user is company role
        if ($user->role_type !== 'companies') {
            abort(403, 'Akses ditolak. Halaman ini khusus untuk perusahaan.');
        }

        // Check if user account is active
        if (!$user->is_active) {
            Auth::logout();
            return redirect()->route('login')->with('error', 'Akun Anda telah dinonaktifkan. Hubungi administrator.');
        }

        // Optional: Check if company exists and is active
        $company = $this->getUserCompany($user);
        if ($company && !$company->is_active) {
            Auth::logout();
            return redirect()->route('login')->with('error', 'Perusahaan Anda telah dinonaktifkan. Hubungi administrator.');
        }

        return $next($request);
    }

    /**
     * Get company associated with user
     */
    private function getUserCompany($user)
    {
        // If user is directly associated with company
        if ($user->company_id) {
            return \App\Models\Company::find($user->company_id);
        }
        
        // If user email matches company email
        return \App\Models\Company::where('email', $user->email)->first();
    }
}