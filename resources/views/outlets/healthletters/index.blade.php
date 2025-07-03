@extends('layouts.app', ['header' => 'Manajemen Surat Kesehatan'])

@section('content')
<div class="space-y-6">

    <div aria-live="assertive" class="pointer-events-none fixed inset-0 flex items-start px-4 py-6 sm:p-6 z-50">
        <div class="flex w-full flex-col items-center space-y-4 sm:items-end">
            @if (session('success'))
                {{-- Komponen alert ini harus sudah Anda buat di resources/views/components/alert.blade.php --}}
                <x-alert type="success" :message="session('success')" />
            @endif
            @if (session('error'))
                <x-alert type="error" :message="session('error')" />
            @endif
        </div>
    </div>

    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Riwayat Surat Kesehatan</h1>
            <p class="mt-1 text-sm text-slate-500">Lihat, kelola, dan terbitkan semua surat sehat (SKB) dan surat sakit (MC).</p>
        </div>
        <div class="flex items-center gap-2 flex-shrink-0">
            <a href="{{ route('outlet.results.skb.create') }}"
               class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-blue-600 text-white font-semibold rounded-lg shadow-md hover:bg-blue-700 transition-all duration-200">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m6-6H6" /></svg>
                <span>Buat Surat Sehat</span>
            </a>
            <a href="{{ route('outlet.results.mc.create') }}"
               class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-rose-600 text-white font-semibold rounded-lg shadow-md hover:bg-rose-700 transition-all duration-200">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m6-6H6" /></svg>
                <span>Buat Surat Sakit</span>
            </a>
        </div>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        @php
            $statCards = [
                ['label' => 'Total Surat (Bulan Ini)', 'value' => $totalLettersThisMonth, 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m9.75 9.75l-2.25 2.25m0 0l-2.25 2.25m2.25-2.25l2.25 2.25m-2.25-2.25l-2.25-2.25M12 21l-2.25-2.25m0 0l-2.25-2.25m2.25 2.25l2.25-2.25m2.25 2.25l-2.25 2.25M12 3l2.25 2.25m0 0l2.25 2.25M12 3l-2.25 2.25m0 0l-2.25 2.25" />', 'color' => 'blue'],
                ['label' => 'Surat Sehat (Bulan Ini)', 'value' => $totalSKBThisMonth, 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />', 'color' => 'green'],
                ['label' => 'Surat Sakit (Bulan Ini)', 'value' => $totalMCThisMonth, 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />', 'color' => 'red'],
                ['label' => 'Total Surat (Semua)', 'value' => $totalLettersAllTime, 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />', 'color' => 'slate'],
            ];
        @endphp
        @foreach($statCards as $card)
            <div class="bg-white border border-slate-200 rounded-xl p-5 flex items-center gap-5 transition-all duration-300 hover:shadow-md hover:-translate-y-1">
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
        <form id="filter-form" action="{{ route('outlet.healthletter.index') }}" method="GET" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
            <div>
                <label for="keyword" class="block text-xs font-medium text-slate-600 mb-1">Cari Pasien</label>
                <input type="text" name="keyword" id="keyword" value="{{ request('keyword') }}" placeholder="Nama pasien..."
                       class="w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
            </div>
            <div>
                <label for="from" class="block text-xs font-medium text-slate-600 mb-1">Dari Tanggal</label>
                <input type="date" name="from" id="from" value="{{ request('from') }}"
                       class="w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
            </div>
            <div>
                <label for="to" class="block text-xs font-medium text-slate-600 mb-1">Sampai Tanggal</label>
                <input type="date" name="to" id="to" value="{{ request('to') }}"
                       class="w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
            </div>
            <div>
                <label for="type" class="block text-xs font-medium text-slate-600 mb-1">Jenis Surat</label>
                <select name="type" id="type" class="w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    <option value="">Semua Jenis</option>
                    <option value="skb" @selected(request('type') === 'skb')>Surat Sehat (SKB)</option>
                    <option value="mc" @selected(request('type') === 'mc')>Surat Sakit (MC)</option>
                </select>
            </div>
             <div>
                <label for="doctor_id" class="block text-xs font-medium text-slate-600 mb-1">Dokter</label>
                <select name="doctor_id" id="doctor_id" class="w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    <option value="">Semua Dokter</option>
                    @foreach($doctors as $doctor)
                        <option value="{{ $doctor->id }}" @selected(request('doctor_id') == $doctor->id)>{{ $doctor->user->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="w-full px-4 py-2 bg-slate-800 text-white rounded-md text-sm font-semibold hover:bg-slate-700 transition">Filter</button>
                <a href="{{ route('outlet.healthletter.index') }}" class="w-full text-center px-4 py-2 bg-slate-200 text-slate-700 rounded-md text-sm font-medium hover:bg-slate-300 transition">Reset</a>
            </div>
        </form>
    </div>

    <div class="bg-white border border-slate-200 rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-xs uppercase text-slate-500 tracking-wider">
                    <tr>
                        <th class="px-6 py-3 font-medium">Pasien</th>
                        <th class="px-6 py-3 font-medium">Detail Surat</th>
                        <th class="px-6 py-3 font-medium">Dokter</th>
                        <th class="px-6 py-3 font-medium text-center">Status</th>
                        <th class="px-6 py-3 font-medium text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse ($results as $result)
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="font-semibold text-slate-800">{{ $result->patient->full_name ?? '-' }}</div>
                                <div class="text-slate-500 text-xs">No. ID: {{ $result->patient->identity ?? 'N/A' }}</div>
                            </td>
                             <td class="px-6 py-4 whitespace-nowrap">
                                <div class="font-semibold text-slate-800 uppercase">{{ $result->type }} - {{ $result->no_letters ?? 'N/A' }}</div>
                                <div class="text-slate-500 text-xs">Tgl: {{ \Carbon\Carbon::parse($result->date)->format('d M Y') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-slate-600">
                                {{ $result->doctor->user->name ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if($result->document_name)
                                    <span class="inline-flex items-center gap-1.5 px-2 py-1 text-xs font-medium text-blue-700 bg-blue-100 rounded-full">
                                        <span class="h-1.5 w-1.5 rounded-full bg-blue-500"></span> Selesai
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2 py-1 text-xs font-medium text-amber-700 bg-amber-100 rounded-full">
                                         <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span> Draft
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                               <div x-data="{ open: false }" class="relative inline-block text-left">
                                    <button @click="open = !open" class="p-2 text-slate-500 hover:text-slate-800 rounded-full hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" /></svg>
                                    </button>
                                    <div x-show="open" @click.away="open = false" x-cloak x-transition class="origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-20">
                                        <div class="py-1" role="menu">
                                            @if(in_array($result->type, ['mc', 'skb']) && $result->document_name)
                                                @php
                                                    $previewRoute = $result->type === 'mc'
                                                        ? route('outlet.results.mc.preview', Crypt::encrypt($result->id))
                                                        : route('outlet.results.skb.preview', Crypt::encrypt($result->id));
                                                @endphp
                                                <a href="{{ $previewRoute }}" target="_blank" class="flex items-center gap-3 px-4 py-2 text-sm text-slate-700 hover:bg-slate-100" role="menuitem">
                                                    <svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                                    <span>Lihat Preview</span>
                                                </a>
                                                 <a href="{{ route('outlet.results.download', Crypt::encrypt($result->id)) }}" target="_blank" class="flex items-center gap-3 px-4 py-2 text-sm text-slate-700 hover:bg-slate-100" role="menuitem">
                                                     <svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
                                                    <span>Download</span>
                                                </a>
                                            @endif
                                            {{-- Tombol Edit ditempatkan di sini --}}
                                            <a href="#" class="flex items-center gap-3 px-4 py-2 text-sm text-slate-700 hover:bg-slate-100" role="menuitem">
                                               <svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125" /></svg>
                                                <span>Edit</span>
                                            </a>
                                            <div class="border-t border-slate-200 my-1"></div>
                                            <form method="POST" action="{{ route('outlet.results.' . $result->type . '.delete', $result->id) }}" onsubmit="return confirm('Hapus surat ini?')" class="w-full">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="w-full text-left flex items-center gap-3 px-4 py-2 text-sm text-red-600 hover:bg-red-50" role="menuitem">
                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" /></svg>
                                                    <span>Hapus</span>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-slate-500">
                                <p class="font-semibold">Belum ada surat yang dibuat.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    @if ($results->hasPages())
        <div class="mt-6">
            {{ $results->appends(request()->query())->links() }}
        </div>
    @endif
</div>
@endsection