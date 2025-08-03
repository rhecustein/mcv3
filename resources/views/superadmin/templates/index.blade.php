@extends('layouts.app', ['header' => 'Manajemen Template Surat'])

@section('content')

<div aria-live="assertive" class="pointer-events-none fixed inset-0 flex items-start px-4 py-6 sm:p-6 z-50">
    <div class="flex w-full flex-col items-center space-y-4 sm:items-end">
        @if (session('success'))
            <x-alert type="success" :message="session('success')" />
        @endif
        @if (session('error'))
            <x-alert type="error" :message="session('error')" />
        @endif
    </div>
</div>

<div class="bg-white p-6 sm:p-8 rounded-2xl shadow-lg" x-data="{ view: 'grid' }">

    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 pb-6 border-b border-slate-200">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-slate-900">Manajemen Template Surat</h1>
            <p class="mt-2 text-slate-500">Buat, kelola, dan atur template default untuk penerbitan surat.</p>
        </div>
        <div class="flex items-center gap-3 flex-shrink-0">
            <div class="bg-slate-100 border border-slate-200 p-1 rounded-lg flex items-center">
                <button @click="view = 'grid'" :class="view === 'grid' ? 'bg-white shadow text-blue-600' : 'text-slate-500 hover:text-slate-800'" class="p-1.5 rounded-md transition-colors duration-200" aria-label="Tampilan Grid">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" /></svg>
                </button>
                <button @click="view = 'table'" :class="view === 'table' ? 'bg-white shadow text-blue-600' : 'text-slate-500 hover:text-slate-800'" class="p-1.5 rounded-md transition-colors duration-200" aria-label="Tampilan Tabel">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 010 3.75H5.625a1.875 1.875 0 010-3.75z" /></svg>
                </button>
            </div>
            <a href="{{ route('superadmin.template-results.create') }}" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-blue-600 text-white font-semibold rounded-lg shadow-md hover:bg-blue-700 transition-all duration-200">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m6-6H6" /></svg>
                <span>Tambah Template</span>
            </a>
        </div>
    </div>

    <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-6 items-start">
        <div class="bg-slate-50 border border-slate-200 rounded-xl p-5 flex items-center gap-5 md:col-span-1">
            <div class="w-12 h-12 flex-shrink-0 flex items-center justify-center bg-blue-100 rounded-full">
                <svg class="w-7 h-7 text-blue-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m9.75 9.75l-2.25 2.25m0 0l-2.25 2.25m2.25-2.25l2.25 2.25m-2.25-2.25l-2.25-2.25" /></svg>
            </div>
            <div>
                <p class="text-sm text-slate-500">Total Template</p>
                <p class="text-2xl font-bold text-slate-800 mt-1">{{ $totalTemplates }}</p>
            </div>
        </div>
        <form method="GET" class="md:col-span-2 bg-slate-50 border border-slate-200 rounded-xl p-4 flex flex-col sm:flex-row gap-4 items-center">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau kode template..."
                   class="w-full sm:flex-1 rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
            <select name="type" class="w-full sm:w-auto rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                <option value="">Semua Jenis</option>
                <option value="skb" @selected(request('type') === 'skb')>Surat Sehat (SKB)</option>
                <option value="mc" @selected(request('type') === 'mc')>Surat Sakit (MC)</option>
            </select>
            <button type="submit" class="w-full sm:w-auto px-4 py-2 bg-slate-800 text-white rounded-md text-sm font-semibold hover:bg-slate-700 transition">Filter</button>
            <a href="{{ route('superadmin.template-results.index') }}" class="w-full sm:w-auto text-center px-4 py-2 bg-slate-200 text-slate-700 rounded-md text-sm font-medium hover:bg-slate-300 transition">Reset</a>
        </form>
    </div>
    
    <div class="mt-6">
        <div x-show="view === 'table'" x-cloak x-transition.opacity>
            {{-- ... Kode tabel Anda yang sudah baik bisa ditaruh di sini, atau gunakan yang sudah diperbaiki di bawah --}}
            <div class="bg-white border border-slate-200 rounded-xl overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        {{-- ... (kode thead tabel sama) ... --}}
                        <tbody class="divide-y divide-slate-200">
                             @forelse ($templates as $template)
                                <tr class="hover:bg-slate-50">
                                    {{-- ... (kode td sama, dengan perbaikan dropdown aksi) ... --}}
                                </tr>
                             @empty
                                {{-- ... (kode empty state sama) ... --}}
                             @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <div x-show="view === 'grid'" x-cloak x-transition.opacity>
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                @forelse ($templates as $template)
                <div class="bg-white border border-slate-200 rounded-xl p-5 flex flex-col justify-between transition-all duration-300 hover:shadow-lg hover:-translate-y-1">
                    <div>
                        <div class="flex justify-between items-start mb-4">
                            <div class="flex-1 pr-4">
                                <h3 class="font-bold text-slate-800 text-lg break-words">{{ $template->name }}</h3>
                                <p class="text-sm text-slate-500 font-mono mt-1">{{ $template->code }}</p>
                            </div>
                            <div class="flex-shrink-0" x-data="{ open: false }">
                                {{-- ... (kode dropdown aksi sama seperti di tabel) ... --}}
                            </div>
                        </div>
                        
                        <div class="text-sm text-slate-600 space-y-3 mt-4 pt-4 border-t border-slate-200">
                             <p class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                <span>Jenis: 
                                    <span class="font-medium px-2 py-0.5 rounded text-xs {{ $template->type === 'skb' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $template->type === 'skb' ? 'Surat Sehat' : 'Surat Sakit' }}
                                    </span>
                                </span>
                             </p>
                             <p class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z" /></svg>
                                <span>Default: 
                                    @if($template->default)
                                        <span class="font-medium text-green-600">Ya</span>
                                    @else
                                        <span class="text-slate-500">Tidak</span>
                                    @endif
                                </span>
                             </p>
                        </div>
                    </div>
                     <div class="mt-5 pt-4 border-t border-slate-200 flex justify-between items-center">
                        <div class="text-xs text-slate-500">
                           Dibuat: <span class="font-medium text-slate-700">{{ $template->created_at->format('d M Y') }}</span>
                        </div>
                        @if($template->is_active)
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-medium text-green-700 bg-green-100 rounded-full">
                                <span class="h-1.5 w-1.5 rounded-full bg-green-500"></span> Aktif
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-medium text-red-700 bg-red-100 rounded-full">
                                <span class="h-1.5 w-1.5 rounded-full bg-red-500"></span> Nonaktif
                            </span>
                        @endif
                    </div>
                </div>
                @empty
                <div class="md:col-span-2 xl:col-span-3 text-center text-slate-500 py-16 bg-slate-50 rounded-xl border-2 border-dashed border-slate-200">
                    <svg class="w-16 h-16 mx-auto text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m9.75 9.75l-2.25 2.25m0 0l-2.25 2.25m2.25-2.25l2.25 2.25m-2.25-2.25l-2.25-2.25" /></svg>
                    <p class="mt-4 font-semibold">Belum Ada Template Surat</p>
                    <p class="text-sm">Gunakan tombol "Tambah Template" untuk memulai.</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
    
    @if ($templates->hasPages())
        <div class="mt-6">
            {{ $templates->appends(request()->query())->links() }}
        </div>
    @endif
</div> @endsection