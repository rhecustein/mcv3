@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto p-6 space-y-6">

    {{-- Alert sukses --}}
    @if(session('success'))
        <div class="mb-4 px-4 py-3 bg-green-100 text-green-800 border border-green-300 rounded-md text-sm shadow-sm">
            ✅ {{ session('success') }}
        </div>
    @endif

    <!-- Header -->
    <div class="flex justify-between items-center">
        <h2 class="text-xl font-bold text-gray-800">🧍 Daftar Pasien</h2>
    </div>

    <!-- Statistik Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white border rounded-md p-4 shadow-sm text-center">
            <div class="text-sm text-gray-500">Total Perusahaan</div>
            <div class="text-2xl font-bold text-blue-600">{{ $totalCompanies }}</div>
        </div>
        <div class="bg-white border rounded-md p-4 shadow-sm text-center">
            <div class="text-sm text-gray-500">Pasien Laki-laki</div>
            <div class="text-2xl font-bold text-blue-600">{{ $totalMale }}</div>
        </div>
        <div class="bg-white border rounded-md p-4 shadow-sm text-center">
            <div class="text-sm text-gray-500">Pasien Perempuan</div>
            <div class="text-2xl font-bold text-blue-600">{{ $totalFemale }}</div>
        </div>
        <div class="bg-white border rounded-md p-4 shadow-sm text-center">
            <div class="text-sm text-gray-500">Total Pasien</div>
            <div class="text-2xl font-bold text-blue-600">{{ $totalPatients }}</div>
        </div>
    </div>

    <!-- Filter -->
    <form method="GET" class="flex items-center space-x-2 mt-4">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama/NIK/telepon..."
               class="px-3 py-2 w-1/3 text-sm border rounded-md shadow-sm focus:ring focus:border-blue-300" />
        <button type="submit"
                class="px-4 py-2 bg-gray-100 border text-sm rounded hover:bg-gray-200">🔍 Cari</button>
    </form>

    <!-- Tabel Pasien -->
    @if($patients->count())
        <div class="overflow-x-auto rounded shadow border mt-4">
            <table class="min-w-full bg-white">
                <thead class="bg-gray-100 text-xs text-gray-700 uppercase">
                    <tr>
                        <th class="px-4 py-3 text-left">Nama</th>
                        <th class="px-4 py-3 text-left">NIK</th>
                        <th class="px-4 py-3 text-left">Telepon</th>
                        <th class="px-4 py-3 text-left">Perusahaan</th>
                        <th class="px-4 py-3 text-left">Outlet</th>
                        <th class="px-4 py-3 text-center">Surat Bulan Ini</th>
                        <th class="px-4 py-3 text-center">Total Surat</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y text-gray-800">
                    @foreach($patients as $patient)
                        <tr>
                            <td class="px-4 py-3">{{ $patient->full_name }}</td>
                            <td class="px-4 py-3">{{ $patient->nik ?? '-' }}</td>
                            <td class="px-4 py-3">{{ $patient->phone ?? '-' }}</td>
                            <td class="px-4 py-3">{{ $patient->company->name ?? '-' }}</td>
                            <td class="px-4 py-3">{{ $patient->outlet->name ?? '-' }}</td>
                            <td class="px-4 py-3 text-center">
                                {{ $patient->results()->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count() }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                {{ $patient->results()->count() }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $patients->withQueryString()->links() }}
        </div>
    @else
        <p class="text-gray-500 mt-4">Belum ada pasien terdaftar.</p>
    @endif
</div>
@endsection
