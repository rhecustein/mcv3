@extends('layouts.app')

@section('content')
<div class="max-w-screen-xl mx-auto">
    {{-- Header --}}
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">💳 Manajemen Paket</h1>
        <a href="{{ route('packages.create') }}"
           class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg shadow hover:bg-blue-700 transition">
            ➕ Tambah Paket
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
            <div class="text-sm text-gray-500">Total Paket</div>
            <div class="text-2xl font-bold text-blue-600">{{ $totalPackages }}</div>
        </div>
    </div>

    {{-- Tabel --}}
    <div class="bg-white shadow rounded-lg overflow-x-auto">
        <table class="min-w-full text-sm text-left text-gray-700">
            <thead class="bg-gray-100 text-gray-800 font-semibold">
                <tr>
                    <th class="px-4 py-3">Nama Paket</th>
                    <th class="px-4 py-3">Durasi</th>
                    <th class="px-4 py-3">Batas Surat</th>
                    <th class="px-4 py-3">Batas Pasien</th>
                    <th class="px-4 py-3">Harga</th>
                    <th class="px-4 py-3 text-center">Status</th>
                    <th class="px-4 py-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($packages as $package)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium">{{ $package->name }}</td>
                        <td class="px-4 py-3">{{ $package->duration_in_days }} hari</td>
                        <td class="px-4 py-3">{{ $package->max_letters ?? '∞' }}</td>
                        <td class="px-4 py-3">{{ $package->max_patients ?? '∞' }}</td>
                        <td class="px-4 py-3">Rp {{ number_format($package->price, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-center">
                            @if($package->is_active)
                                <span class="px-2 py-1 bg-green-100 text-green-700 rounded text-xs font-medium">Aktif</span>
                            @else
                                <span class="px-2 py-1 bg-red-100 text-red-700 rounded text-xs font-medium">Nonaktif</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex flex-col items-center gap-2 w-24">
                                <a href="{{ route('packages.edit', $package) }}"
                                   class="w-full text-center px-3 py-1.5 text-sm bg-yellow-100 text-yellow-800 rounded hover:bg-yellow-200 transition">
                                    ✏️ Edit
                                </a>

                                <form action="{{ route('packages.destroy', $package) }}" method="POST" class="w-full">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            onclick="return confirm('Yakin ingin menghapus paket ini?')"
                                            class="w-full px-3 py-1.5 text-sm bg-red-100 text-red-700 rounded hover:bg-red-200 transition">
                                        🗑️ Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-4 text-center text-gray-500">Belum ada data paket.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="mt-6">
        {{ $packages->links() }}
    </div>
</div>
@endsection
