@extends('layouts.app', ['header' => 'Manajemen Surat Kesehatan'])

@section('content')
<div class="space-y-6">

    <!-- Toast Notifications -->
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

    <!-- Header Section -->
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6">
        <div class="space-y-2">
            <h1 class="text-3xl font-bold text-slate-800">Riwayat Surat Kesehatan</h1>
            <p class="text-slate-600">Lihat, kelola, dan terbitkan semua surat sehat (SKB) dan surat sakit (MC).</p>
        </div>
        
        <!-- Action Buttons -->
        <div class="flex items-center gap-3 flex-shrink-0">
            <a href="{{ route('outlet.results.skb.create') }}"
               class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-emerald-600 text-white font-semibold rounded-lg shadow-md hover:bg-emerald-700 hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-200">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>Buat Surat Sehat</span>
            </a>
            <a href="{{ route('outlet.results.mc.create') }}"
               class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-rose-600 text-white font-semibold rounded-lg shadow-md hover:bg-rose-700 hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-200">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                </svg>
                <span>Buat Surat Sakit</span>
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        @php
            $statCards = [
                [
                    'label' => 'Total Surat (Bulan Ini)', 
                    'value' => $totalLettersThisMonth, 
                    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m9.75 9.75l-2.25 2.25m0 0l-2.25 2.25m2.25-2.25l2.25 2.25m-2.25-2.25l-2.25-2.25M12 21l-2.25-2.25m0 0l-2.25-2.25m2.25 2.25l2.25-2.25m2.25 2.25l-2.25 2.25M12 3l2.25 2.25m0 0l2.25 2.25M12 3l-2.25 2.25m0 0l-2.25 2.25" />', 
                    'color' => 'blue',
                    'bgGradient' => 'from-blue-50 to-blue-100'
                ],
                [
                    'label' => 'Surat Sehat (Bulan Ini)', 
                    'value' => $totalSKBThisMonth, 
                    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />', 
                    'color' => 'emerald',
                    'bgGradient' => 'from-emerald-50 to-emerald-100'
                ],
                [
                    'label' => 'Surat Sakit (Bulan Ini)', 
                    'value' => $totalMCThisMonth, 
                    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />', 
                    'color' => 'rose',
                    'bgGradient' => 'from-rose-50 to-rose-100'
                ],
                [
                    'label' => 'Total Surat (Semua)', 
                    'value' => $totalLettersAllTime, 
                    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />', 
                    'color' => 'slate',
                    'bgGradient' => 'from-slate-50 to-slate-100'
                ],
            ];
        @endphp
        
        @foreach($statCards as $card)
            <div class="relative overflow-hidden bg-gradient-to-br {{$card['bgGradient']}} border border-{{$card['color']}}-200 rounded-xl p-6 group hover:shadow-lg hover:-translate-y-1 transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-sm font-medium text-{{$card['color']}}-600 mb-1">{{ $card['label'] }}</p>
                        <p class="text-3xl font-bold text-{{$card['color']}}-700">{{ $card['value'] }}</p>
                    </div>
                    <div class="w-14 h-14 flex-shrink-0 flex items-center justify-center bg-{{$card['color']}}-500 bg-opacity-10 rounded-full group-hover:bg-opacity-20 transition-all duration-300">
                        <svg class="w-8 h-8 text-{{$card['color']}}-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">{!! $card['icon'] !!}</svg>
                    </div>
                </div>
                <!-- Decorative gradient overlay -->
                <div class="absolute top-0 right-0 w-20 h-20 bg-{{$card['color']}}-400 bg-opacity-10 rounded-full -translate-y-10 translate-x-10"></div>
            </div>
        @endforeach
    </div>

    <!-- Filter Section -->
    <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm">
        <h3 class="text-lg font-semibold text-slate-800 mb-4">Filter Pencarian</h3>
        <form id="filter-form" action="{{ route('outlet.healthletter.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4">
            <div>
                <label for="keyword" class="block text-sm font-medium text-slate-700 mb-2">Cari Pasien</label>
                <input type="text" name="keyword" id="keyword" value="{{ request('keyword') }}" placeholder="Nama pasien..."
                       class="w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition-colors">
            </div>
            <div>
                <label for="from" class="block text-sm font-medium text-slate-700 mb-2">Dari Tanggal</label>
                <input type="date" name="from" id="from" value="{{ request('from') }}"
                       class="w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition-colors">
            </div>
            <div>
                <label for="to" class="block text-sm font-medium text-slate-700 mb-2">Sampai Tanggal</label>
                <input type="date" name="to" id="to" value="{{ request('to') }}"
                       class="w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition-colors">
            </div>
            <div>
                <label for="type" class="block text-sm font-medium text-slate-700 mb-2">Jenis Surat</label>
                <select name="type" id="type" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition-colors">
                    <option value="">Semua Jenis</option>
                    <option value="skb" @selected(request('type') === 'skb')>Surat Sehat (SKB)</option>
                    <option value="mc" @selected(request('type') === 'mc')>Surat Sakit (MC)</option>
                </select>
            </div>
            <div>
                <label for="doctor_id" class="block text-sm font-medium text-slate-700 mb-2">Dokter</label>
                <select name="doctor_id" id="doctor_id" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition-colors">
                    <option value="">Semua Dokter</option>
                    @foreach($doctors as $doctor)
                        <option value="{{ $doctor->id }}" @selected(request('doctor_id') == $doctor->id)>{{ $doctor->user->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 px-4 py-2.5 bg-slate-800 text-white rounded-lg font-semibold hover:bg-slate-700 transition-all duration-200 shadow-sm hover:shadow">
                    <span class="flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                        </svg>
                        Filter
                    </span>
                </button>
                <a href="{{ route('outlet.healthletter.index') }}" class="flex-1 text-center px-4 py-2.5 bg-slate-100 text-slate-700 rounded-lg font-medium hover:bg-slate-200 transition-all duration-200">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Results Table -->
    <div class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Pasien</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Detail Surat</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-slate-600 uppercase tracking-wider">Jenis</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Dokter</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-slate-600 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-slate-600 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-slate-200">
                    @forelse ($results as $result)
                        <tr class="hover:bg-slate-50 transition-colors duration-150">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded-full bg-slate-200 flex items-center justify-center">
                                            <svg class="h-6 w-6 text-slate-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-semibold text-slate-900">{{ $result->patient->full_name ?? '-' }}</div>
                                        <div class="text-sm text-slate-500">ID: {{ $result->patient->identity ?? 'N/A' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-semibold text-slate-900 uppercase">{{ $result->type }} - {{ $result->no_letters ?? 'N/A' }}</div>
                                <div class="text-sm text-slate-500">{{ \Carbon\Carbon::parse($result->date)->format('d M Y') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-slate-900">{{ \Carbon\Carbon::parse($result->date)->format('d M Y') }}</div>
                                <div class="text-sm text-slate-500">{{ \Carbon\Carbon::parse($result->date)->format('H:i') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if($result->type === 'skb')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                                        <svg class="mr-1.5 h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                        </svg>
                                        Surat Sehat
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-rose-100 text-rose-800">
                                        <svg class="mr-1.5 h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                        </svg>
                                        Surat Sakit
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-slate-900">{{ $result->doctor->user->name ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if($result->document_name)
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-medium text-blue-700 bg-blue-100 rounded-full">
                                        <span class="h-2 w-2 rounded-full bg-blue-500"></span> 
                                        Selesai
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-medium text-amber-700 bg-amber-100 rounded-full">
                                        <span class="h-2 w-2 rounded-full bg-amber-500"></span> 
                                        Draft
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <div class="flex items-center justify-center gap-2">
                                    
                                    @if(in_array($result->type, ['mc', 'skb']) && $result->document_name)
                                        @php
                                            $previewRoute = $result->type === 'mc'
                                                ? route('outlet.results.mc.preview', Crypt::encrypt($result->id))
                                                : route('outlet.results.skb.preview', Crypt::encrypt($result->id));
                                            $regenerateRoute = $result->type === 'mc'
                                                ? route('outlet.results.mc.regenerate', $result->id)
                                                : route('outlet.results.skb.regenerate', $result->id);
                                        @endphp

                                        <!-- Preview Button -->
                                        <a href="{{ $previewRoute }}" target="_blank"
                                           class="inline-flex items-center justify-center w-9 h-9 text-slate-600 bg-slate-100 rounded-lg hover:bg-blue-100 hover:text-blue-600 transition-all duration-200 group" 
                                           title="Lihat Preview">
                                            <svg class="w-4 h-4 group-hover:scale-110 transition-transform" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                        </a>

                                        <!-- Download Button -->
                                        <a href="{{ route('outlet.results.download', Crypt::encrypt($result->id)) }}" target="_blank"
                                           class="inline-flex items-center justify-center w-9 h-9 text-slate-600 bg-slate-100 rounded-lg hover:bg-emerald-100 hover:text-emerald-600 transition-all duration-200 group" 
                                           title="Download PDF">
                                            <svg class="w-4 h-4 group-hover:scale-110 transition-transform" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                                            </svg>
                                        </a>
                                        <!-- Edit Button -->
                                        <!-- @if($result->type === 'skb')
                                            <a href="{{ route('outlet.results.skb.edit', $result->id) }}" 
                                            class="inline-flex items-center justify-center w-9 h-9 text-slate-600 bg-slate-100 rounded-lg hover:bg-blue-100 hover:text-blue-600 transition-all duration-200 group" 
                                            title="Edit SKB">
                                                <svg class="w-4 h-4 group-hover:scale-110 transition-transform" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                </svg>
                                            </a>
                                        @elseif($result->type === 'mc')
                                            <a href="{{ route('outlet.results.mc.edit', $result->id) }}" 
                                            class="inline-flex items-center justify-center w-9 h-9 text-slate-600 bg-slate-100 rounded-lg hover:bg-blue-100 hover:text-blue-600 transition-all duration-200 group" 
                                            title="Edit MC">
                                                <svg class="w-4 h-4 group-hover:scale-110 transition-transform" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                </svg>
                                            </a>
                                        @endif -->

                                        <!-- Enhanced Regenerate Button -->
                                        <button onclick="confirmRegenerate('{{ $result->id }}', '{{ $result->type }}', '{{ $result->patient->full_name ?? 'Pasien' }}', '{{ $regenerateRoute }}')"
                                                class="inline-flex items-center justify-center w-9 h-9 text-slate-600 bg-slate-100 rounded-lg hover:bg-amber-100 hover:text-amber-600 transition-all duration-200 group" 
                                                title="Regenerate PDF">
                                            <svg class="w-4 h-4 group-hover:scale-110 transition-transform" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                                            </svg>
                                        </button>
                                    @endif

                                    <!-- Delete Button with Form -->
                                    @php
                                        $deleteRoute = $result->type === 'mc'
                                            ? route('outlet.results.mc.delete', $result->id)
                                            : route('outlet.results.skb.delete', $result->id);
                                    @endphp
                                    
                                    <form id="delete-form-{{ $result->id }}" action="{{ $deleteRoute }}" method="POST" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" 
                                                onclick="confirmDelete('{{ $result->id }}', '{{ $result->type }}', '{{ $result->patient->full_name ?? 'Pasien' }}', 'delete-form-{{ $result->id }}')"
                                                class="inline-flex items-center justify-center w-9 h-9 text-slate-600 bg-slate-100 rounded-lg hover:bg-red-100 hover:text-red-600 transition-all duration-200 group" 
                                                title="Hapus Surat ke Trash">
                                            <svg class="w-4 h-4 group-hover:scale-110 transition-transform" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                            </svg>
                                        </button>
                                    </form>

                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center space-y-3">
                                    <svg class="w-12 h-12 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m9.75 9.75l-2.25 2.25m0 0l-2.25 2.25m2.25-2.25l2.25 2.25m-2.25-2.25l-2.25-2.25M12 21l-2.25-2.25m0 0l-2.25-2.25m2.25 2.25l2.25-2.25m2.25 2.25l-2.25 2.25M12 3l2.25 2.25m0 0l2.25 2.25M12 3l-2.25 2.25m0 0l-2.25 2.25" />
                                    </svg>
                                    <div class="text-slate-500">
                                        <p class="font-semibold text-lg">Belum ada surat yang dibuat</p>
                                        <p class="text-sm">Mulai buat surat kesehatan untuk pasien Anda</p>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Pagination -->
    @if ($results->hasPages())
        <div class="flex justify-center mt-6">
            <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-1">
                {{ $results->appends(request()->query())->links() }}
            </div>
        </div>
    @endif
</div>

<!-- Regenerate Confirmation Modal -->
<div id="regenerateModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-xl bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-center w-12 h-12 mx-auto bg-amber-100 rounded-full">
                <svg class="w-6 h-6 text-amber-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                </svg>
            </div>
            <div class="mt-5 text-center">
                <h3 class="text-lg font-semibold text-gray-900">Regenerate PDF</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500">
                        Apakah Anda yakin ingin regenerate PDF untuk surat <span id="regenerateType" class="font-semibold"></span> 
                        pasien <span id="regeneratePatient" class="font-semibold"></span>?
                    </p>
                    <div class="mt-3 p-3 bg-amber-50 rounded-lg">
                        <p class="text-xs text-amber-700">
                            <strong>Catatan:</strong> Proses regenerate akan membuat ulang file PDF dengan data terbaru. 
                            Proses ini akan memakan waktu beberapa saat.
                        </p>
                    </div>
                </div>
                <div class="flex gap-3 px-4 py-3">
                    <button onclick="closeRegenerateModal()" 
                            class="flex-1 px-4 py-2 bg-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-400 transition-colors">
                        Batal
                    </button>
                    <button onclick="executeRegenerate()" 
                            class="flex-1 px-4 py-2 bg-amber-600 text-white text-sm font-medium rounded-lg hover:bg-amber-700 transition-colors">
                        Ya, Regenerate
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-xl bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 rounded-full">
                <svg class="w-6 h-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                </svg>
            </div>
            <div class="mt-5 text-center">
                <h3 class="text-lg font-semibold text-gray-900">Hapus Surat ke Trash</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500">
                        Apakah Anda yakin ingin menghapus surat <span id="deleteType" class="font-semibold"></span> 
                        pasien <span id="deletePatient" class="font-semibold"></span>?
                    </p>
                    <div class="mt-3 p-3 bg-yellow-50 rounded-lg">
                        <p class="text-xs text-yellow-700">
                            <strong>Info:</strong> Surat akan dipindahkan ke trash dan dapat dipulihkan kembali jika diperlukan. 
                            Data tidak akan hilang permanen.
                        </p>
                    </div>
                </div>
                <div class="flex gap-3 px-4 py-3">
                    <button onclick="closeDeleteModal()" 
                            class="flex-1 px-4 py-2 bg-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-400 transition-colors">
                        Batal
                    </button>
                    <button onclick="executeDelete()" 
                            class="flex-1 px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition-colors">
                        Ya, Pindah ke Trash
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let currentRegenerateId = null;
let currentRegenerateType = null;
let currentRegenerateUrl = null;
let currentDeleteId = null;
let currentDeleteType = null;
let currentDeleteFormId = null;

// Get CSRF token
function getCSRFToken() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (csrfToken) {
        return csrfToken.getAttribute('content');
    }
    
    // Fallback: try to get from Laravel CSRF token input
    const csrfInput = document.querySelector('input[name="_token"]');
    if (csrfInput) {
        return csrfInput.value;
    }
    
    return null;
}

// Regenerate Modal Functions
function confirmRegenerate(id, type, patientName, regenerateUrl) {
    currentRegenerateId = id;
    currentRegenerateType = type;
    currentRegenerateUrl = regenerateUrl;
    
    document.getElementById('regenerateType').textContent = type.toUpperCase();
    document.getElementById('regeneratePatient').textContent = patientName;
    document.getElementById('regenerateModal').classList.remove('hidden');
}

function closeRegenerateModal() {
    document.getElementById('regenerateModal').classList.add('hidden');
    currentRegenerateId = null;
    currentRegenerateType = null;
    currentRegenerateUrl = null;
}

function executeRegenerate() {
    if (currentRegenerateUrl) {
        // Show loading state
        const button = event.target;
        const originalText = button.innerHTML;
        button.innerHTML = `
            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Memproses...
        `;
        button.disabled = true;
        
        // Redirect to regenerate route (GET request, no CSRF needed)
        window.location.href = currentRegenerateUrl;
    }
}

// Delete Modal Functions
function confirmDelete(id, type, patientName, formId) {
    currentDeleteId = id;
    currentDeleteType = type;
    currentDeleteFormId = formId;
    
    document.getElementById('deleteType').textContent = type.toUpperCase();
    document.getElementById('deletePatient').textContent = patientName;
    document.getElementById('deleteModal').classList.remove('hidden');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
    currentDeleteId = null;
    currentDeleteType = null;
    currentDeleteFormId = null;
}

function executeDelete() {
    if (currentDeleteFormId) {
        // Show loading state
        const button = event.target;
        const originalText = button.innerHTML;
        button.innerHTML = `
            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Memproses...
        `;
        button.disabled = true;
        
        // Submit the existing form with CSRF token
        const form = document.getElementById(currentDeleteFormId);
        if (form) {
            try {
                form.submit();
            } catch (error) {
                console.error('Error submitting form:', error);
                alert('Terjadi kesalahan. Silakan coba lagi.');
                button.innerHTML = originalText;
                button.disabled = false;
            }
        } else {
            alert('Form tidak ditemukan. Silakan refresh halaman dan coba lagi.');
            button.innerHTML = originalText;
            button.disabled = false;
        }
    }
}

// Close modals when clicking outside
document.addEventListener('click', function(event) {
    const regenerateModal = document.getElementById('regenerateModal');
    const deleteModal = document.getElementById('deleteModal');
    
    if (event.target === regenerateModal) {
        closeRegenerateModal();
    }
    
    if (event.target === deleteModal) {
        closeDeleteModal();
    }
});

// Close modals with Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeRegenerateModal();
        closeDeleteModal();
    }
});

// Debug function to check CSRF token
function debugCSRF() {
    const token = getCSRFToken();
    console.log('CSRF Token:', token);
    return token;
}
</script>

@endsection