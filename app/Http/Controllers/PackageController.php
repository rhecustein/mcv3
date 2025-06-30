<?php

namespace App\Http\Controllers;

use App\Models\Package;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    public function index()
    {
        $packages = Package::latest()->paginate(10);
        $totalPackages = Package::count();

        return view('superadmin.packages.index', compact('packages', 'totalPackages'));
    }

    public function create()
    {
        return view('superadmin.packages.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'duration_in_days' => 'required|integer|min:1',
            'max_letters' => 'nullable|integer|min:0',
            'max_patients' => 'nullable|integer|min:0',
            'price' => 'required|numeric|min:0',
            'is_active' => 'nullable|boolean'
        ]);

        Package::create($validated + [
            'is_active' => $request->has('is_active')
        ]);

        return redirect()->route('packages.index')->with('success', 'Paket berhasil ditambahkan.');
    }

    public function edit(Package $package)
    {
        return view('superadmin.packages.edit', compact('package'));
    }

    public function update(Request $request, Package $package)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'duration_in_days' => 'required|integer|min:1',
            'max_letters' => 'nullable|integer|min:0',
            'max_patients' => 'nullable|integer|min:0',
            'price' => 'required|numeric|min:0',
            'is_active' => 'nullable|boolean'
        ]);

        $package->update($validated + [
            'is_active' => $request->has('is_active')
        ]);

        return redirect()->route('packages.index')->with('success', 'Paket berhasil diperbarui.');
    }

    public function destroy(Package $package)
    {
        $package->delete();
        return redirect()->route('packages.index')->with('success', 'Paket berhasil dihapus.');
    }
}
