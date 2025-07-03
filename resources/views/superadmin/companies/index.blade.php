@extends('layouts.app', ['header' => 'Manajemen Perusahaan Klien'])

@section('content')
<div class="space-y-6">

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

    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Manajemen Perusahaan</h1>
            <p class="mt-1 text-sm text-slate-500">Kelola perusahaan klien, paket langganan, dan status kerja sama.</p>
        </div>
        <a href="{{ route('companies.create') }}"
           class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-blue-600 text-white font-semibold rounded-lg shadow-md hover:bg-blue-700 transition-all duration-200">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m6-6H6" /></svg>
            <span>Tambah Perusahaan</span>
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        @php
            $statCards = [
                ['label' => 'Total Perusahaan', 'value' => number_format($totalCompanies), 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h6M9 11.25h6M9 15.75h6M9 20.25h6" />', 'color' => 'indigo'],
                ['label' => 'Klien Aktif', 'value' => number_format($activeCompanies), 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />', 'color' => 'green'],
                ['label' => 'Langganan Berakhir', 'value' => number_format($expiredPackages), 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />', 'color' => 'amber'],
                ['label' => 'Perusahaan Nonaktif', 'value' => number_format($totalCompanies - $activeCompanies), 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />', 'color' => 'red'],
            ];
        @endphp
        @foreach ($statCards as $card)
        <div class="bg-white border border-slate-200 rounded-xl p-5 flex items-center gap-5">
             <div class="w-12 h-12 flex-shrink-0 flex items-center justify-center bg-{{$card['color']}}-100 rounded-lg">
                <svg class="w-7 h-7 text-{{$card['color']}}-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">{!! $card['icon'] !!}</svg>
            </div>
            <div>
                <p class="text-sm text-slate-500">{{ $card['label'] }}</p>
                <p class="text-2xl font-bold text-slate-800 mt-1">{{ $card['value'] }}</p>
            </div>
        </div>
        @endforeach
    </div>

    <div class="bg-white border border-slate-200 rounded-xl p-4">
        <form method="GET" class="flex flex-col md:flex-row gap-4 items-center">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, email, atau industri..."
                   class="w-full md:flex-1 rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
            <select name="is_active" class="w-full md:w-auto rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                <option value="">Semua Status</option>
                <option value="1" @selected(request('is_active') === '1')>Aktif</option>
                <option value="0" @selected(request('is_active') === '0')>Nonaktif</option>
            </select>
            <button type="submit" class="w-full md:w-auto px-4 py-2 bg-slate-800 text-white rounded-md text-sm font-semibold hover:bg-slate-700 transition">Filter</button>
            <a href="{{ route('companies.index') }}" class="w-full md:w-auto text-center px-4 py-2 bg-slate-200 text-slate-700 rounded-md text-sm font-medium hover:bg-slate-300 transition">Reset</a>
        </form>
    </div>

    <div class="bg-white border border-slate-200 rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-xs uppercase text-slate-500 tracking-wider">
                    <tr>
                        <th class="px-6 py-3 font-medium">Perusahaan</th>
                        <th class="px-6 py-3 font-medium">Kontak</th>
                        <th class="px-6 py-3 font-medium">Paket Langganan</th>
                        <th class="px-6 py-3 font-medium">Penggunaan Kuota</th>
                        <th class="px-6 py-3 font-medium text-center">Status</th>
                        <th class="px-6 py-3 font-medium text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse ($companies as $company)
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="font-semibold text-slate-800">{{ $company->name }}</div>
                                <div class="text-slate-500">{{ $company->industry_type ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-slate-800">{{ $company->email ?? '-' }}</div>
                                <div class="text-slate-500">{{ $company->phone ?? '-' }}</div>
                            </td>
                             <td class="px-6 py-4 whitespace-nowrap">
                                <div class="font-semibold text-slate-800">{{ $company->package->name ?? 'Belum ada paket' }}</div>
                                <div class="text-slate-500 text-xs">
                                    @if ($company->package_end_date)
                                        Berakhir pada: {{ $company->package_end_date->format('d M Y') }}
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $letterUsage = $company->letters_used_count ?? 0;
                                    $maxLetters = $company->package->max_letters ?? 0;
                                    $letterPercentage = $maxLetters > 0 ? ($letterUsage / $maxLetters) * 100 : 0;
                                @endphp
                                <div class="space-y-2">
                                    <div class="text-xs">
                                        <span class="font-medium text-slate-600">Surat:</span> {{ $letterUsage }} / {{ $maxLetters > 0 ? $maxLetters : '∞' }}
                                    </div>
                                    <div class="w-full bg-slate-200 rounded-full h-1.5">
                                        <div class="bg-blue-500 h-1.5 rounded-full" style="width: {{ $letterPercentage }}%"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                               @if($company->is_active)
                                    <span class="inline-flex items-center gap-1.5 px-2 py-1 text-xs font-medium text-green-700 bg-green-100 rounded-full">
                                        <span class="h-1.5 w-1.5 rounded-full bg-green-500"></span> Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2 py-1 text-xs font-medium text-red-700 bg-red-100 rounded-full">
                                         <span class="h-1.5 w-1.5 rounded-full bg-red-500"></span> Nonaktif
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                               {{-- Di sini bisa dibuat komponen terpisah <x-company-actions :company="$company" /> --}}
                               <div x-data="{ open: false }" class="relative inline-block text-left">
                                    <button @click="open = !open" class="p-2 text-slate-500 hover:text-slate-800 rounded-full hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" /></svg>
                                    </button>
                                    <div x-show="open" @click.away="open = false" x-cloak x-transition class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-10">
                                        <div class="py-1" role="menu">
                                            <a href="{{ route('companies.edit', $company) }}" class="flex items-center gap-3 px-4 py-2 text-sm text-slate-700 hover:bg-slate-100" role="menuitem">
                                                <svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125" /></svg>
                                                <span>Edit</span>
                                            </a>
                                            {{-- Aksi lainnya --}}
                                            <div class="border-t border-slate-200 my-1"></div>
                                            <form action="{{ route('companies.destroy', $company) }}" method="POST" onsubmit="return confirm('PERINGATAN: Yakin ingin menghapus perusahaan ini?')" class="w-full">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="w-full text-left flex items-center gap-3 px-4 py-2 text-sm text-red-600 hover:bg-red-50" role="menuitem">
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
                            <td colspan="6" class="px-6 py-12 text-center text-slate-500">
                                <p class="font-semibold">Data Perusahaan Tidak Ditemukan</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if ($companies->hasPages())
        <div class="mt-6">
            {{ $companies->appends(request()->query())->links() }}
        </div>
    @endif
</div>
@endsection