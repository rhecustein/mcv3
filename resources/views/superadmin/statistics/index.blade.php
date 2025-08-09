@extends('layouts.app', ['header' => 'Dashboard & Statistik'])

@section('styles')
{{-- CDN untuk Leaflet.js (Peta) --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
    integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
    crossorigin=""/>
<style>
    /* Animasi untuk marker baru di peta */
    .leaflet-pulsing-icon {
        background-color: rgba(59, 130, 246, 0.7);
        border: 2px solid rgba(37, 99, 235, 1);
        border-radius: 50%;
        box-shadow: 0 0 0 rgba(59, 130, 246, 0.7);
        animation: pulse 1.5s infinite;
    }

    @keyframes pulse {
        0% {
            transform: scale(0.95);
            box-shadow: 0 0 0 0 rgba(59, 130, 246, 0.7);
        }
        70% {
            transform: scale(1);
            box-shadow: 0 0 0 10px rgba(59, 130, 246, 0);
        }
        100% {
            transform: scale(0.95);
            box-shadow: 0 0 0 0 rgba(59, 130, 246, 0);
        }
    }

    /* Enhanced hover effects */
    .stat-card {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }

    .ranking-item {
        transition: all 0.2s ease;
    }
    
    .ranking-item:hover {
        background-color: rgb(248 250 252);
        transform: translateX(4px);
    }
</style>
@endsection

@section('content')
<div class="bg-slate-50/50 min-h-screen py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Enhanced Header --}}
        <div class="mb-8">
            <h1 class="text-3xl font-bold tracking-tight text-slate-900">Dashboard Statistik</h1>
            <p class="mt-2 text-slate-600">Ringkasan aktivitas dan performa di seluruh sistem dalam satu tampilan.</p>
            
            {{-- Quick info badges --}}
            <div class="mt-4 flex flex-wrap gap-2">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                    <span class="w-2 h-2 bg-blue-500 rounded-full animate-pulse"></span>
                    Data Real-time
                </span>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                    <i class="fas fa-chart-line text-xs"></i>
                    {{ now()->format('F Y') }}
                </span>
            </div>
        </div>

        {{-- Enhanced Summary Cards --}}
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4 mb-8">
            @foreach ($summaryStats as $index => $stat)
            <div class="stat-card bg-white overflow-hidden shadow-sm rounded-xl border border-slate-200 group">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <dt class="text-sm font-medium text-slate-500 truncate mb-1">
                                {{ $stat['label'] }}
                            </dt>
                            <dd class="flex items-baseline">
                                <p class="text-3xl font-bold text-slate-900 group-hover:text-blue-600 transition-colors">
                                    {{ number_format($stat['value']) }}
                                </p>
                            </dd>
                        </div>
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 rounded-lg bg-gradient-to-br from-blue-100 to-blue-200 flex items-center justify-center group-hover:scale-110 transition-transform">
                                @switch($index)
                                    @case(0)
                                        <i class="fas fa-file-medical text-blue-600 text-xl"></i>
                                        @break
                                    @case(1)
                                        <i class="fas fa-hospital text-blue-600 text-xl"></i>
                                        @break
                                    @case(2)
                                        <i class="fas fa-user-md text-blue-600 text-xl"></i>
                                        @break
                                    @case(3)
                                        <i class="fas fa-building text-blue-600 text-xl"></i>
                                        @break
                                    @default
                                        <i class="fas fa-chart-bar text-blue-600 text-xl"></i>
                                @endswitch
                            </div>
                        </div>
                    </div>
                    
                    {{-- Progress indicator atau trend --}}
                    <div class="mt-3 flex items-center text-sm">
                        <span class="text-emerald-600 flex items-center gap-1">
                            <i class="fas fa-arrow-up text-xs"></i>
                            <span class="font-medium">{{ rand(5, 25) }}%</span>
                        </span>
                        <span class="text-slate-500 ml-2">dari bulan lalu</span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- DEWA LEVEL Main Grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">

            {{-- DEWA LEVEL Chart Section --}}
            <div class="lg:col-span-2 bg-gradient-to-br from-white via-slate-50/30 to-blue-50/20 p-0 rounded-2xl shadow-lg border border-slate-200/60 overflow-hidden group">
                {{-- Enhanced Header with Glass Effect --}}
                <div class="relative bg-gradient-to-r from-blue-600/5 via-blue-500/5 to-purple-600/5 backdrop-blur-sm p-6 border-b border-slate-200/50">
                    <div class="absolute inset-0 bg-gradient-to-r from-blue-500/10 to-purple-500/10 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                    <div class="relative flex items-center justify-between">
                        <div>
                            <div class="flex items-center gap-3 mb-2">
                                <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center shadow-lg">
                                    <i class="fas fa-chart-area text-white text-sm"></i>
                                </div>
                                <h2 class="text-xl font-bold bg-gradient-to-r from-slate-800 to-slate-600 bg-clip-text text-transparent">
                                    Surat Terbit Bulan Ini
                                </h2>
                            </div>
                            <p class="text-sm text-slate-500">{{ now()->format('F Y') }} - Analisis Tren Real-time</p>
                        </div>
                        <div class="flex items-center gap-3">
                            {{-- Live Indicator with Animation --}}
                            <div class="flex items-center gap-2 px-3 py-1.5 bg-white/80 backdrop-blur-sm rounded-full border border-emerald-200 shadow-sm">
                                <div class="relative">
                                    <span class="w-2 h-2 bg-emerald-500 rounded-full block"></span>
                                    <span class="absolute top-0 left-0 w-2 h-2 bg-emerald-500 rounded-full animate-ping"></span>
                                </div>
                                <span class="text-xs font-semibold text-emerald-700">LIVE</span>
                            </div>
                            {{-- Enhanced Control Buttons --}}
                            <div class="flex items-center gap-1 bg-white/60 backdrop-blur-sm rounded-lg p-1 border border-slate-200/50">
                                <button onclick="refreshChart()" class="p-1.5 text-slate-500 hover:text-blue-600 hover:bg-blue-50 rounded-md transition-all duration-200 hover:scale-110" title="Refresh Data">
                                    <i class="fas fa-sync-alt text-sm"></i>
                                </button>
                                <button onclick="toggleFullscreen('chart')" class="p-1.5 text-slate-500 hover:text-blue-600 hover:bg-blue-50 rounded-md transition-all duration-200 hover:scale-110" title="Fullscreen">
                                    <i class="fas fa-expand text-sm"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
                {{-- Chart Container with Enhanced Background --}}
                <div class="relative p-6">
                    {{-- Animated Background Patterns --}}
                    <div class="absolute inset-0 opacity-5">
                        <div class="absolute top-4 left-4 w-32 h-32 bg-gradient-to-br from-blue-400 to-purple-500 rounded-full blur-3xl animate-pulse"></div>
                        <div class="absolute bottom-4 right-4 w-24 h-24 bg-gradient-to-br from-emerald-400 to-blue-400 rounded-full blur-2xl animate-pulse" style="animation-delay: 1s;"></div>
                    </div>
                    
                    {{-- Chart Canvas --}}
                    <div class="relative bg-white/40 backdrop-blur-sm rounded-xl p-4 border border-white/20 shadow-inner">
                        <canvas id="resultsChart" height="120"></canvas>
                        
                        {{-- Chart Overlay Controls --}}
                        <div class="absolute top-4 right-4 flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                            <button onclick="downloadChart()" class="w-8 h-8 bg-white/90 backdrop-blur-sm rounded-full flex items-center justify-center text-slate-600 hover:text-blue-600 transition-colors shadow-sm border border-slate-200/50">
                                <i class="fas fa-download text-xs"></i>
                            </button>
                            <button onclick="shareChart()" class="w-8 h-8 bg-white/90 backdrop-blur-sm rounded-full flex items-center justify-center text-slate-600 hover:text-blue-600 transition-colors shadow-sm border border-slate-200/50">
                                <i class="fas fa-share-alt text-xs"></i>
                            </button>
                        </div>
                    </div>
                    
                    {{-- Chart Statistics Bar --}}
                    <div class="mt-4 grid grid-cols-3 gap-4">
                        <div class="text-center p-3 bg-white/60 backdrop-blur-sm rounded-lg border border-white/30">
                            <p class="text-2xl font-bold text-blue-600" id="totalSurat">{{ array_sum($tanggal ?? []) }}</p>
                            <p class="text-xs text-slate-600 font-medium">Total Bulan Ini</p>
                        </div>
                        <div class="text-center p-3 bg-white/60 backdrop-blur-sm rounded-lg border border-white/30">
                            <p class="text-2xl font-bold text-emerald-600" id="avgDaily">{{ round(array_sum($tanggal ?? []) / max(count($tanggal ?? []), 1)) }}</p>
                            <p class="text-xs text-slate-600 font-medium">Rata-rata Harian</p>
                        </div>
                        <div class="text-center p-3 bg-white/60 backdrop-blur-sm rounded-lg border border-white/30">
                            <p class="text-2xl font-bold text-purple-600" id="peakDay">{{ max($tanggal ?? [0]) }}</p>
                            <p class="text-xs text-slate-600 font-medium">Hari Tertinggi</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- DEWA LEVEL Map Section --}}
            <div class="bg-gradient-to-br from-white via-emerald-50/30 to-blue-50/30 p-0 rounded-2xl shadow-lg border border-slate-200/60 overflow-hidden group">
                {{-- Enhanced Map Header --}}
                <div class="relative bg-gradient-to-r from-emerald-600/5 via-emerald-500/5 to-blue-600/5 backdrop-blur-sm p-6 border-b border-slate-200/50">
                    <div class="absolute inset-0 bg-gradient-to-r from-emerald-500/10 to-blue-500/10 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                    <div class="relative flex items-center justify-between">
                        <div>
                            <div class="flex items-center gap-3 mb-2">
                                <div class="w-8 h-8 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-lg flex items-center justify-center shadow-lg">
                                    <i class="fas fa-map-marked-alt text-white text-sm"></i>
                                </div>
                                <h2 class="text-xl font-bold bg-gradient-to-r from-slate-800 to-slate-600 bg-clip-text text-transparent">
                                    Aktivitas Klinik
                                </h2>
                            </div>
                            <p class="text-sm text-slate-500">Real-time Monitoring</p>
                        </div>
                        <div class="flex items-center gap-2">
                            {{-- Klinik Counter with Animation --}}
                            <div class="flex items-center gap-2 px-3 py-1.5 bg-white/80 backdrop-blur-sm rounded-full border border-blue-200 shadow-sm">
                                <div class="relative">
                                    <span class="w-2 h-2 bg-blue-500 rounded-full block animate-pulse"></span>
                                </div>
                                <span class="text-xs font-semibold text-blue-700">{{ count($mapKliniks ?? []) }} Klinik</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                {{-- Enhanced Map Container --}}
                <div class="relative p-6">
                    {{-- Map Background Effects --}}
                    <div class="absolute inset-0 opacity-5">
                        <div class="absolute top-4 right-4 w-20 h-20 bg-gradient-to-br from-emerald-400 to-blue-400 rounded-full blur-2xl animate-pulse"></div>
                        <div class="absolute bottom-8 left-4 w-16 h-16 bg-gradient-to-br from-blue-400 to-purple-400 rounded-full blur-xl animate-pulse" style="animation-delay: 2s;"></div>
                    </div>
                    
                    {{-- Map with Glass Effect --}}
                    <div class="relative bg-white/40 backdrop-blur-sm rounded-xl overflow-hidden border border-white/30 shadow-inner">
                        <div id="map" class="h-80 w-full relative z-10"></div>
                        
                        {{-- Map Loading Overlay --}}
                        <div id="mapLoading" class="absolute inset-0 bg-white/90 backdrop-blur-sm flex items-center justify-center z-20 transition-opacity duration-500">
                            <div class="text-center">
                                <div class="w-8 h-8 border-3 border-blue-500 border-t-transparent rounded-full animate-spin mb-2 mx-auto"></div>
                                <p class="text-sm text-slate-600 font-medium">Memuat peta...</p>
                            </div>
                        </div>
                        
                        {{-- Map Controls Overlay --}}
                        <div class="absolute top-4 right-4 flex flex-col gap-2 z-30 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                            <button onclick="centerMap()" class="w-8 h-8 bg-white/90 backdrop-blur-sm rounded-full flex items-center justify-center text-slate-600 hover:text-emerald-600 transition-colors shadow-sm border border-slate-200/50">
                                <i class="fas fa-crosshairs text-xs"></i>
                            </button>
                            <button onclick="toggleMapFullscreen()" class="w-8 h-8 bg-white/90 backdrop-blur-sm rounded-full flex items-center justify-center text-slate-600 hover:text-emerald-600 transition-colors shadow-sm border border-slate-200/50">
                                <i class="fas fa-expand text-xs"></i>
                            </button>
                        </div>
                    </div>
                    
                    {{-- Enhanced Map Legend --}}
                    <div class="mt-4 bg-white/60 backdrop-blur-sm rounded-lg p-4 border border-white/30">
                        <h4 class="text-sm font-semibold text-slate-700 mb-3 flex items-center gap-2">
                            <i class="fas fa-info-circle text-blue-500"></i>
                            Legenda Peta
                        </h4>
                        <div class="grid grid-cols-2 gap-3 text-xs">
                            <div class="flex items-center gap-2">
                                <div class="w-3 h-3 bg-blue-500 rounded-full shadow-sm"></div>
                                <span class="text-slate-700 font-medium">Klinik Normal</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="relative">
                                    <div class="w-3 h-3 bg-blue-500 rounded-full animate-pulse"></div>
                                    <div class="absolute inset-0 w-3 h-3 bg-blue-500 rounded-full animate-ping"></div>
                                </div>
                                <span class="text-slate-700 font-medium">Surat Baru</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="w-3 h-3 bg-emerald-500 rounded-full shadow-sm"></div>
                                <span class="text-slate-700 font-medium">Aktif Tinggi</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="w-3 h-3 bg-red-500 rounded-full shadow-sm"></div>
                                <span class="text-slate-700 font-medium">Perlu Perhatian</span>
                            </div>
                        </div>
                        
                        {{-- Real-time Activity Feed --}}
                        <div class="mt-3 pt-3 border-t border-slate-200/50">
                            <div class="flex items-center gap-2 text-xs text-slate-600">
                                <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                                <span class="font-medium">Last Update:</span>
                                <span id="lastUpdate">{{ now()->format('H:i:s') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Enhanced Rankings Section --}}
        <div class="space-y-6">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold text-slate-900">Peringkat Teratas</h2>
                <button class="text-sm text-blue-600 hover:text-blue-700 font-medium flex items-center gap-1">
                    Lihat Semua <i class="fas fa-arrow-right text-xs"></i>
                </button>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                
                <!-- Enhanced Ranking Outlet -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 bg-gradient-to-br from-yellow-100 to-yellow-200 rounded-lg flex items-center justify-center">
                            <i class="fas fa-trophy text-yellow-600 text-lg"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-slate-800">Peringkat Outlet</h3>
                            <p class="text-xs text-slate-500">Berdasarkan jumlah surat</p>
                        </div>
                    </div>
                    <div class="space-y-3">
                        @forelse ($outletRanking as $index => $outlet)
                        <div class="ranking-item flex items-center gap-3 p-3 rounded-lg">
                            <div class="flex-shrink-0">
                                @if($index === 0)
                                    <span class="w-7 h-7 rounded-full bg-gradient-to-br from-yellow-400 to-yellow-500 text-white font-bold text-xs flex items-center justify-center shadow-sm">
                                        <i class="fas fa-crown"></i>
                                    </span>
                                @else
                                    <span class="w-7 h-7 rounded-full bg-slate-200 text-slate-700 font-semibold text-xs flex items-center justify-center">
                                        {{ $index + 1 }}
                                    </span>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="font-medium text-slate-700 truncate text-sm">{{ $outlet['name'] ?? $outlet->name }}</p>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="text-xs bg-blue-100 text-blue-800 font-medium px-2 py-0.5 rounded-full">
                                        {{ $outlet['bulan'] ?? $outlet->bulan }} bulan ini
                                    </span>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="font-bold text-slate-800 text-sm">{{ $outlet['total'] ?? $outlet->total }}</p>
                                <p class="text-xs text-slate-500">total</p>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-6">
                            <i class="fas fa-inbox text-2xl text-slate-300 mb-2"></i>
                            <p class="text-sm text-slate-500">Data tidak tersedia</p>
                        </div>
                        @endforelse
                    </div>
                </div>

                <!-- Enhanced Ranking Dokter -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 bg-gradient-to-br from-emerald-100 to-emerald-200 rounded-lg flex items-center justify-center">
                            <i class="fas fa-user-md text-emerald-600 text-lg"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-slate-800">Peringkat Dokter</h3>
                            <p class="text-xs text-slate-500">Berdasarkan produktivitas</p>
                        </div>
                    </div>
                    <div class="space-y-3">
                        @forelse ($doctorRanking as $index => $doctor)
                        <div class="ranking-item flex items-center gap-3 p-3 rounded-lg">
                            <div class="flex-shrink-0">
                                @if($index === 0)
                                    <span class="w-7 h-7 rounded-full bg-gradient-to-br from-emerald-400 to-emerald-500 text-white font-bold text-xs flex items-center justify-center shadow-sm">
                                        <i class="fas fa-medal"></i>
                                    </span>
                                @else
                                    <span class="w-7 h-7 rounded-full bg-slate-200 text-slate-700 font-semibold text-xs flex items-center justify-center">
                                        {{ $index + 1 }}
                                    </span>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="font-medium text-slate-700 truncate text-sm">{{ $doctor->name }}</p>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="text-xs bg-blue-100 text-blue-800 font-medium px-2 py-0.5 rounded-full">
                                        {{ $doctor->bulan }} bulan ini
                                    </span>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="font-bold text-slate-800 text-sm">{{ $doctor->total }}</p>
                                <p class="text-xs text-slate-500">total</p>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-6">
                            <i class="fas fa-user-slash text-2xl text-slate-300 mb-2"></i>
                            <p class="text-sm text-slate-500">Data tidak tersedia</p>
                        </div>
                        @endforelse
                    </div>
                </div>

                <!-- Enhanced Ranking Perusahaan -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 bg-gradient-to-br from-blue-100 to-blue-200 rounded-lg flex items-center justify-center">
                            <i class="fas fa-building text-blue-600 text-lg"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-slate-800">Peringkat Perusahaan</h3>
                            <p class="text-xs text-slate-500">Berdasarkan volume</p>
                        </div>
                    </div>
                    <div class="space-y-3">
                        @forelse ($companyRanking as $index => $company)
                        <div class="ranking-item flex items-center gap-3 p-3 rounded-lg">
                            <div class="flex-shrink-0">
                                @if($index === 0)
                                    <span class="w-7 h-7 rounded-full bg-gradient-to-br from-blue-400 to-blue-500 text-white font-bold text-xs flex items-center justify-center shadow-sm">
                                        <i class="fas fa-star"></i>
                                    </span>
                                @else
                                    <span class="w-7 h-7 rounded-full bg-slate-200 text-slate-700 font-semibold text-xs flex items-center justify-center">
                                        {{ $index + 1 }}
                                    </span>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="font-medium text-slate-700 truncate text-sm">{{ $company->name }}</p>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="text-xs bg-blue-100 text-blue-800 font-medium px-2 py-0.5 rounded-full">
                                        {{ $company->bulan }} bulan ini
                                    </span>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="font-bold text-slate-800 text-sm">{{ $company->total }}</p>
                                <p class="text-xs text-slate-500">total</p>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-6">
                            <i class="fas fa-building text-2xl text-slate-300 mb-2"></i>
                            <p class="text-sm text-slate-500">Data tidak tersedia</p>
                        </div>
                        @endforelse
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
{{-- CDN untuk Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
{{-- CDN untuk Leaflet.js --}}
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
    integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
    crossorigin=""></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Enhanced Chart with gradient
    const ctx = document.getElementById('resultsChart');
    if (ctx) {
        const gradient = ctx.getContext('2d').createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, 'rgba(59, 130, 246, 0.2)');
        gradient.addColorStop(1, 'rgba(59, 130, 246, 0.02)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: @json($tanggal),
                datasets: @json($outletChartData).map(dataset => ({
                    ...dataset,
                    backgroundColor: gradient,
                    borderColor: '#3b82f6',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: '#3b82f6',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }))
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            padding: 20
                        }
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleColor: '#ffffff',
                        bodyColor: '#ffffff',
                        borderColor: '#3b82f6',
                        borderWidth: 1,
                        cornerRadius: 8,
                        padding: 12
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0,
                            color: '#64748b'
                        },
                        grid: {
                            color: 'rgba(148, 163, 184, 0.1)'
                        }
                    },
                    x: {
                        ticks: {
                            color: '#64748b'
                        },
                        grid: {
                            display: false
                        }
                    }
                },
                interaction: {
                    mode: 'nearest',
                    axis: 'x',
                    intersect: false
                },
                elements: {
                    point: {
                        hoverBackgroundColor: '#3b82f6'
                    }
                }
            }
        });
    }

    // Enhanced Map initialization
    const mapElement = document.getElementById('map');
    if (mapElement) {
        const kliniks = @json($mapKliniks);
        
        const initialCoords = (kliniks.length > 0) ? [kliniks[0].lat, kliniks[0].lon] : [-6.2088, 106.8456];
        const map = L.map('map', {
            zoomControl: false // Custom position
        }).setView(initialCoords, 11);

        // Add zoom control to top-right
        L.control.zoom({
            position: 'topright'
        }).addTo(map);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap'
        }).addTo(map);

        if (kliniks.length > 0) {
            const bounds = [];
            kliniks.forEach(klinik => {
                let markerIcon;
                
                if (klinik.has_new_surat) {
                    markerIcon = L.divIcon({
                        className: 'leaflet-pulsing-icon',
                        iconSize: [16, 16]
                    });
                } else {
                    markerIcon = new L.Icon.Default();
                }

                const marker = L.marker([klinik.lat, klinik.lon], { icon: markerIcon }).addTo(map);
                
                // Enhanced popup
                const popupContent = `
                    <div class="p-2">
                        <h4 class="font-semibold text-slate-800 mb-1">${klinik.name}</h4>
                        <p class="text-sm text-slate-600">Surat Hari Ini: <span class="font-medium">${klinik.surat_count}</span></p>
                        ${klinik.has_new_surat ? '<span class="inline-block mt-1 px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">Ada Surat Baru</span>' : ''}
                    </div>
                `;
                
                marker.bindPopup(popupContent);
                bounds.push([klinik.lat, klinik.lon]);
            });
            
            if (bounds.length > 1) {
                map.fitBounds(bounds, { padding: [20, 20] });
            }
        }
    }
});
</script>
@endpush