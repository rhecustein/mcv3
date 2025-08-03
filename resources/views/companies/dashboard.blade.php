@extends('layouts.app')

@section('title', 'Dashboard Perusahaan')

@section('content')
<div class="max-w-7xl mx-auto space-y-8">
    {{-- Header Section dengan Gradient --}}
    <div class="relative overflow-hidden bg-gradient-to-br from-blue-600 via-blue-700 to-blue-800 rounded-2xl shadow-2xl">
        <div class="absolute inset-0 bg-black/10 backdrop-blur-sm"></div>
        <div class="absolute top-0 right-0 w-96 h-96 bg-white/5 rounded-full -translate-y-48 translate-x-48"></div>
        <div class="absolute bottom-0 left-0 w-64 h-64 bg-white/5 rounded-full translate-y-32 -translate-x-32"></div>
        
        <div class="relative z-10 p-8">
            <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6">
                <div class="space-y-3">
                    <div class="flex items-center gap-4">
                        <div class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center">
                            <svg class="w-9 h-9 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h6M9 11.25h6M9 15.75h6M9 20.25h6" />
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-4xl font-bold text-white">Dashboard {{ $company->name }}</h1>
                            <p class="text-blue-100 text-lg">Monitor kesehatan karyawan dan statistik medical check-up</p>
                        </div>
                    </div>
                </div>
                
                <div class="flex items-center gap-3">
                    <a href="{{ route('company.reports.index') }}" class="group inline-flex items-center gap-3 px-6 py-3 bg-white/90 backdrop-blur-sm text-blue-700 rounded-xl font-semibold hover:bg-white hover:scale-105 transition-all duration-300 shadow-lg">
                        <svg class="w-5 h-5 group-hover:rotate-12 transition-transform" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                        </svg>
                        Laporan Kesehatan
                    </a>
                    <a href="{{ route('company.patients.index') }}" class="group inline-flex items-center gap-3 px-6 py-3 bg-white/20 backdrop-blur-sm text-white border border-white/30 rounded-xl font-semibold hover:bg-white/30 hover:scale-105 transition-all duration-300">
                        <svg class="w-5 h-5 group-hover:rotate-12 transition-transform" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.003c0 1.113.285 2.16.786 3.07m-9.328-1.635a9.38 9.38 0 012.625-.372 9.337 9.337 0 014.121.952 4.125 4.125 0 01-7.533 2.493m-3.328 1.635c1.256-.368 2.355-.936 3.328-1.635m0 0a13.792 13.792 0 00-3.328-1.635" />
                        </svg>
                        Data Karyawan
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Enhanced Statistics Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        {{-- Total Karyawan --}}
        <div class="group relative overflow-hidden bg-white rounded-2xl shadow-lg border border-slate-200 p-6 hover:shadow-2xl hover:-translate-y-2 transition-all duration-500">
            <div class="absolute inset-0 bg-gradient-to-br from-blue-50 to-blue-100 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-14 h-14 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-7 h-7 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.003c0 1.113.285 2.16.786 3.07m-9.328-1.635a9.38 9.38 0 012.625-.372 9.337 9.337 0 014.121.952 4.125 4.125 0 01-7.533 2.493m-3.328 1.635c1.256-.368 2.355-.936 3.328-1.635m0 0a13.792 13.792 0 00-3.328-1.635" />
                        </svg>
                    </div>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                        Active
                    </span>
                </div>
                <div>
                    <p class="text-sm font-medium text-slate-600 mb-1">Total Karyawan</p>
                    <p class="text-3xl font-bold text-slate-900 mb-1">{{ number_format($stats['total_patients']) }}</p>
                    <p class="text-xs text-slate-500">Terdaftar di sistem</p>
                </div>
            </div>
            <div class="absolute -top-4 -right-4 w-20 h-20 bg-blue-500/10 rounded-full"></div>
        </div>

        {{-- Total Medical Check-up --}}
        <div class="group relative overflow-hidden bg-white rounded-2xl shadow-lg border border-slate-200 p-6 hover:shadow-2xl hover:-translate-y-2 transition-all duration-500">
            <div class="absolute inset-0 bg-gradient-to-br from-emerald-50 to-emerald-100 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-14 h-14 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-2xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-7 h-7 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                        +{{ $stats['this_month_checks'] }}
                    </span>
                </div>
                <div>
                    <p class="text-sm font-medium text-slate-600 mb-1">Total Medical Check-up</p>
                    <p class="text-3xl font-bold text-slate-900 mb-1">{{ number_format($stats['total_health_checks']) }}</p>
                    <p class="text-xs text-slate-500">{{ $stats['this_month_checks'] }} bulan ini</p>
                </div>
            </div>
            <div class="absolute -top-4 -right-4 w-20 h-20 bg-emerald-500/10 rounded-full"></div>
        </div>

        {{-- Surat Sehat --}}
        <div class="group relative overflow-hidden bg-white rounded-2xl shadow-lg border border-slate-200 p-6 hover:shadow-2xl hover:-translate-y-2 transition-all duration-500">
            <div class="absolute inset-0 bg-gradient-to-br from-green-50 to-green-100 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-14 h-14 bg-gradient-to-br from-green-500 to-green-600 rounded-2xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-7 h-7 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 18.75a6 6 0 006-6v-1.5m-6 7.5a6 6 0 01-6-6v-1.5m6 7.5v3.75m-3.75 0h7.5M12 15.75a3 3 0 01-3-3V4.5a3 3 0 116 0v8.25a3 3 0 01-3 3z" />
                        </svg>
                    </div>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        {{ $healthSummary['healthy_percentage'] }}%
                    </span>
                </div>
                <div>
                    <p class="text-sm font-medium text-slate-600 mb-1">Surat Sehat</p>
                    <p class="text-3xl font-bold text-slate-900 mb-1">{{ number_format($stats['skb_count']) }}</p>
                    <p class="text-xs text-slate-500">{{ $healthSummary['healthy_percentage'] }}% dari total</p>
                </div>
            </div>
            <div class="absolute -top-4 -right-4 w-20 h-20 bg-green-500/10 rounded-full"></div>
        </div>

        {{-- Surat Sakit --}}
        <div class="group relative overflow-hidden bg-white rounded-2xl shadow-lg border border-slate-200 p-6 hover:shadow-2xl hover:-translate-y-2 transition-all duration-500">
            <div class="absolute inset-0 bg-gradient-to-br from-orange-50 to-orange-100 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-14 h-14 bg-gradient-to-br from-orange-500 to-orange-600 rounded-2xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-7 h-7 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                        </svg>
                    </div>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                        {{ $healthSummary['sick_percentage'] }}%
                    </span>
                </div>
                <div>
                    <p class="text-sm font-medium text-slate-600 mb-1">Surat Sakit</p>
                    <p class="text-3xl font-bold text-slate-900 mb-1">{{ number_format($stats['mc_count']) }}</p>
                    <p class="text-xs text-slate-500">{{ $healthSummary['sick_percentage'] }}% dari total</p>
                </div>
            </div>
            <div class="absolute -top-4 -right-4 w-20 h-20 bg-orange-500/10 rounded-full"></div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Enhanced Chart Section --}}
        <div class="lg:col-span-2 bg-white/90 backdrop-blur-xl rounded-2xl shadow-xl border border-slate-200 p-8">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h3 class="text-xl font-bold text-slate-900 flex items-center gap-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
                            </svg>
                        </div>
                        Tren Medical Check-up
                    </h3>
                    <p class="text-sm text-slate-600 mt-1">12 Bulan Terakhir</p>
                </div>
                <div class="flex gap-2">
                    <button class="p-2 rounded-lg bg-slate-100 hover:bg-slate-200 transition-colors">
                        <svg class="w-4 h-4 text-slate-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                        </svg>
                    </button>
                </div>
            </div>
            <div class="relative">
                <canvas id="healthCheckChart" class="w-full h-80"></canvas>
            </div>
        </div>

        {{-- Enhanced Health Summary --}}
        <div class="space-y-6">
            {{-- Health Status Overview --}}
            <div class="bg-white/90 backdrop-blur-xl rounded-2xl shadow-xl border border-slate-200 p-6">
                <h3 class="text-lg font-bold text-slate-900 mb-6 flex items-center gap-3">
                    <div class="w-8 h-8 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    Status Kesehatan
                </h3>
                
                <div class="space-y-6">
                    <div class="relative">
                        <div class="flex justify-between items-center mb-3">
                            <div class="flex items-center gap-2">
                                <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                                <span class="text-sm font-semibold text-green-700">Sehat</span>
                            </div>
                            <span class="text-lg font-bold text-green-800">{{ $healthSummary['healthy_percentage'] }}%</span>
                        </div>
                        <div class="w-full bg-green-100 rounded-full h-3 overflow-hidden">
                            <div class="bg-gradient-to-r from-green-400 to-green-500 h-3 rounded-full transition-all duration-1000 ease-out shadow-lg" style="width: {{ $healthSummary['healthy_percentage'] }}%"></div>
                        </div>
                    </div>
                    
                    <div class="relative">
                        <div class="flex justify-between items-center mb-3">
                            <div class="flex items-center gap-2">
                                <div class="w-3 h-3 bg-orange-500 rounded-full"></div>
                                <span class="text-sm font-semibold text-orange-700">Sakit</span>
                            </div>
                            <span class="text-lg font-bold text-orange-800">{{ $healthSummary['sick_percentage'] }}%</span>
                        </div>
                        <div class="w-full bg-orange-100 rounded-full h-3 overflow-hidden">
                            <div class="bg-gradient-to-r from-orange-400 to-orange-500 h-3 rounded-full transition-all duration-1000 ease-out shadow-lg" style="width: {{ $healthSummary['sick_percentage'] }}%"></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Enhanced Gender Distribution --}}
            <div class="bg-white/90 backdrop-blur-xl rounded-2xl shadow-xl border border-slate-200 p-6">
                <h3 class="text-lg font-bold text-slate-900 mb-6 flex items-center gap-3">
                    <div class="w-8 h-8 bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.003c0 1.113.285 2.16.786 3.07m-9.328-1.635a9.38 9.38 0 012.625-.372 9.337 9.337 0 014.121.952 4.125 4.125 0 01-7.533 2.493m-3.328 1.635c1.256-.368 2.355-.936 3.328-1.635m0 0a13.792 13.792 0 00-3.328-1.635" />
                        </svg>
                    </div>
                    Distribusi Gender
                </h3>
                
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-4 bg-gradient-to-r from-blue-50 to-blue-100 rounded-xl">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2a10 10 0 1010 10A10 10 0 0012 2zm0 18a8 8 0 118-8 8 8 0 01-8 8z"/>
                                    <path d="M12 6a6 6 0 106 6 6 6 0 00-6-6zm0 10a4 4 0 114-4 4 4 0 01-4 4z"/>
                                </svg>
                            </div>
                            <span class="font-semibold text-blue-900">Laki-laki</span>
                        </div>
                        <span class="text-xl font-bold text-blue-800">{{ number_format($stats['male_patients']) }}</span>
                    </div>
                    
                    <div class="flex items-center justify-between p-4 bg-gradient-to-r from-pink-50 to-pink-100 rounded-xl">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-pink-500 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2a10 10 0 1010 10A10 10 0 0012 2zm0 18a8 8 0 118-8 8 8 0 01-8 8z"/>
                                    <circle cx="12" cy="12" r="3"/>
                                </svg>
                            </div>
                            <span class="font-semibold text-pink-900">Perempuan</span>
                        </div>
                        <span class="text-xl font-bold text-pink-800">{{ number_format($stats['female_patients']) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        {{-- Enhanced Recent Activity --}}
        <div class="bg-white/90 backdrop-blur-xl rounded-2xl shadow-xl border border-slate-200 p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-bold text-slate-900 flex items-center gap-3">
                    <div class="w-8 h-8 bg-gradient-to-br from-slate-500 to-slate-600 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    Aktivitas Terbaru
                </h3>
                <button class="text-sm text-blue-600 hover:text-blue-700 font-medium">Lihat Semua</button>
            </div>
            
            <div class="space-y-3 max-h-96 overflow-y-auto">
                @forelse($recentResults as $result)
                    <div class="group flex items-center gap-4 p-4 bg-slate-50 hover:bg-slate-100 rounded-xl transition-all duration-200 hover:shadow-md">
                        <div class="w-12 h-12 rounded-full {{ $result->type === 'skb' ? 'bg-green-100' : 'bg-orange-100' }} flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
                            @if($result->type === 'skb')
                                <svg class="w-6 h-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            @else
                                <svg class="w-6 h-6 text-orange-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                                </svg>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-slate-900 truncate">{{ $result->patient->full_name }}</p>
                            <p class="text-sm text-slate-600">
                                {{ $result->type === 'skb' ? 'Surat Sehat' : 'Surat Sakit' }} • 
                                {{ $result->outlet->name ?? 'Unknown Outlet' }}
                            </p>
                        </div>
                        <div class="text-xs text-slate-400 flex-shrink-0 font-medium">
                            {{ $result->created_at->diffForHumans() }}
                        </div>
                    </div>
                @empty
                    <div class="text-center py-12">
                        <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" />
                            </svg>
                        </div>
                        <p class="text-slate-500 font-medium">Belum ada aktivitas medical check-up</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Enhanced Top Outlets --}}
        <div class="bg-white/90 backdrop-blur-xl rounded-2xl shadow-xl border border-slate-200 p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-bold text-slate-900 flex items-center gap-3">
                    <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                        </svg>
                    </div>
                    Outlet Partner Utama
                </h3>
                <button class="text-sm text-blue-600 hover:text-blue-700 font-medium">Lihat Semua</button>
            </div>
            
            <div class="space-y-3">
                @forelse($topOutlets as $index => $outlet)
                    <div class="group flex items-center gap-4 p-4 bg-slate-50 hover:bg-slate-100 rounded-xl transition-all duration-200 hover:shadow-md">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
                            <span class="text-sm font-bold text-white">#{{ $index + 1 }}</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-slate-900 truncate">{{ $outlet->name }}</p>
                            <p class="text-sm text-slate-600">{{ $outlet->city }}</p>
                        </div>
                        <div class="text-right flex-shrink-0">
                            <p class="text-lg font-bold text-blue-700">{{ $outlet->patient_count }}</p>
                            <p class="text-xs text-slate-500 font-medium">karyawan</p>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-12">
                        <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                            </svg>
                        </div>
                        <p class="text-slate-500 font-medium">Belum ada data outlet partner</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Enhanced Company Info Card --}}
    <div class="bg-gradient-to-br from-slate-50 via-white to-slate-50 border border-slate-200 rounded-2xl p-8 shadow-lg">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
            <div>
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 bg-gradient-to-br from-slate-600 to-slate-700 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900">Informasi Perusahaan</h3>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-white/70 backdrop-blur-sm rounded-xl p-4 border border-slate-200">
                        <div class="flex items-center gap-2 mb-2">
                            <svg class="w-4 h-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 00.75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 00-3.413-.387m4.5 8.054c-.003.097-.01.193-.024.287M15.75 7.5V5.25A2.25 2.25 0 0013.5 3h-3a2.25 2.25 0 00-2.25 2.25v2.25m6 0V4.875c0-.621-.504-1.125-1.125-1.125h-2.25c-.621 0-1.125.504-1.125 1.125V7.5" />
                            </svg>
                            <span class="text-sm font-medium text-slate-600">Industri</span>
                        </div>
                        <span class="text-lg font-bold text-slate-900">{{ $company->industry_type ?? 'Tidak diketahui' }}</span>
                    </div>
                    <div class="bg-white/70 backdrop-blur-sm rounded-xl p-4 border border-slate-200">
                        <div class="flex items-center gap-2 mb-2">
                            <svg class="w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 8.25h15m-16.5 7.5h15m-1.8-13.5l-3.9 19.5m-2.1-19.5l-3.9 19.5" />
                            </svg>
                            <span class="text-sm font-medium text-slate-600">Kode</span>
                        </div>
                        <span class="text-lg font-bold text-slate-900">{{ $company->code ?? '-' }}</span>
                    </div>
                    <div class="bg-white/70 backdrop-blur-sm rounded-xl p-4 border border-slate-200">
                        <div class="flex items-center gap-2 mb-2">
                            <svg class="w-4 h-4 text-purple-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                            </svg>
                            <span class="text-sm font-medium text-slate-600">Outlet Aktif</span>
                        </div>
                        <span class="text-lg font-bold text-slate-900">{{ $stats['active_outlets'] }} lokasi</span>
                    </div>
                </div>
            </div>
            <div class="flex flex-col items-end gap-3">
                <span class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold rounded-xl {{ $company->is_active ? 'bg-emerald-100 text-emerald-700 border border-emerald-200' : 'bg-red-100 text-red-700 border border-red-200' }}">
                    <span class="h-2 w-2 rounded-full {{ $company->is_active ? 'bg-emerald-500 animate-pulse' : 'bg-red-500' }}"></span>
                    {{ $company->is_active ? 'Status Aktif' : 'Status Non-aktif' }}
                </span>
                <div class="text-right">
                    <p class="text-xs text-slate-500">Terakhir diperbarui</p>
                    <p class="text-sm font-medium text-slate-700">{{ now()->format('d M Y, H:i') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Enhanced Chart.js Script --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Enhanced Health Check Chart
    const ctx = document.getElementById('healthCheckChart').getContext('2d');
    const chartData = @json($chartData);
    
    // Create gradient
    const gradient = ctx.createLinearGradient(0, 0, 0, 400);
    gradient.addColorStop(0, 'rgba(59, 130, 246, 0.8)');
    gradient.addColorStop(0.5, 'rgba(59, 130, 246, 0.4)');
    gradient.addColorStop(1, 'rgba(59, 130, 246, 0.1)');
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: chartData.labels,
            datasets: [{
                label: 'Medical Check-up',
                data: chartData.data,
                borderColor: '#3B82F6',
                backgroundColor: gradient,
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#3B82F6',
                pointBorderColor: '#ffffff',
                pointBorderWidth: 3,
                pointRadius: 6,
                pointHoverRadius: 10,
                pointHoverBackgroundColor: '#1D4ED8',
                pointHoverBorderColor: '#ffffff',
                pointHoverBorderWidth: 3,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                intersect: false,
                mode: 'index'
            },
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(255, 255, 255, 0.95)',
                    titleColor: '#1e293b',
                    bodyColor: '#475569',
                    borderColor: '#e2e8f0',
                    borderWidth: 1,
                    cornerRadius: 12,
                    padding: 12,
                    boxShadow: '0 10px 15px -3px rgb(0 0 0 / 0.1)',
                    displayColors: false,
                    callbacks: {
                        title: function(context) {
                            return 'Bulan ' + context[0].label;
                        },
                        label: function(context) {
                            return context.parsed.y + ' medical check-up';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(148, 163, 184, 0.1)',
                        drawBorder: false
                    },
                    ticks: {
                        stepSize: 1,
                        color: '#64748b',
                        font: {
                            size: 12,
                            weight: '500'
                        },
                        padding: 10
                    }
                },
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        color: '#64748b',
                        font: {
                            size: 12,
                            weight: '500'
                        },
                        padding: 10
                    }
                }
            },
            elements: {
                point: {
                    hoverBackgroundColor: '#1D4ED8'
                }
            }
        }
    });

    // Add loading animation for stats cards
    const statsCards = document.querySelectorAll('.group[class*="hover:-translate-y-2"]');
    statsCards.forEach((card, index) => {
        setTimeout(() => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            card.style.transition = 'all 0.6s ease-out';
            
            setTimeout(() => {
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, 100);
        }, index * 100);
    });

    // Auto refresh data every 5 minutes with notification
    setInterval(function() {
        // Show subtle refresh indicator
        const refreshIndicator = document.createElement('div');
        refreshIndicator.className = 'fixed top-4 right-4 bg-blue-600 text-white px-4 py-2 rounded-lg shadow-lg z-50 transition-all duration-300';
        refreshIndicator.innerHTML = `
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                <span class="text-sm font-medium">Memperbarui data...</span>
            </div>
        `;
        document.body.appendChild(refreshIndicator);
        
        setTimeout(() => {
            window.location.reload();
        }, 1000);
    }, 300000);
});

// Add smooth scrolling for anchor links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        document.querySelector(this.getAttribute('href')).scrollIntoView({
            behavior: 'smooth'
        });
    });
});
</script>

{{-- Add custom CSS for enhanced animations --}}
<style>
@keyframes slideInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes fadeInScale {
    from {
        opacity: 0;
        transform: scale(0.95);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}

.animate-slideInUp {
    animation: slideInUp 0.6s ease-out;
}

.animate-fadeInScale {
    animation: fadeInScale 0.4s ease-out;
}

/* Custom scrollbar for activity feed */
.max-h-96::-webkit-scrollbar {
    width: 6px;
}

.max-h-96::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 3px;
}

.max-h-96::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 3px;
}

.max-h-96::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

/* Enhanced glassmorphism effect */
.bg-white\/90 {
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
}

/* Progress bar animation */
.progress-bar {
    animation: progressFill 2s ease-out forwards;
}

@keyframes progressFill {
    from {
        width: 0%;
    }
}
</style>
@endsection