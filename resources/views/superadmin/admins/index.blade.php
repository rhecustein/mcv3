@extends('layouts.app', ['header' => 'Manajemen Admin'])

@section('content')
{{-- [IMPROVEMENT] Container utama dengan background dan padding solid putih --}}
<div class="space-y-8 bg-white p-6 sm:p-8 rounded-2xl shadow-lg">

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 pb-6 border-b border-slate-200">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-slate-900">Manajemen Admin</h1>
            <p class="mt-2 text-slate-500">Kelola semua akun administrator regional di sistem.</p>
        </div>
        <a href="{{ route('admins.create') }}"
           class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-blue-600 text-white font-semibold rounded-lg shadow-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors whitespace-nowrap">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m6-6H6" /></svg>
            <span>Tambah Admin</span>
        </a>
    </div>

    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" x-transition
             class="bg-green-100 border border-green-300 text-green-800 text-sm rounded-lg p-4 flex items-center justify-between gap-3" role="alert">
            <div class="flex items-center gap-3">
                <svg class="w-5 h-5 flex-shrink-0 text-green-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
            <button @click="show = false" class="p-1 text-green-800/70 hover:text-green-900 rounded-full">&times;</button>
        </div>
    @endif

    <div class="bg-slate-50 rounded-xl p-4 border border-slate-200">
        <form method="GET" class="flex flex-col md:flex-row gap-4 items-center">
            <div class="relative w-full md:flex-1">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5">
                    <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" /></svg>
                </div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, email, atau wilayah..."
                       class="block w-full rounded-md border-slate-300 py-2.5 pl-11 text-slate-800 shadow-sm focus:border-cyan-500 focus:ring-cyan-500 sm:text-sm transition">
            </div>

            <div class="w-full md:w-auto flex items-center gap-4">
                <select name="province" onchange="this.form.submit()" class="w-full flex-1 appearance-none rounded-md border-slate-300 py-2.5 pl-3 pr-10 text-slate-800 shadow-sm focus:border-cyan-500 focus:ring-cyan-500 sm:text-sm transition">
                    <option value="">Semua Provinsi</option>
                    @foreach($provinces as $prov)
                        <option value="{{ $prov }}" {{ request('province') == $prov ? 'selected' : '' }}>{{ $prov }}</option>
                    @endforeach
                </select>
                <a href="{{ route('admins.index') }}" title="Reset Filter" class="px-4 py-2.5 text-slate-600 rounded-md text-sm hover:bg-slate-200 transition border border-slate-300">Reset</a>
            </div>
        </form>
    </div>

    <div class="text-sm text-slate-500">
        Menampilkan <span class="font-semibold text-slate-900">{{ $admins->firstItem() ?? 0 }}</span> - <span class="font-semibold text-slate-900">{{ $admins->lastItem() ?? 0 }}</span> dari <span class="font-semibold text-slate-900">{{ $admins->total() }}</span> total admin.
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 2xl:grid-cols-3 gap-6">
        @forelse ($admins as $admin)
            <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm flex flex-col justify-between transition-all duration-300 hover:shadow-xl hover:border-slate-300">
                <div>
                    <div class="flex justify-between items-start gap-4">
                        <div class="flex items-center gap-4">
                            <img class="h-14 w-14 rounded-full ring-2 ring-slate-200" src="https://ui-avatars.com/api/?name={{ urlencode($admin->user->name ?? 'A') }}&background=f1f5f9&color=475569&size=128" alt="Avatar">
                            <div>
                                <h3 class="font-bold text-lg text-slate-900">{{ $admin->user->name ?? 'Nama Tidak Tersedia' }}</h3>
                                <p class="text-sm text-cyan-600">{{ $admin->user->email ?? '-' }}</p>
                            </div>
                        </div>
                         <div x-data="{ open: false }" class="relative flex-shrink-0">
                            <button @click="open = !open" class="p-2 text-slate-500 hover:text-slate-800 rounded-full hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:ring-offset-2">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" /></svg>
                            </button>
                            <div x-show="open" @click.away="open = false" x-transition class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-20">
                                <div class="py-1" role="menu" aria-orientation="vertical">
                                    <a href="{{ route('admins.edit', $admin) }}" class="flex items-center gap-3 px-4 py-2 text-sm text-slate-700 hover:bg-slate-100" role="menuitem">
                                        <svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125" /></svg>
                                        <span>Edit</span>
                                    </a>
                                    @if($admin->user->is_active)
                                        <form action="{{ route('admins.ban', $admin->user_id) }}" method="POST" onsubmit="return confirm('Yakin ingin menonaktifkan user ini?')" class="w-full">
                                            @csrf
                                            <button type="submit" class="w-full text-left flex items-center gap-3 px-4 py-2 text-sm text-yellow-600 hover:bg-slate-100" role="menuitem">
                                                <svg class="w-4 h-4 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" /></svg>
                                                <span>Nonaktifkan</span>
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ route('admins.unban', $admin->user_id) }}" method="POST" onsubmit="return confirm('Aktifkan kembali user ini?')" class="w-full">
                                            @csrf
                                            <button type="submit" class="w-full text-left flex items-center gap-3 px-4 py-2 text-sm text-green-600 hover:bg-slate-100" role="menuitem">
                                                <svg class="w-4 h-4 text-green-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                                <span>Aktifkan</span>
                                            </button>
                                        </form>
                                    @endif
                                    <div class="border-t border-slate-200 my-1"></div>
                                    <form action="{{ route('admins.destroy', $admin) }}" method="POST" onsubmit="return confirm('PERINGATAN: Tindakan ini tidak dapat diurungkan. Yakin ingin menghapus permanen admin ini?')" class="w-full">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-full text-left flex items-center gap-3 px-4 py-2 text-sm text-red-600 hover:bg-slate-100" role="menuitem">
                                            <svg class="w-4 h-4 text-red-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" /></svg>
                                            <span>Hapus Permanen</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 border-t border-slate-200 pt-4 space-y-3 text-sm">
                        <div class="flex justify-between items-center">
                            <span class="text-slate-500">Wilayah Tanggung Jawab:</span>
                            <span class="font-semibold text-slate-800">{{ $admin->region_name ?? '-' }}, {{ $admin->province ?? '-' }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-slate-500">Jumlah Outlet Dikelola:</span>
                            <span class="font-semibold text-slate-800">{{ $admin->outlet_count ?? 0 }} Outlet</span>
                        </div>
                    </div>
                </div>

                <div class="mt-6 pt-4 border-t border-slate-200">
                    @if($admin->user->is_active)
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-medium text-green-800 bg-green-100 rounded-full">
                            <span class="h-2 w-2 rounded-full bg-green-500"></span> Akun Aktif
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-medium text-red-800 bg-red-100 rounded-full">
                            <span class="h-2 w-2 rounded-full bg-red-500"></span> Nonaktif
                        </span>
                    @endif
                </div>
            </div>
        @empty
            <div class="lg:col-span-2 2xl:col-span-3 text-center py-20 bg-slate-50 rounded-2xl border-2 border-dashed border-slate-200">
                <svg class="w-16 h-16 mx-auto text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 15.75l-2.489-2.489m0 0a3.375 3.375 0 10-4.773-4.773 3.375 3.375 0 004.774 4.774zM21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                <h3 class="mt-4 text-xl font-semibold text-slate-800">Data Admin Tidak Ditemukan</h3>
                <p class="mt-1 text-slate-500">Coba ubah kata kunci pencarian atau reset filter untuk melihat semua data.</p>
            </div>
        @endforelse
    </div>
    
    @if ($admins->hasPages())
        <div class="pt-6">
            {{ $admins->appends(request()->query())->links() }}
        </div>
    @endif
</div>
@endsection