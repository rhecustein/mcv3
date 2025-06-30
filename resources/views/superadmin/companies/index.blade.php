@extends('layouts.app')

@section('content')
<div class="max-w-screen-xl mx-auto">
    {{-- Header --}}
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">🏢 Manajemen Perusahaan</h1>
        <a href="{{ route('companies.create') }}"
           class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg shadow hover:bg-blue-700 transition">
            ➕ Tambah Perusahaan
        </a>
    </div>

    {{-- Alert sukses --}}
    @if(session('success'))
        <div class="mb-4 px-4 py-3 bg-green-100 text-green-800 border border-green-300 rounded-md text-sm shadow-sm">
            ✅ {{ session('success') }}
        </div>
    @endif

    {{-- Count Card --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm text-gray-500">Total Perusahaan</div>
            <div class="text-2xl font-bold text-blue-600">{{ $totalCompanies }}</div>
        </div>
    </div>

    {{-- Filter & Search --}}
    <form method="GET" class="bg-white p-4 rounded-lg shadow mb-6 flex flex-wrap gap-4 items-center">
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Cari nama / email / industri..."
               class="w-full md:w-1/3 px-4 py-2 border border-gray-300 rounded-md text-sm">

        <select name="is_active" class="w-full md:w-1/4 px-4 py-2 border border-gray-300 rounded-md text-sm">
            <option value="">-- Status --</option>
            <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>Aktif</option>
            <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>Nonaktif</option>
        </select>

        <button type="submit"
                class="px-4 py-2 bg-blue-600 text-white rounded-md text-sm hover:bg-blue-700 transition">
            🔍 Filter
        </button>

        <a href="{{ route('companies.index') }}"
           class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md text-sm hover:bg-gray-200 transition">
            🔄 Reset
        </a>
    </form>

    {{-- Tabel --}}
    <div class="bg-white shadow rounded-lg overflow-x-auto">
        <table class="min-w-full text-sm text-left text-gray-700">
            <thead class="bg-gray-100 text-gray-800 font-semibold">
                <tr>
                    <th class="px-4 py-3">Nama</th>
                    <th class="px-4 py-3">Email</th>
                    <th class="px-4 py-3">Telepon</th>
                    <th class="px-4 py-3">Industri</th>
                    <th class="px-4 py-3">Paket</th>
                    <th class="px-4 py-3">Durasi</th>
                    <th class="px-4 py-3">Maks Surat</th>
                    <th class="px-4 py-3">Maks Pasien</th>
                    <th class="px-4 py-3">Harga</th>
                    <th class="px-4 py-3">Masa Aktif</th>
                    <th class="px-4 py-3 text-center">Status</th>
                    <th class="px-4 py-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($companies as $company)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-4 py-3">{{ $company->name }}</td>
                        <td class="px-4 py-3">{{ $company->email ?? '-' }}</td>
                        <td class="px-4 py-3">{{ $company->phone ?? '-' }}</td>
                        <td class="px-4 py-3">{{ $company->industry_type ?? '-' }}</td>
                        <td class="px-4 py-3">{{ $company->package->name ?? '-' }}</td>
                        <td class="px-4 py-3">{{ $company->package->duration_in_days ?? '-' }} hari</td>
                        <td class="px-4 py-3">{{ $company->package->max_letters ?? '∞' }}</td>
                        <td class="px-4 py-3">{{ $company->package->max_patients ?? '∞' }}</td>
                        <td class="px-4 py-3">Rp {{ number_format($company->package->price ?? 0, 0, ',', '.') }}</td>
                        <td class="px-4 py-3">
                            @if ($company->package_start_date && $company->package_end_date)
                                <div>
                                    {{ $company->package_start_date->format('d M Y') }} -
                                    <br>
                                    {{ $company->package_end_date->format('d M Y') }}
                                </div>
                            @else
                                <span class="text-gray-400">Belum Aktif</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($company->is_active)
                                <span class="px-2 py-1 bg-green-100 text-green-700 rounded text-xs font-medium">Aktif</span>
                            @else
                                <span class="px-2 py-1 bg-red-100 text-red-700 rounded text-xs font-medium">Nonaktif</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center align-top">
                            <div class="flex flex-col items-center gap-2 w-28">
                                <a href="{{ route('companies.edit', $company) }}"
                                   class="w-full text-center px-3 py-1.5 text-sm bg-yellow-100 text-yellow-800 rounded hover:bg-yellow-200 transition">
                                    ✏️ Edit
                                </a>

                                <form action="{{ route('companies.destroy', $company) }}" method="POST" class="w-full">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            onclick="return confirm('Yakin ingin menghapus perusahaan ini?')"
                                            class="w-full px-3 py-1.5 text-sm bg-red-100 text-red-700 rounded hover:bg-red-200 transition">
                                        🗑️ Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="12" class="px-4 py-4 text-center text-gray-500">Belum ada data perusahaan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="mt-6">
        {{ $companies->appends(request()->query())->links() }}
    </div>
</div>
@endsection
