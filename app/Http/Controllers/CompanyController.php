<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    public function index(Request $request) {
        $query = Company::query();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%'.$request->search.'%')
                  ->orWhere('email', 'like', '%'.$request->search.'%')
                  ->orWhere('industry_type', 'like', '%'.$request->search.'%');
            });
        }

        if ($request->has('is_active') && $request->is_active !== null) {
            $query->where('is_active', $request->is_active);
        }

        $companies = $query->with('package')->latest()->paginate(10);
        $totalCompanies = Company::count();

        return view('superadmin.companies.index', compact('companies', 'totalCompanies'));
    }

    public function create() {
        return view('superadmin.companies.create');
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|unique:companies',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:20',
            'industry_type' => 'nullable|string|max:100',
            'registration_number' => 'nullable|string|max:100',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'province' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
        ]);

        Company::create($validated + [
            'is_active' => $request->has('is_active')
        ]);

        return redirect()->route('companies.index')->with('success', 'Perusahaan berhasil ditambahkan.');
    }

    public function edit(Company $company) {
        return view('superadmin.companies.edit', compact('company'));
    }

    public function update(Request $request, Company $company) {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|unique:companies,code,' . $company->id,
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:20',
            'industry_type' => 'nullable|string|max:100',
            'registration_number' => 'nullable|string|max:100',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'province' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
        ]);

        $company->update($validated + [
            'is_active' => $request->has('is_active')
        ]);

        return redirect()->route('companies.index')->with('success', 'Perusahaan berhasil diperbarui.');
    }

    public function destroy(Company $company) {
        $company->delete();
        return redirect()->route('companies.index')->with('success', 'Perusahaan berhasil dihapus.');
    }
}
