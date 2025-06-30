@extends('layouts.app')

@section('content')
<div class="p-6 space-y-6">

    <div class="flex justify-between items-center">
        <h2 class="text-2xl font-bold">🗑️ Riwayat Surat Terhapus</h2>
        <a href="{{ route('outlet.healthletter.index') }}"
           class="bg-gray-200 hover:bg-gray-300 text-sm px-4 py-2 rounded shadow">
            ← Kembali
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 text-green-800 px-4 py-2 rounded">
            {{ session('success') }}
        </div>
    @elseif(session('error'))
        <div class="bg-red-100 text-red-800 px-4 py-2 rounded">
            {{ session('error') }}
        </div>
    @endif

    {{-- Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-center text-sm font-semibold">
        <div class="bg-gray-800 text-white p-4 rounded shadow">
            Total Terhapus
            <div class="text-2xl mt-1">{{ $trashed->total() }}</div>
        </div>
        <div class="bg-blue-500 text-white p-4 rounded shadow">
            User Unik
            <div class="text-2xl mt-1">
                {{ $trashed->unique('deleted_by')->count() }}
            </div>
        </div>
        <div class="bg-indigo-600 text-white p-4 rounded shadow">
            Outlet Unik
            <div class="text-2xl mt-1">
                {{ $trashed->unique('deleted_outlet_id')->count() }}
            </div>
        </div>
    </div>

    <div class="overflow-x-auto bg-white border rounded shadow">
        <table class="min-w-full text-sm text-left">
            <thead class="bg-gray-100 text-xs uppercase font-semibold border-b">
                <tr>
                    <th class="px-4 py-2">ID</th>
                    <th class="px-4 py-2">Waktu Hapus</th>
                    <th class="px-4 py-2">User</th>
                    <th class="px-4 py-2">IP</th>
                    <th class="px-4 py-2">Lokasi</th>
                    <th class="px-4 py-2">Outlet</th>
                    <th class="px-4 py-2">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse ($trashed as $trash)
                    <tr>
                        <td class="px-4 py-2">{{ $trash->id }}</td>
                        <td class="px-4 py-2 text-xs">{{ \Carbon\Carbon::parse($trash->deleted_at)->format('d M Y H:i') }}</td>
                        <td class="px-4 py-2">{{ $trash->user->name ?? '—' }}</td>
                        <td class="px-4 py-2 text-xs">{{ $trash->deleted_ip }}</td>
                        <td class="px-4 py-2 text-xs">{{ $trash->deleted_location }}</td>
                        <td class="px-4 py-2">{{ $trash->outlet->name ?? '-' }}</td>
                        <td class="px-4 py-2">
                            <form method="POST" action="{{ route('outlet.result.trash.restore', $trash->id) }}">
                                @csrf
                                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-xs px-3 py-1 rounded">
                                    🔄 Restore
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center px-4 py-6 text-gray-500">
                            Tidak ada data terhapus.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $trashed->links('vendor.pagination.tailwind') }}
    </div>

</div>
@endsection
