@extends('layouts.app')

@section('title', 'Data Karyawan - ' . $company->name)

@section('content')
<div class="max-w-7xl mx-auto space-y-8">
    {{-- Enhanced Header Section --}}
    <div class="relative overflow-hidden bg-gradient-to-br from-slate-600 via-slate-700 to-slate-800 rounded-2xl shadow-2xl">
        <div class="absolute inset-0 bg-black/10 backdrop-blur-sm"></div>
        <div class="absolute top-0 right-0 w-96 h-96 bg-white/5 rounded-full -translate-y-48 translate-x-48"></div>
        <div class="absolute bottom-0 left-0 w-64 h-64 bg-white/5 rounded-full translate-y-32 -translate-x-32"></div>
        
        <div class="relative z-10 p-8">
            <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6">
                <div class="space-y-3">
                    <div class="flex items-center gap-4">
                        <div class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center">
                            <svg class="w-9 h-9 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.003c0 1.113.285 2.16.786 3.07m-9.328-1.635a9.38 9.38 0 012.625-.372 9.337 9.337 0 014.121.952 4.125 4.125 0 01-7.533 2.493m-3.328 1.635c1.256-.368 2.355-.936 3.328-1.635m0 0a13.792 13.792 0 00-3.328-1.635" />
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-4xl font-bold text-white">Data Karyawan</h1>
                            <p class="text-slate-200 text-lg">{{ $company->name }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="flex items-center gap-3">
                    <a href="{{ route('company.dashboard') }}" class="group inline-flex items-center gap-3 px-6 py-3 bg-white/20 backdrop-blur-sm text-white border border-white/30 rounded-xl font-semibold hover:bg-white/30 hover:scale-105 transition-all duration-300">
                        <svg class="w-5 h-5 group-hover:-rotate-12 transition-transform" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                        </svg>
                        Kembali ke Dashboard
                    </a>
                    <button id="exportBtn" class="group inline-flex items-center gap-3 px-6 py-3 bg-white/90 backdrop-blur-sm text-slate-700 rounded-xl font-semibold hover:bg-white hover:scale-105 transition-all duration-300 shadow-lg">
                        <svg class="w-5 h-5 group-hover:rotate-12 transition-transform" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                        </svg>
                        Export Data
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Statistics Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="group relative overflow-hidden bg-white rounded-2xl shadow-lg border border-slate-200 p-6 hover:shadow-2xl hover:-translate-y-2 transition-all duration-500">
            <div class="absolute inset-0 bg-gradient-to-br from-blue-50 to-blue-100 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-14 h-14 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-7 h-7 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.003c0 1.113.285 2.16.786 3.07m-9.328-1.635a9.38 9.38 0 012.625-.372 9.337 9.337 0 014.121.952 4.125 4.125 0 01-7.533 2.493m-3.328 1.635c1.256-.368 2.355-.936 3.328-1.635m0 0a13.792 13.792 0 00-3.328-1.635" />
                        </svg>
                    </div>
                </div>
                <div>
                    <p class="text-sm font-medium text-slate-600 mb-1">Total Karyawan</p>
                    <p class="text-3xl font-bold text-slate-900 mb-1">{{ number_format($totalPatients) }}</p>
                    <p class="text-xs text-slate-500">Terdaftar aktif</p>
                </div>
            </div>
        </div>

        <div class="group relative overflow-hidden bg-white rounded-2xl shadow-lg border border-slate-200 p-6 hover:shadow-2xl hover:-translate-y-2 transition-all duration-500">
            <div class="absolute inset-0 bg-gradient-to-br from-emerald-50 to-emerald-100 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-14 h-14 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-2xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-7 h-7 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                        </svg>
                    </div>
                </div>
                <div>
                    <p class="text-sm font-medium text-slate-600 mb-1">Laki-laki</p>
                    <p class="text-3xl font-bold text-slate-900 mb-1">{{ number_format($totalMale) }}</p>
                    <p class="text-xs text-slate-500">{{ $totalPatients > 0 ? number_format(($totalMale / $totalPatients) * 100, 1) : 0 }}% dari total</p>
                </div>
            </div>
        </div>

        <div class="group relative overflow-hidden bg-white rounded-2xl shadow-lg border border-slate-200 p-6 hover:shadow-2xl hover:-translate-y-2 transition-all duration-500">
            <div class="absolute inset-0 bg-gradient-to-br from-pink-50 to-pink-100 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-14 h-14 bg-gradient-to-br from-pink-500 to-pink-600 rounded-2xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-7 h-7 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                        </svg>
                    </div>
                </div>
                <div>
                    <p class="text-sm font-medium text-slate-600 mb-1">Perempuan</p>
                    <p class="text-3xl font-bold text-slate-900 mb-1">{{ number_format($totalFemale) }}</p>
                    <p class="text-xs text-slate-500">{{ $totalPatients > 0 ? number_format(($totalFemale / $totalPatients) * 100, 1) : 0 }}% dari total</p>
                </div>
            </div>
        </div>

        <div class="group relative overflow-hidden bg-white rounded-2xl shadow-lg border border-slate-200 p-6 hover:shadow-2xl hover:-translate-y-2 transition-all duration-500">
            <div class="absolute inset-0 bg-gradient-to-br from-purple-50 to-purple-100 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-14 h-14 bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-7 h-7 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                        </svg>
                    </div>
                </div>
                <div>
                    <p class="text-sm font-medium text-slate-600 mb-1">Outlet Aktif</p>
                    <p class="text-3xl font-bold text-slate-900 mb-1">{{ number_format($totalOutlets) }}</p>
                    <p class="text-xs text-slate-500">Lokasi kerja</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Search and Filter Section --}}
    <div class="bg-white/90 backdrop-blur-xl rounded-2xl shadow-xl border border-slate-200 p-6">
        <div class="flex flex-col lg:flex-row gap-6 items-start lg:items-center justify-between">
            <div class="flex-1 grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="relative">
                    <input type="text" id="searchInput" placeholder="Cari nama karyawan..." 
                           class="w-full pl-10 pr-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <svg class="w-5 h-5 text-slate-400 absolute left-3 top-1/2 transform -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                    </svg>
                </div>
                
                <select id="genderFilter" class="border border-slate-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Semua Gender</option>
                    <option value="L">Laki-laki</option>
                    <option value="P">Perempuan</option>
                </select>
                
                <select id="outletFilter" class="border border-slate-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Semua Outlet</option>
                    @foreach($outlets as $outlet)
                        <option value="{{ $outlet->id }}">{{ $outlet->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="flex gap-3">
                <button id="resetFilters" class="px-4 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl transition-colors font-medium">
                    Reset Filter
                </button>
                <button id="refreshData" class="px-4 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl transition-colors font-medium flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                    </svg>
                    Refresh
                </button>
            </div>
        </div>
    </div>

    {{-- Enhanced Table Section --}}
    <div class="bg-white/90 backdrop-blur-xl rounded-2xl shadow-xl border border-slate-200 overflow-hidden">
        <div class="p-6 border-b border-slate-200">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-xl font-bold text-slate-900">Daftar Karyawan</h3>
                    <p class="text-sm text-slate-600 mt-1">Total: <span id="totalCount">{{ number_format($totalPatients) }}</span> karyawan</p>
                </div>
                <div class="flex items-center gap-3">
                    <div class="flex items-center gap-2 text-sm text-slate-600">
                        <span>Tampilkan:</span>
                        <select id="perPageSelect" class="border border-slate-300 rounded-lg px-3 py-1 text-sm">
                            <option value="10">10</option>
                            <option value="25" selected>25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto" id="tableContainer">
            <table class="w-full">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">
                            <div class="flex items-center gap-2 cursor-pointer hover:text-slate-900" data-sort="full_name">
                                Nama Karyawan
                                <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 15L12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9" />
                                </svg>
                            </div>
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">
                            <div class="flex items-center gap-2 cursor-pointer hover:text-slate-900" data-sort="nik">
                                NIK
                                <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 15L12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9" />
                                </svg>
                            </div>
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Gender</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Kontak</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Outlet</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-slate-600 uppercase tracking-wider">Medical Check-up</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-slate-600 uppercase tracking-wider">Status Terakhir</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-slate-600 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200" id="tableBody">
                    @forelse($patients as $patient)
                        <tr class="hover:bg-slate-50 transition-colors group" data-patient-id="{{ $patient->id }}">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full {{ $patient->gender === 'L' ? 'bg-blue-100' : 'bg-pink-100' }} flex items-center justify-center flex-shrink-0">
                                        <span class="text-sm font-semibold {{ $patient->gender === 'L' ? 'text-blue-700' : 'text-pink-700' }}">
                                            {{ strtoupper(substr($patient->full_name, 0, 2)) }}
                                        </span>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-slate-900">{{ $patient->full_name }}</p>
                                        <p class="text-sm text-slate-500">ID: {{ $patient->identity ?? '-' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-mono text-sm text-slate-700">{{ $patient->nik ?? '-' }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium {{ $patient->gender === 'L' ? 'bg-blue-100 text-blue-700' : 'bg-pink-100 text-pink-700' }}">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24">
                                        @if($patient->gender === 'L')
                                            <path d="M12 2a10 10 0 1010 10A10 10 0 0012 2zm0 18a8 8 0 118-8 8 8 0 01-8 8z"/>
                                            <path d="M12 6a6 6 0 106 6 6 6 0 00-6-6zm0 10a4 4 0 114-4 4 4 0 01-4 4z"/>
                                        @else
                                            <path d="M12 2a10 10 0 1010 10A10 10 0 0012 2zm0 18a8 8 0 118-8 8 8 0 01-8 8z"/>
                                            <circle cx="12" cy="12" r="3"/>
                                        @endif
                                    </svg>
                                    {{ $patient->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="space-y-1">
                                    <p class="text-sm text-slate-900">{{ $patient->phone ?? '-' }}</p>
                                    @if($patient->birth_date)
                                        <p class="text-xs text-slate-500">{{ \Carbon\Carbon::parse($patient->birth_date)->age }} tahun</p>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <div class="w-2 h-2 bg-green-400 rounded-full"></div>
                                    <span class="text-sm text-slate-700">{{ $patient->outlet->name ?? '-' }}</span>
                                </div>
                                @if($patient->outlet && $patient->outlet->city)
                                    <p class="text-xs text-slate-500 mt-1">{{ $patient->outlet->city }}</p>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                @php
                                    $thisMonth = $patient->results()->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count();
                                    $total = $patient->results()->count();
                                @endphp
                                <div class="flex flex-col items-center gap-1">
                                    <span class="text-lg font-bold text-slate-900">{{ $total }}</span>
                                    <span class="text-xs text-slate-500">{{ $thisMonth }} bulan ini</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @php
                                    $latestResult = $patient->results()->latest()->first();
                                @endphp
                                @if($latestResult)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium {{ $latestResult->type === 'skb' ? 'bg-green-100 text-green-700' : 'bg-orange-100 text-orange-700' }}">
                                        @if($latestResult->type === 'skb')
                                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            Sehat
                                        @else
                                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                                            </svg>
                                            Sakit
                                        @endif
                                    </span>
                                    <p class="text-xs text-slate-500 mt-1">{{ $latestResult->created_at->diffForHumans() }}</p>
                                @else
                                    <span class="text-sm text-slate-400">Belum ada data</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <button onclick="viewPatientDetail({{ $patient->id }})" class="group relative inline-flex items-center justify-center w-8 h-8 text-slate-600 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-all duration-200">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        <div class="absolute -top-8 left-1/2 transform -translate-x-1/2 bg-slate-900 text-white text-xs rounded px-2 py-1 opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap">
                                            Lihat Detail
                                        </div>
                                    </button>
                                    <button onclick="viewMedicalHistory({{ $patient->id }})" class="group relative inline-flex items-center justify-center w-8 h-8 text-slate-600 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition-all duration-200">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" />
                                        </svg>
                                        <div class="absolute -top-8 left-1/2 transform -translate-x-1/2 bg-slate-900 text-white text-xs rounded px-2 py-1 opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap">
                                            Riwayat Medical
                                        </div>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mb-4">
                                        <svg class="w-8 h-8 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.003c0 1.113.285 2.16.786 3.07m-9.328-1.635a9.38 9.38 0 012.625-.372 9.337 9.337 0 014.121.952 4.125 4.125 0 01-7.533 2.493m-3.328 1.635c1.256-.368 2.355-.936 3.328-1.635m0 0a13.792 13.792 0 00-3.328-1.635" />
                                        </svg>
                                    </div>
                                    <h3 class="text-lg font-semibold text-slate-900 mb-2">Belum Ada Data Karyawan</h3>
                                    <p class="text-slate-500 max-w-sm text-center">Belum ada karyawan yang terdaftar di sistem untuk perusahaan ini.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($patients->hasPages())
            <div class="px-6 py-4 border-t border-slate-200 bg-slate-50">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-slate-600">
                        Menampilkan {{ $patients->firstItem() }} hingga {{ $patients->lastItem() }} dari {{ $patients->total() }} karyawan
                    </div>
                    <div class="flex items-center gap-2">
                        {{ $patients->links('pagination::tailwind') }}
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

{{-- Patient Detail Modal --}}
<div id="patientDetailModal" class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm hidden z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-hidden">
        <div class="flex items-center justify-between p-6 border-b border-slate-200">
            <h3 class="text-xl font-bold text-slate-900">Detail Karyawan</h3>
            <button onclick="closeModal()" class="w-8 h-8 flex items-center justify-center text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-lg transition-colors">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <div id="modalContent" class="p-6 overflow-y-auto max-h-[calc(90vh-120px)]">
            {{-- Content will be loaded here --}}
        </div>
    </div>
</div>

{{-- Medical History Modal --}}
<div id="medicalHistoryModal" class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm hidden z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-hidden">
        <div class="flex items-center justify-between p-6 border-b border-slate-200">
            <h3 class="text-xl font-bold text-slate-900">Riwayat Medical Check-up</h3>
            <button onclick="closeMedicalModal()" class="w-8 h-8 flex items-center justify-center text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-lg transition-colors">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <div id="medicalModalContent" class="p-6 overflow-y-auto max-h-[calc(90vh-120px)]">
            {{-- Content will be loaded here --}}
        </div>
    </div>
</div>

{{-- Loading Spinner --}}
<div id="loadingSpinner" class="fixed inset-0 bg-black bg-opacity-25 backdrop-blur-sm hidden z-50 flex items-center justify-center">
    <div class="bg-white rounded-2xl p-8 shadow-2xl">
        <div class="flex items-center gap-4">
            <div class="animate-spin rounded-full h-8 w-8 border-4 border-blue-600 border-t-transparent"></div>
            <span class="text-slate-700 font-medium">Memuat data...</span>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Search functionality
    const searchInput = document.getElementById('searchInput');
    const genderFilter = document.getElementById('genderFilter');
    const outletFilter = document.getElementById('outletFilter');
    const resetFilters = document.getElementById('resetFilters');
    const refreshData = document.getElementById('refreshData');
    
    let searchTimeout;
    
    function performSearch() {
        const searchTerm = searchInput.value.toLowerCase();
        const selectedGender = genderFilter.value;
        const selectedOutlet = outletFilter.value;
        
        const rows = document.querySelectorAll('#tableBody tr[data-patient-id]');
        let visibleCount = 0;
        
        rows.forEach(row => {
            const name = row.querySelector('td:first-child p').textContent.toLowerCase();
            const gender = row.querySelector('td:nth-child(3) span').textContent.includes('Laki-laki') ? 'L' : 'P';
            const outletText = row.querySelector('td:nth-child(5) span').textContent;
            
            let showRow = true;
            
            // Search filter
            if (searchTerm && !name.includes(searchTerm)) {
                showRow = false;
            }
            
            // Gender filter
            if (selectedGender && gender !== selectedGender) {
                showRow = false;
            }
            
            // Outlet filter
            if (selectedOutlet) {
                const outletId = Array.from(outletFilter.options).find(option => 
                    option.textContent === outletText
                )?.value;
                if (outletId !== selectedOutlet) {
                    showRow = false;
                }
            }
            
            row.style.display = showRow ? '' : 'none';
            if (showRow) visibleCount++;
        });
        
        document.getElementById('totalCount').textContent = visibleCount.toLocaleString();
    }
    
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(performSearch, 300);
    });
    
    genderFilter.addEventListener('change', performSearch);
    outletFilter.addEventListener('change', performSearch);
    
    resetFilters.addEventListener('click', function() {
        searchInput.value = '';
        genderFilter.value = '';
        outletFilter.value = '';
        performSearch();
    });
    
    refreshData.addEventListener('click', function() {
        showLoading();
        setTimeout(() => {
            window.location.reload();
        }, 1000);
    });
    
    // Export functionality
    document.getElementById('exportBtn').addEventListener('click', function() {
        const csv = generateCSV();
        downloadCSV(csv, 'data-karyawan-{{ $company->name }}.csv');
    });
    
    // Table sorting
    document.querySelectorAll('[data-sort]').forEach(header => {
        header.addEventListener('click', function() {
            const sortBy = this.dataset.sort;
            sortTable(sortBy);
        });
    });
});

function showLoading() {
    document.getElementById('loadingSpinner').classList.remove('hidden');
}

function hideLoading() {
    document.getElementById('loadingSpinner').classList.add('hidden');
}

function viewPatientDetail(patientId) {
    showLoading();
    
    // Simulate API call
    setTimeout(() => {
        hideLoading();
        
        const modalContent = document.getElementById('modalContent');
        modalContent.innerHTML = `
            <div class="space-y-6">
                <div class="flex items-center gap-4 p-4 bg-slate-50 rounded-xl">
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center">
                        <span class="text-xl font-bold text-blue-700">JD</span>
                    </div>
                    <div>
                        <h4 class="text-xl font-bold text-slate-900">John Doe</h4>
                        <p class="text-slate-600">ID: EMP001</p>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <h5 class="font-semibold text-slate-900 border-b pb-2">Informasi Personal</h5>
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-slate-600">NIK:</span>
                                <span class="font-medium">1234567890123456</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-600">Gender:</span>
                                <span class="font-medium">Laki-laki</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-600">Tanggal Lahir:</span>
                                <span class="font-medium">15 Januari 1990</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-600">Telepon:</span>
                                <span class="font-medium">+62 812 3456 7890</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="space-y-4">
                        <h5 class="font-semibold text-slate-900 border-b pb-2">Informasi Kerja</h5>
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-slate-600">Outlet:</span>
                                <span class="font-medium">Klinik Jakarta Pusat</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-600">Status:</span>
                                <span class="inline-flex items-center gap-1 px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs font-medium">
                                    <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                                    Aktif
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-600">Bergabung:</span>
                                <span class="font-medium">1 Januari 2023</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="p-4 bg-blue-50 rounded-xl">
                    <h5 class="font-semibold text-blue-900 mb-2">Statistik Medical Check-up</h5>
                    <div class="grid grid-cols-3 gap-4 text-center">
                        <div>
                            <p class="text-2xl font-bold text-blue-700">12</p>
                            <p class="text-xs text-blue-600">Total Check-up</p>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-green-700">10</p>
                            <p class="text-xs text-green-600">Surat Sehat</p>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-orange-700">2</p>
                            <p class="text-xs text-orange-600">Surat Sakit</p>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        document.getElementById('patientDetailModal').classList.remove('hidden');
    }, 1000);
}

function viewMedicalHistory(patientId) {
    showLoading();
    
    // Simulate API call
    setTimeout(() => {
        hideLoading();
        
        const modalContent = document.getElementById('medicalModalContent');
        modalContent.innerHTML = `
            <div class="space-y-6">
                <div class="flex items-center justify-between">
                    <h4 class="text-lg font-semibold text-slate-900">John Doe - Riwayat Medical Check-up</h4>
                    <span class="text-sm text-slate-500">Total: 12 pemeriksaan</span>
                </div>
                
                <div class="space-y-4">
                    <div class="border border-slate-200 rounded-xl p-4 hover:shadow-md transition-shadow">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-semibold text-slate-900">Surat Keterangan Sehat</p>
                                    <p class="text-sm text-slate-600">Dr. Ahmad Susanto</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-medium text-slate-900">5 Agustus 2025</p>
                                <p class="text-xs text-slate-500">2 hari yang lalu</p>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="text-slate-600">Outlet:</span>
                                <span class="font-medium ml-2">Klinik Jakarta Pusat</span>
                            </div>
                            <div>
                                <span class="text-slate-600">Tujuan:</span>
                                <span class="font-medium ml-2">Medical Check-up Rutin</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="border border-slate-200 rounded-xl p-4 hover:shadow-md transition-shadow">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-orange-100 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 text-orange-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-semibold text-slate-900">Surat Keterangan Sakit</p>
                                    <p class="text-sm text-slate-600">Dr. Sarah Williams</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-medium text-slate-900">28 Juli 2025</p>
                                <p class="text-xs text-slate-500">1 minggu yang lalu</p>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="text-slate-600">Outlet:</span>
                                <span class="font-medium ml-2">Klinik Jakarta Pusat</span>
                            </div>
                            <div>
                                <span class="text-slate-600">Diagnosa:</span>
                                <span class="font-medium ml-2">Flu & Demam</span>
                            </div>
                        </div>
                        <div class="mt-3 p-3 bg-orange-50 rounded-lg">
                            <p class="text-sm text-orange-800"><strong>Istirahat:</strong> 2 hari</p>
                        </div>
                    </div>
                </div>
                
                <div class="text-center pt-4">
                    <button class="text-blue-600 hover:text-blue-700 font-medium text-sm">Lihat Riwayat Lengkap</button>
                </div>
            </div>
        `;
        
        document.getElementById('medicalHistoryModal').classList.remove('hidden');
    }, 1000);
}

function closeModal() {
    document.getElementById('patientDetailModal').classList.add('hidden');
}

function closeMedicalModal() {
    document.getElementById('medicalHistoryModal').classList.add('hidden');
}

function generateCSV() {
    const rows = document.querySelectorAll('#tableBody tr[data-patient-id]');
    let csv = 'Nama,NIK,Gender,Telepon,Outlet,Total Medical Check-up,Status Terakhir\n';
    
    rows.forEach(row => {
        if (row.style.display !== 'none') {
            const cells = row.querySelectorAll('td');
            const name = cells[0].querySelector('p').textContent;
            const nik = cells[1].textContent.trim();
            const gender = cells[2].querySelector('span').textContent;
            const phone = cells[3].querySelector('p').textContent;
            const outlet = cells[4].querySelector('span').textContent;
            const totalChecks = cells[5].querySelector('.text-lg').textContent;
            const lastStatus = cells[6].querySelector('span')?.textContent || 'Belum ada data';
            
            csv += `"${name}","${nik}","${gender}","${phone}","${outlet}","${totalChecks}","${lastStatus}"\n`;
        }
    });
    
    return csv;
}

function downloadCSV(csv, filename) {
    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    link.setAttribute('href', url);
    link.setAttribute('download', filename);
    link.style.visibility = 'hidden';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

function sortTable(column) {
    const tbody = document.getElementById('tableBody');
    const rows = Array.from(tbody.querySelectorAll('tr[data-patient-id]'));
    
    rows.sort((a, b) => {
        let aVal, bVal;
        
        switch(column) {
            case 'full_name':
                aVal = a.querySelector('td:first-child p').textContent;
                bVal = b.querySelector('td:first-child p').textContent;
                break;
            case 'nik':
                aVal = a.querySelector('td:nth-child(2)').textContent.trim();
                bVal = b.querySelector('td:nth-child(2)').textContent.trim();
                break;
            default:
                return 0;
        }
        
        return aVal.localeCompare(bVal);
    });
    
    rows.forEach(row => tbody.appendChild(row));
}

// Close modals when clicking outside
document.getElementById('patientDetailModal').addEventListener('click', function(e) {
    if (e.target === this) closeModal();
});

document.getElementById('medicalHistoryModal').addEventListener('click', function(e) {
    if (e.target === this) closeMedicalModal();
});
</script>

<style>
/* Custom scrollbar for modals */
#modalContent::-webkit-scrollbar,
#medicalModalContent::-webkit-scrollbar {
    width: 6px;
}

#modalContent::-webkit-scrollbar-track,
#medicalModalContent::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 3px;
}

#modalContent::-webkit-scrollbar-thumb,
#medicalModalContent::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 3px;
}

#modalContent::-webkit-scrollbar-thumb:hover,
#medicalModalContent::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

/* Animation for table rows */
#tableBody tr {
    transition: all 0.2s ease-in-out;
}

#tableBody tr:hover {
    transform: translateY(-1px);
}

/* Smooth modal animations */
#patientDetailModal,
#medicalHistoryModal {
    animation: modalFadeIn 0.3s ease-out;
}

@keyframes modalFadeIn {
    from {
        opacity: 0;
        transform: scale(0.95);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}

/* Loading animation */
@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}

.animate-spin {
    animation: spin 1s linear infinite;
}
</style>
@endsection