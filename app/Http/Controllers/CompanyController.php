<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Package; // TAMBAHKAN: Untuk relasi
use Illuminate\Http\Request;
use Carbon\Carbon; // TAMBAHKAN: Untuk manipulasi tanggal

class CompanyController extends Controller
{
    /**
     * Menampilkan daftar semua perusahaan.
     * DISESUAIKAN UNTUK VIEW BARU
     */
    public function index(Request $request) 
    {
        // UBAH: Optimalkan query dengan eager loading dan withCount
        $query = Company::with('package')
            ->withCount('results as letters_used_count'); // TAMBAHKAN: Untuk progress bar

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%'.$request->search.'%')
                  ->orWhere('email', 'like', '%'.$request->search.'%')
                  ->orWhere('industry_type', 'like', '%'.$request->search.'%');
            });
        }

        if ($request->filled('is_active')) { // UBAH: Cukup periksa 'filled'
            $query->where('is_active', $request->is_active);
        }

        // TAMBAHKAN: Ambil data untuk kartu statistik
        $totalCompanies = Company::count();
        $activeCompanies = Company::where('is_active', true)->count();
        $expiredPackages = Company::where('package_end_date', '<', Carbon::now())->count();

        $companies = $query->latest()->paginate(10);
        
        // UBAH: Kirim semua data yang dibutuhkan ke view
        return view('superadmin.companies.index', compact(
            'companies', 
            'totalCompanies',
            'activeCompanies',
            'expiredPackages'
        ));
    }

    /**
     * Menampilkan form untuk membuat perusahaan baru.
     */
    public function create() 
    {
        // TAMBAHKAN: Kirim data paket ke view
        $packages = Package::where('is_active', true)->get();
        return view('superadmin.companies.create', compact('packages'));
    }

    /**
     * Menyimpan perusahaan baru ke database.
     */
    public function store(Request $request) 
    {
        // UBAH: Tambahkan validasi untuk paket
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|unique:companies',
            'email' => 'nullable|email|max:255|unique:companies,email',
            'phone' => 'nullable|string|max:20',
            'industry_type' => 'nullable|string|max:100',
            'registration_number' => 'nullable|string|max:100',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'province' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'package_id' => 'nullable|exists:packages,id', // TAMBAHKAN
            'is_active' => 'boolean', // UBAH: Gunakan validasi boolean
        ]);
        
        // TAMBAHKAN: Logika untuk mengatur tanggal mulai dan berakhir paket
        if (isset($validated['package_id'])) {
            $package = Package::find($validated['package_id']);
            if ($package) {
                $validated['package_start_date'] = Carbon::now();
                $validated['package_end_date'] = Carbon::now()->addDays($package->duration_in_days);
            }
        }
        
        Company::create($validated);

        return redirect()->route('companies.index')->with('success', 'Perusahaan berhasil ditambahkan.');
    }

    /**
     * Menampilkan form untuk mengedit perusahaan.
     */
    public function edit(Company $company) 
    {
        // TAMBAHKAN: Kirim data paket ke view edit
        $packages = Package::where('is_active', true)->get();
        return view('superadmin.companies.edit', compact('company', 'packages'));
    }

    /**
     * Memperbarui data perusahaan di database.
     */
    public function update(Request $request, Company $company) 
    {
        // UBAH: Tambahkan validasi untuk paket
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|unique:companies,code,' . $company->id,
            'email' => 'nullable|email|max:255|unique:companies,email,' . $company->id,
            'phone' => 'nullable|string|max:20',
            'industry_type' => 'nullable|string|max:100',
            'registration_number' => 'nullable|string|max:100',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'province' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'package_id' => 'nullable|exists:packages,id', // TAMBAHKAN
            'is_active' => 'boolean', // UBAH: Gunakan validasi boolean
        ]);
        
        // TAMBAHKAN: Logika jika paket diubah
        if ($request->filled('package_id') && $request->package_id != $company->package_id) {
            $package = Package::find($validated['package_id']);
            if ($package) {
                $validated['package_start_date'] = Carbon::now();
                $validated['package_end_date'] = Carbon::now()->addDays($package->duration_in_days);
            }
        }

        $company->update($validated);

        return redirect()->route('companies.index')->with('success', 'Perusahaan berhasil diperbarui.');
    }

    /**
     * Menghapus perusahaan dari database.
     */
    public function destroy(Company $company) 
    {
        $company->delete();
        return redirect()->route('companies.index')->with('success', 'Perusahaan berhasil dihapus.');
    }
}