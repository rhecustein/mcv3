@extends('layouts.app')

@section('content')
<div class="max-w-screen-xl mx-auto">
    @if(session('success'))
    <div class="mb-4 px-4 py-3 bg-green-100 text-green-800 border border-green-300 rounded-md text-sm shadow-sm">
        ✅ {{ session('success') }}
    </div>
    @endif

    {{-- Header --}}
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">🏥 Manajemen Outlet</h1>
        <a href="{{ route('outlets.create') }}"
           class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg shadow hover:bg-blue-700 transition">
            ➕ Tambah Outlet
        </a>
    </div>

    {{-- Count Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm text-gray-500">Total Outlet</div>
            <div class="text-2xl font-bold text-blue-600">{{ $totalOutlets }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm text-gray-500">Outlet Diblokir</div>
            <div class="text-2xl font-bold text-red-600">{{ $bannedOutlets }}</div>
        </div>
    </div>

    {{-- Filter & Search --}}
    <form method="GET" class="bg-white p-4 rounded-lg shadow mb-6 flex flex-wrap gap-4 items-center">
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Cari nama, kota, atau email..."
               class="w-full md:w-1/3 px-4 py-2 border border-gray-300 rounded-md text-sm">

        <select name="is_active"
                class="w-full md:w-1/4 px-4 py-2 border border-gray-300 rounded-md text-sm">
            <option value="">-- Status Aktif --</option>
            <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>Aktif</option>
            <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>Nonaktif</option>
        </select>

        <button type="submit"
                class="px-4 py-2 bg-blue-600 text-white rounded-md text-sm hover:bg-blue-700 transition">
            🔍 Filter
        </button>

        <a href="{{ route('outlets.index') }}"
           class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md text-sm hover:bg-gray-200 transition">
            🔄 Reset
        </a>
    </form>

    {{-- Tabel --}}
    <div class="bg-white shadow rounded-lg overflow-x-auto">
        <table class="min-w-full text-sm text-left text-gray-600">
            <thead class="bg-gray-100 text-gray-700 font-semibold">
                <tr>
                    <th class="px-4 py-3">Nama Outlet</th>
                    <th class="px-4 py-3">Lokasi</th>
                    <th class="px-4 py-3">Kontak</th>
                    <th class="px-4 py-3">Admin</th>
                    <th class="px-4 py-3 text-center">Status</th>
                    <th class="px-4 py-3 text-center">Total</th>
                    <th class="px-4 py-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($outlets as $outlet)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium">
                            {{ $outlet->name }}
                            <div class="text-xs text-gray-400">{{ $outlet->code ?? '-' }}</div>
                        </td>
                        <td class="px-4 py-3">
                            {{ $outlet->city ?? '-' }}<br>
                            <span class="text-xs text-gray-400">{{ $outlet->province ?? '-' }}</span>
                        </td>
                        <td class="px-4 py-3">
                            📞 {{ $outlet->phone ?? '-' }}<br>
                            📧 {{ $outlet->email ?? '-' }}
                        </td>
                        <td class="px-4 py-3">
                            {{ $outlet->admin->user->name ?? '-' }}
                            <div class="text-xs text-gray-400">{{ $outlet->admin->user->email ?? '' }}</div>
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($outlet->is_active)
                                <span class="px-2 py-1 bg-green-100 text-green-700 rounded text-xs font-medium">Aktif</span>
                            @else
                                <span class="px-2 py-1 bg-red-100 text-red-700 rounded text-xs font-medium">Nonaktif</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center align-top">
                            <div class="grid grid-cols-2 gap-2 justify-center text-xs text-gray-700">
                                <div class="bg-gray-50 rounded-md px-2 py-1 shadow-sm w-24 text-center">
                                    🧑‍⚕️<br><strong>{{ $outlet->doctor_count }}</strong><br>
                                    <span class="text-[10px]">Dokter</span>
                                </div>
                                <div class="bg-gray-50 rounded-md px-2 py-1 shadow-sm w-24 text-center">
                                    📄<br><strong>{{ $outlet->letter_count }}</strong><br>
                                    <span class="text-[10px]">Surat</span>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-center align-top">
                            <div class="flex flex-col gap-2 items-center w-28">
                                <a href="{{ route('outlets.edit', $outlet) }}"
                                   class="w-full text-center px-3 py-1.5 text-sm bg-yellow-100 text-yellow-800 rounded hover:bg-yellow-200 transition">
                                    ✏️ Edit
                                </a>

                                <form action="{{ route('outlets.destroy', $outlet) }}" method="POST" class="w-full">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            onclick="return confirm('Yakin ingin menghapus outlet ini?')"
                                            class="w-full px-3 py-1.5 text-sm bg-red-100 text-red-700 rounded hover:bg-red-200 transition">
                                        🗑️ Hapus
                                    </button>
                                </form>

                                <form action="{{ route('outlets.toggle', $outlet) }}" method="POST" class="w-full">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                            class="w-full px-3 py-1.5 text-sm rounded transition
                                                {{ $outlet->is_active ? 'bg-red-100 text-red-700 hover:bg-red-200' : 'bg-green-100 text-green-700 hover:bg-green-200' }}">
                                        {{ $outlet->is_active ? '🚫 Ban' : '✅ Unban' }}
                                    </button>
                                </form>

                                <form action="{{ route('outlets.reset-password', $outlet) }}" method="POST" class="w-full mt-1">
                                    @csrf
                                    <button type="submit"
                                            onclick="return confirm('Reset password akun user outlet ini ke default?')"
                                            class="w-full px-3 py-1.5 text-sm bg-indigo-100 text-indigo-700 rounded hover:bg-indigo-200 transition">
                                        🔒 Reset Password
                                    </button>
                                    <span class="text-xs text-gray-400 block mt-1 text-center">Default: <code>default123</code></span>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-4 text-center text-gray-500">Belum ada data outlet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="mt-6">
        {{ $outlets->appends(request()->query())->links() }}
    </div>
</div>
@endsection
