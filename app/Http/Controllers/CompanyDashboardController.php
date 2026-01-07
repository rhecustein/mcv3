<?php

namespace App\Http\Controllers;

use App\Services\CompanyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

/**
 * Company Dashboard Controller
 * Handles company dashboard, profile management, and cache operations
 */
class CompanyDashboardController extends Controller
{
    public function __construct(
        protected CompanyService $companyService
    ) {}

    /**
     * Show company dashboard with statistics
     */
    public function dashboard()
    {
        try {
            $company = $this->companyService->getUserCompany();

            if (!$company) {
                return redirect()->route('login')
                    ->with('error', 'Company tidak ditemukan untuk akun ini.');
            }

            // Cache key for dashboard data
            $cacheKey = "company_dashboard_{$company->id}";

            $dashboardData = Cache::remember($cacheKey, 30, function () use ($company) {
                $patientIds = $this->companyService->getCompanyPatients($company)->pluck('id');

                $stats = $this->companyService->calculateCompanyStats($company, $patientIds);
                $chartData = $this->companyService->getMonthlyChartData($patientIds);
                $recentResults = $this->companyService->getRecentResults($patientIds);
                $healthSummary = $this->companyService->getHealthStatusSummary($patientIds);
                $topOutlets = $this->companyService->getTopOutlets($patientIds);

                return compact('stats', 'chartData', 'recentResults', 'healthSummary', 'topOutlets');
            });

            return view('companies.dashboard', array_merge(['company' => $company], $dashboardData));

        } catch (\Exception $e) {
            Log::error('Company dashboard error: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('login')
                ->with('error', 'Terjadi kesalahan saat memuat dashboard.');
        }
    }

    /**
     * Show company profile
     */
    public function profile()
    {
        try {
            $user = Auth::user();
            $company = $this->companyService->getUserCompany();

            if (!$company) {
                return redirect()->route('company.dashboard')
                    ->with('error', 'Company tidak ditemukan.');
            }

            $patientIds = $this->companyService->getCompanyPatients($company)->pluck('id');
            $profileStats = $this->companyService->getProfileStats($patientIds);

            return view('companies.profile', compact('user', 'company', 'profileStats'));

        } catch (\Exception $e) {
            Log::error('Company profile error: ' . $e->getMessage(), [
                'user_id' => Auth::id()
            ]);

            return redirect()->route('company.dashboard')
                ->with('error', 'Terjadi kesalahan saat memuat profil.');
        }
    }

    /**
     * Edit company profile
     */
    public function editProfile()
    {
        try {
            $user = Auth::user();
            $company = $this->companyService->getUserCompany();

            if (!$company) {
                return redirect()->route('company.dashboard')
                    ->with('error', 'Company tidak ditemukan.');
            }

            return view('companies.edit-profile', compact('user', 'company'));

        } catch (\Exception $e) {
            Log::error('Edit profile error: ' . $e->getMessage());
            return redirect()->route('company.profile.show')
                ->with('error', 'Terjadi kesalahan saat memuat halaman edit profil.');
        }
    }

    /**
     * Update company profile
     */
    public function updateProfile(Request $request)
    {
        try {
            $company = $this->companyService->getUserCompany();

            if (!$company) {
                return redirect()->route('company.dashboard')
                    ->with('error', 'Company tidak ditemukan.');
            }

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'industry_type' => 'nullable|string|max:100',
                'email' => 'required|email|max:255|unique:companies,email,' . $company->id,
                'phone' => 'nullable|string|max:20',
                'address' => 'nullable|string|max:500',
                'city' => 'nullable|string|max:100',
                'province' => 'nullable|string|max:100',
                'description' => 'nullable|string|max:1000',
                'website' => 'nullable|url|max:255',
            ]);

            $this->companyService->updateCompany($company, $validated);

            return redirect()->route('company.profile.show')
                ->with('success', 'Profil perusahaan berhasil diperbarui.');

        } catch (ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            Log::error('Update profile error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat memperbarui profil.')
                ->withInput();
        }
    }

    /**
     * Clear company cache
     */
    public function clearCache()
    {
        try {
            $company = $this->companyService->getUserCompany();

            if ($company) {
                $this->companyService->clearCompanyCache($company->id);
            }

            return response()->json(['message' => 'Cache berhasil dibersihkan']);

        } catch (\Exception $e) {
            Log::error('Clear cache error: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal membersihkan cache'], 500);
        }
    }
}
