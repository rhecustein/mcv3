@extends('layouts.app', ['header' => 'Manajemen Admin'])

@section('content')
<div class="space-y-6">

    <!-- Header & Tombol Aksi -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-white">Manajemen Admin</h1>
            <p class="mt-1 text-sm text-slate-400">Kelola semua akun administrator regional di sistem.</p>
        </div>
        <a href="{{ route('admins.create') }}"
           class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-gradient-to-r from-blue-600 to-cyan-500 text-white font-semibold rounded-lg shadow-lg shadow-cyan-500/20 transition-transform duration-200 hover:scale-105">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m6-6H6" /></svg>
            <span>Tambah Admin</span>
        </a>
    </div>

    <!-- Alert Sukses -->
    @if(session('success'))
        <div class="bg-green-500/10 border border-green-500/30 text-green-300 text-sm rounded-lg p-4 flex items-center gap-3" role="alert">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Panel Filter & Search -->
    <div class="bg-slate-800/50 border border-slate-700/80 rounded-xl p-4">
        <form method="GET" class="flex flex-col md:flex-row gap-4 items-center">
            <div class="relative w-full md:w-1/3">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                    <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" /></svg>
                </div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau email..."
                       class="block w-full rounded-md border-0 bg-white/5 py-2.5 pl-10 text-white ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-inset focus:ring-cyan-500 sm:text-sm">
            </div>
            
            <div class="relative w-full md:w-auto">
                 <select name="province" class="w-full appearance-none rounded-md border-0 bg-white/5 py-2.5 pl-3 pr-10 text-white ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-inset focus:ring-cyan-500 sm:text-sm">
                    <option value="">Semua Provinsi</option>
                    @foreach($provinces as $prov)
                        <option value="{{ $prov }}" {{ request('province') == $prov ? 'selected' : '' }}>{{ $prov }}</option>
                    @endforeach
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2"><svg class="h-4 w-4 text-slate-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" /></svg></div>
            </div>

            <button type="submit" class="w-full md:w-auto px-4 py-2.5 bg-slate-700 text-white rounded-md text-sm font-semibold hover:bg-slate-600 transition">Filter</button>
            <a href="{{ route('admins.index') }}" class="w-full md:w-auto text-center px-4 py-2.5 text-slate-300 rounded-md text-sm hover:bg-slate-700 transition">Reset</a>
        </form>
    </div>

    <!-- Tabel Data Admin -->
    <div class="bg-slate-800/50 border border-slate-700/80 rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-900/50 text-xs uppercase text-slate-400 tracking-wider">
                    <tr>
                        <th class="px-6 py-3 font-medium">Pengguna</th>
                        <th class="px-6 py-3 font-medium">Wilayah</th>
                        <th class="px-6 py-3 font-medium">Status</th>
                        <th class="px-6 py-3 font-medium text-center">Kelolaan</th>
                        <th class="px-6 py-3 font-medium text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800">
                    @forelse ($admins as $admin)
                        <tr class="hover:bg-slate-800">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <img class="h-10 w-10 rounded-full" src="https://ui-avatars.com/api/?name={{ urlencode($admin->user->name) }}&background=1e293b&color=94a3b8" alt="">
                                    <div>
                                        <div class="font-semibold text-white">{{ $admin->user->name }}</div>
                                        <div class="text-slate-400">{{ $admin->user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-white">{{ $admin->region_name }}</div>
                                <div class="text-slate-400">{{ $admin->province }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($admin->user->is_active)
                                    <span class="inline-flex items-center gap-1.5 px-2 py-1 text-xs font-medium text-green-400 bg-green-500/10 rounded-full">
                                        <span class="h-1.5 w-1.5 rounded-full bg-green-400"></span> Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2 py-1 text-xs font-medium text-red-400 bg-red-500/10 rounded-full">
                                         <span class="h-1.5 w-1.5 rounded-full bg-red-400"></span> Nonaktif
                                    </span>
                                @endif
                            </td>
                             <td class="px-6 py-4 whitespace-nowrap text-slate-300 text-center">
                                {{ $admin->outlet_count }} <span class="text-slate-400">Outlet</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <div x-data="{ open: false }" class="relative inline-block text-left">
                                    <button @click="open = !open" class="p-2 text-slate-400 hover:text-white rounded-full hover:bg-slate-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-slate-800 focus:ring-cyan-500">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" /></svg>
                                    </button>
                                    <div x-show="open" @click.away="open = false" 
                                         x-transition
                                         class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-slate-700 ring-1 ring-black ring-opacity-5 z-10">
                                        <div class="py-1" role="menu" aria-orientation="vertical">
                                            <a href="{{ route('admins.edit', $admin) }}" class="flex items-center gap-3 px-4 py-2 text-sm text-slate-300 hover:bg-slate-600" role="menuitem">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125" /></svg>
                                                <span>Edit</span>
                                            </a>
                                            <!-- Ban/Unban logic -->
                                            @if($admin->user->is_active)
                                                <form action="{{ route('admins.ban', $admin->user_id) }}" method="POST" onsubmit="return confirm('Yakin ingin menonaktifkan user ini?')" class="w-full">
                                                    @csrf
                                                    <button type="submit" class="w-full flex items-center gap-3 px-4 py-2 text-sm text-yellow-400 hover:bg-slate-600" role="menuitem">
                                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" /></svg>
                                                        <span>Nonaktifkan</span>
                                                    </button>
                                                </form>
                                            @else
                                                <form action="{{ route('admins.unban', $admin->user_id) }}" method="POST" onsubmit="return confirm('Aktifkan kembali user ini?')" class="w-full">
                                                    @csrf
                                                    <button type="submit" class="w-full flex items-center gap-3 px-4 py-2 text-sm text-green-400 hover:bg-slate-600" role="menuitem">
                                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                                        <span>Aktifkan</span>
                                                    </button>
                                                </form>
                                            @endif
                                            <div class="border-t border-slate-600 my-1"></div>
                                            <form action="{{ route('admins.destroy', $admin) }}" method="POST" onsubmit="return confirm('PERINGATAN: Tindakan ini tidak dapat diurungkan. Yakin ingin menghapus permanen admin ini?')" class="w-full">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="w-full flex items-center gap-3 px-4 py-2 text-sm text-red-400 hover:bg-slate-600" role="menuitem">
                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" /></svg>
                                                    <span>Hapus Permanen</span>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-slate-400">
                                <svg class="w-12 h-12 mx-auto text-slate-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" /></svg>
                                <p class="mt-4 font-semibold">Data Tidak Ditemukan</p>
                                <p class="text-sm">Coba ubah kata kunci pencarian atau reset filter.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Pagination -->
    @if ($admins->hasPages())
        <div class="mt-6">
            {{ $admins->appends(request()->query())->links() }}
        </div>
    @endif
</div>
@endsection