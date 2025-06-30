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
        <h1 class="text-2xl font-bold text-gray-800">🧑‍💼 Manajemen Admin</h1>
        <a href="{{ route('admins.create') }}"
           class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg shadow hover:bg-blue-700 transition">
            ➕ Tambah Admin
        </a>
    </div>

    {{-- Card Count --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm text-gray-500">Total Admin</div>
            <div class="text-xl font-bold text-blue-600">{{ $totalAdmins }}</div>
        </div>
    </div>

    {{-- Filter & Search --}}
    <form method="GET" class="bg-white p-4 rounded-lg shadow mb-6 flex flex-wrap gap-4 items-center">
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Cari nama atau email..."
               class="w-full md:w-1/3 px-4 py-2 border border-gray-300 rounded-md focus:ring focus:ring-blue-100 text-sm">

        <select name="province"
                class="w-full md:w-1/4 px-4 py-2 border border-gray-300 rounded-md text-sm">
            <option value="">-- Semua Provinsi --</option>
            @foreach($provinces as $prov)
                <option value="{{ $prov }}" {{ request('province') == $prov ? 'selected' : '' }}>
                    {{ $prov }}
                </option>
            @endforeach
        </select>

        <button type="submit"
                class="px-4 py-2 bg-blue-600 text-white rounded-md text-sm hover:bg-blue-700 transition">
            🔍 Filter
        </button>

        <a href="{{ route('admins.index') }}"
           class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md text-sm hover:bg-gray-200 transition">
            🔄 Reset
        </a>
    </form>

    {{-- Tabel --}}
    <div class="bg-white shadow rounded-lg overflow-x-auto">
        <table class="min-w-full text-sm text-left text-gray-600">
            <thead class="bg-gray-100 text-gray-700 font-semibold">
                <tr>
                    <th class="px-4 py-3">Nama</th>
                    <th class="px-4 py-3">Email</th>
                    <th class="px-4 py-3">No. HP</th>
                    <th class="px-4 py-3">Wilayah</th>
                    <th class="px-4 py-3">Jabatan</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3 text-center">Jumlah Data</th>
                    <th class="px-4 py-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($admins as $admin)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-4 py-3">{{ $admin->user->name }}</td>
                        <td class="px-4 py-3">{{ $admin->user->email }}</td>
                        <td class="px-4 py-3">{{ $admin->user->phone ?? '-' }}</td>
                        <td class="px-4 py-3">{{ $admin->region_name }}<br><span class="text-xs text-gray-400">{{ $admin->province }}</span></td>
                        <td class="px-4 py-3">{{ $admin->position_title ?? '-' }}</td>
                        <td class="px-4 py-3">
                            @if($admin->user->is_active)
                                <span class="px-2 py-1 text-xs font-semibold text-green-700 bg-green-100 rounded">Aktif</span>
                            @else
                                <span class="px-2 py-1 text-xs font-semibold text-red-700 bg-red-100 rounded">Nonaktif</span>
                            @endif
                        </td>
                       <td class="px-4 py-3 text-center align-top">
                            <div class="grid grid-cols-2 gap-2 text-xs text-gray-700 justify-center">
                                <div class="bg-gray-50 rounded-md px-2 py-1 shadow-sm">
                                    👤<br><strong>{{ $admin->user_count }}</strong><br><span class="text-[10px]">User</span>
                                </div>
                                <div class="bg-gray-50 rounded-md px-2 py-1 shadow-sm">
                                    🏥<br><strong>{{ $admin->outlet_count }}</strong><br><span class="text-[10px]">Outlet</span>
                                </div>
                                <div class="bg-gray-50 rounded-md px-2 py-1 shadow-sm">
                                    🧑‍⚕️<br><strong>{{ $admin->doctor_count }}</strong><br><span class="text-[10px]">Dokter</span>
                                </div>
                                <div class="bg-gray-50 rounded-md px-2 py-1 shadow-sm">
                                    📄<br><strong>{{ $admin->letter_count }}</strong><br><span class="text-[10px]">Surat</span>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex flex-col space-y-2">
                                <a href="{{ route('admins.edit', $admin) }}"
                                   class="w-full px-3 py-1 text-sm bg-yellow-100 text-yellow-800 rounded hover:bg-yellow-200 transition text-center">✏️ Edit</a>

                                <form action="{{ route('admins.destroy', $admin) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            onclick="return confirm('Yakin ingin menghapus admin ini?')"
                                            class="w-full px-3 py-1 text-sm bg-red-100 text-red-700 rounded hover:bg-red-200 transition">
                                        🗑️ Hapus
                                    </button>
                                </form>

                                @if($admin->user->is_active)
                                    <form action="{{ route('admins.ban', $admin->user_id) }}" method="POST">
                                        @csrf
                                        <button type="submit"
                                                onclick="return confirm('Yakin ingin menonaktifkan user ini?')"
                                                class="w-full px-3 py-1 text-sm bg-gray-100 text-gray-800 rounded hover:bg-gray-200 transition">
                                            🚫 Ban
                                        </button>
                                    </form>
                                @else
                                    <form action="{{ route('admins.unban', $admin->user_id) }}" method="POST">
                                        @csrf
                                        <button type="submit"
                                                onclick="return confirm('Aktifkan kembali user ini?')"
                                                class="w-full px-3 py-1 text-sm bg-green-100 text-green-800 rounded hover:bg-green-200 transition">
                                            ✅ Unban
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-4 py-4 text-center text-gray-500">Belum ada admin.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="mt-6">
        {{ $admins->appends(request()->query())->links() }}
    </div>

</div>
@endsection
