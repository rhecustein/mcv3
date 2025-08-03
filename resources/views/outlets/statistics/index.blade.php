@extends('layouts.app')

@section('title', 'Statistik Aktivitas Surat')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    #map {
        height: 450px;
        width: 100%;
        border-radius: 0.75rem;
    }
    .chart-container {
        position: relative;
        height: 350px;
        width: 100%;
    }
    .stat-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        transition: all 0.3s ease;
    }
    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }
</style>
@endpush

@section('content')
<div class="container mx-auto px-4 py-8 space-y-8">

    <!-- Header Section -->
    <div class="bg-white rounded-2xl shadow-lg p-8 border border-slate-200">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-6">
                <div class="flex items-center justify-center w-20 h-20 bg-gradient-to-br from-blue-500 to-purple-600 rounded-2xl shadow-lg">
                    <svg class="w-10 h-10 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-4xl font-bold text-slate-800">Statistik & Analytics</h1>
                    <p class="text-slate-600 mt-2 text-lg">Dashboard analisis performa klinik dan aktivitas surat kesehatan</p>
                </div>
            </div>
            <div class="flex items-center space-x-3">
                <div class="text-right">
                    <p class="text-sm text-slate-500">Pembaruan Terakhir</p>
                    <p class="text-lg font-semibold text-slate-800">{{ now()->format('d M Y, H:i') }}</p>
                </div>
                <div class="w-3 h-3 bg-green-400 rounded-full animate-pulse"></div>
            </div>
        </div>
    </div>

    <!-- Summary Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        @php
            $stats = [
                [
                    'label' => 'Total Surat',
                    'value' => $summaryStats[0]['value'],
                    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />',
                    'gradient' => 'from-blue-500 to-blue-600',
                    'textColor' => 'text-blue-600',
                    'bgColor' => 'bg-blue-50',
                    'change' => '+12%'
                ],
                [
                    'label' => 'Perusahaan Aktif',
                    'value' => $summaryStats[1]['value'],
                    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m2.25-18v18m13.5-18v18m2.25-18v18M6.75 9h.008v.008H6.75V9zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM6.75 10.5h.008v.008H6.75v-.008zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM6.75 12h.008v.008H6.75V12zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />',
                    'gradient' => 'from-emerald-500 to-emerald-600',
                    'textColor' => 'text-emerald-600',
                    'bgColor' => 'bg-emerald-50',
                    'change' => '+8%'
                ],
                [
                    'label' => 'Dokter Aktif',
                    'value' => $summaryStats[2]['value'],
                    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />',
                    'gradient' => 'from-purple-500 to-purple-600',
                    'textColor' => 'text-purple-600',
                    'bgColor' => 'bg-purple-50',
                    'change' => '+5%'
                ],
                [
                    'label' => 'Pasien Aktif',
                    'value' => $summaryStats[3]['value'],
                    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0z" />',
                    'gradient' => 'from-rose-500 to-rose-600',
                    'textColor' => 'text-rose-600',
                    'bgColor' => 'bg-rose-50',
                    'change' => '+15%'
                ]
            ];
        @endphp

        @foreach($stats as $stat)
            <div class="relative overflow-hidden bg-white rounded-2xl shadow-lg border border-slate-200 p-6 group hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                <!-- Background Gradient -->
                <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br {{ $stat['gradient'] }} opacity-5 rounded-full transform translate-x-16 -translate-y-16"></div>
                
                <div class="relative">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 {{ $stat['bgColor'] }} rounded-xl">
                            <svg class="w-6 h-6 {{ $stat['textColor'] }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                {!! $stat['icon'] !!}
                            </svg>
                        </div>
                        <span class="text-sm font-medium text-emerald-600 bg-emerald-100 px-2 py-1 rounded-full">
                            {{ $stat['change'] }}
                        </span>
                    </div>
                    
                    <div>
                        <p class="text-sm font-medium text-slate-600 mb-1">{{ $stat['label'] }}</p>
                        <p class="text-3xl font-bold {{ $stat['textColor'] }}">{{ number_format($stat['value']) }}</p>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Charts & Map Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Chart Section -->
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-lg p-6 border border-slate-200">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-slate-800">Trend Surat Terbit</h2>
                    <p class="text-slate-600">Aktivitas penerbitan surat bulan ini</p>
                </div>
                <div class="flex items-center space-x-2">
                    <div class="flex items-center space-x-2 text-sm text-slate-600">
                        <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                        <span>Surat Diterbitkan</span>
                    </div>
                </div>
            </div>
            <div class="chart-container">
                <canvas id="outletLineChart"></canvas>
            </div>
        </div>

        <!-- Map Section -->
        <div class="bg-white rounded-2xl shadow-lg p-6 border border-slate-200">
            <div class="mb-6">
                <h2 class="text-2xl font-bold text-slate-800 mb-2">Lokasi Klinik</h2>
                <p class="text-slate-600">Peta sebaran aktivitas hari ini</p>
            </div>
            <div id="map" class="rounded-xl"></div>
            
            <!-- Map Legend -->
            <div class="mt-4 p-4 bg-slate-50 rounded-lg">
                <div class="flex items-center justify-between text-sm">
                    <div class="flex items-center space-x-2">
                        <div class="w-3 h-3 bg-blue-600 rounded-full animate-pulse"></div>
                        <span class="text-slate-600">Aktif Hari Ini</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <div class="w-3 h-3 bg-slate-400 rounded-full"></div>
                        <span class="text-slate-600">Tidak Aktif</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Rankings Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        <!-- Outlet Ranking -->
        <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-2xl shadow-lg border border-blue-200 overflow-hidden">
            <div class="p-6 bg-white bg-opacity-80 backdrop-blur-sm">
                <div class="flex items-center space-x-3 mb-6">
                    <div class="p-2 bg-blue-600 rounded-lg">
                        <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m2.25-18v18m13.5-18v18m2.25-18v18M6.75 9h.008v.008H6.75V9z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-blue-800">Klinik Penerbit Surat</h3>
                        <p class="text-blue-600 text-sm">Ranking berdasarkan aktivitas</p>
                    </div>
                </div>
                
                <div class="space-y-4">
                    @foreach ($outletRanking as $i => $row)
                        <div class="flex items-center space-x-4 p-4 bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow">
                            <div class="flex items-center justify-center w-10 h-10 bg-blue-100 rounded-lg">
                                <span class="text-blue-600 font-bold text-lg">{{ $i + 1 }}</span>
                            </div>
                            <div class="flex-1">
                                <p class="font-semibold text-slate-800">{{ $row['name'] }}</p>
                                <div class="flex items-center space-x-4 text-sm text-slate-600">
                                    <span>Bulan: {{ number_format($row['bulan']) }}</span>
                                    <span>Total: {{ number_format($row['total']) }}</span>
                                </div>
                            </div>
                            @if($i === 0)
                                <div class="text-2xl">🏆</div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Doctor Ranking -->
        <div class="bg-gradient-to-br from-emerald-50 to-emerald-100 rounded-2xl shadow-lg border border-emerald-200 overflow-hidden">
            <div class="p-6 bg-white bg-opacity-80 backdrop-blur-sm">
                <div class="flex items-center space-x-3 mb-6">
                    <div class="p-2 bg-emerald-600 rounded-lg">
                        <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-emerald-800">Dokter Penerbit Surat</h3>
                        <p class="text-emerald-600 text-sm">Ranking berdasarkan produktivitas</p>
                    </div>
                </div>
                
                <div class="space-y-4">
                    @forelse ($doctorRanking as $i => $row)
                        <div class="flex items-center space-x-4 p-4 bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow">
                            <div class="flex items-center justify-center w-10 h-10 bg-emerald-100 rounded-lg">
                                <span class="text-emerald-600 font-bold text-lg">{{ $i + 1 }}</span>
                            </div>
                            <div class="flex-1">
                                <p class="font-semibold text-slate-800">{{ $row['name'] }}</p>
                                <div class="flex items-center space-x-4 text-sm text-slate-600">
                                    <span>Bulan: {{ number_format($row['bulan']) }}</span>
                                    <span>Total: {{ number_format($row['total']) }}</span>
                                </div>
                            </div>
                            @if($i === 0 && $row['total'] > 0)
                                <div class="text-2xl">👨‍⚕️</div>
                            @endif
                        </div>
                    @empty
                        <div class="text-center py-8 text-slate-500">
                            <svg class="w-12 h-12 mx-auto mb-3 text-slate-300" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                            </svg>
                            <p>Belum ada data dokter</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Company Ranking -->
        <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-2xl shadow-lg border border-purple-200 overflow-hidden">
            <div class="p-6 bg-white bg-opacity-80 backdrop-blur-sm">
                <div class="flex items-center space-x-3 mb-6">
                    <div class="p-2 bg-purple-600 rounded-lg">
                        <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15l-.75 18H5.25L4.5 3z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-purple-800">Perusahaan Teraktif</h3>
                        <p class="text-purple-600 text-sm">Ranking berdasarkan penggunaan</p>
                    </div>
                </div>
                
                <div class="space-y-4">
                    @forelse ($companyRanking as $i => $row)
                        <div class="flex items-center space-x-4 p-4 bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow">
                            <div class="flex items-center justify-center w-10 h-10 bg-purple-100 rounded-lg">
                                <span class="text-purple-600 font-bold text-lg">{{ $i + 1 }}</span>
                            </div>
                            <div class="flex-1">
                                <p class="font-semibold text-slate-800">{{ $row['name'] }}</p>
                                <div class="flex items-center space-x-4 text-sm text-slate-600">
                                    <span>Bulan: {{ number_format($row['bulan']) }}</span>
                                    <span>Total: {{ number_format($row['total']) }}</span>
                                </div>
                            </div>
                            @if($i === 0 && $row['total'] > 0)
                                <div class="text-2xl">🏢</div>
                            @endif
                        </div>
                    @empty
                        <div class="text-center py-8 text-slate-500">
                            <svg class="w-12 h-12 mx-auto mb-3 text-slate-300" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15l-.75 18H5.25L4.5 3z" />
                            </svg>
                            <p>Belum ada data perusahaan</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    const kliniks = @json($mapKliniks);
    
    // Initialize map
    const map = L.map('map').setView([1.1, 104.05], 10);

    // Add tile layer
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors',
        maxZoom: 18,
    }).addTo(map);

    // Add markers for each clinic
    kliniks.forEach(k => {
        const isActive = k.has_new_surat;
        const markerColor = isActive ? '#2563eb' : '#64748b';
        
        const markerHtml = `
            <div class="relative ${isActive ? 'animate-bounce' : ''}">
                <div class="px-3 py-2 text-white text-xs rounded-lg shadow-lg" style="background-color: ${markerColor};">
                    <div class="flex items-center space-x-1">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                        </svg>
                        <span class="font-medium">${k.name}</span>
                    </div>
                    <div class="text-center mt-1">
                        <span class="font-bold">${k.surat_count}</span> surat
                    </div>
                </div>
                ${isActive ? `<div class="absolute inset-0 rounded-lg bg-blue-400 opacity-30 animate-ping"></div>` : ''}
            </div>
        `;

        const customIcon = L.divIcon({
            className: '',
            html: markerHtml,
            iconSize: [120, 50],
            popupAnchor: [0, -25]
        });

        const marker = L.marker([k.lat || 1.1, k.lon || 104.05], { icon: customIcon }).addTo(map);
        
        marker.bindPopup(`
            <div class="p-3">
                <h3 class="font-bold text-lg text-slate-800 mb-2">${k.name}</h3>
                <div class="space-y-1 text-sm text-slate-600">
                    <div class="flex items-center justify-between">
                        <span>Surat Hari Ini:</span>
                        <span class="font-medium">${k.surat_count}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span>Status:</span>
                        <span class="font-medium ${isActive ? 'text-green-600' : 'text-slate-500'}">
                            ${isActive ? '🟢 Aktif' : '⚪ Tidak Aktif'}
                        </span>
                    </div>
                </div>
            </div>
        `);
    });

    // Fit map to show all markers
    if (kliniks.length > 0) {
        const group = new L.featureGroup(map._layers);
        if (Object.keys(group._layers).length > 0) {
            map.fitBounds(group.getBounds().pad(0.1));
        }
    }
</script>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const chartLabels = @json($tanggal);
    const chartDataSets = @json($outletChartData);

    const ctx = document.getElementById('outletLineChart').getContext('2d');
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: chartLabels,
            datasets: chartDataSets.map(ds => ({
                ...ds,
                backgroundColor: 'rgba(37, 99, 235, 0.1)',
                borderColor: '#2563eb',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#2563eb',
                pointBorderColor: '#ffffff',
                pointBorderWidth: 2,
                pointRadius: 5,
                pointHoverRadius: 8,
                pointHoverBackgroundColor: '#1d4ed8',
                pointHoverBorderColor: '#ffffff',
                pointHoverBorderWidth: 3,
            }))
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                    backgroundColor: 'rgba(15, 23, 42, 0.9)',
                    titleColor: '#ffffff',
                    bodyColor: '#ffffff',
                    borderColor: '#2563eb',
                    borderWidth: 1,
                    cornerRadius: 8,
                    displayColors: false,
                    callbacks: {
                        title: function(context) {
                            return `Tanggal ${context[0].label}`;
                        },
                        label: function(context) {
                            return `${context.parsed.y} surat diterbitkan`;
                        }
                    }
                }
            },
            interaction: {
                mode: 'nearest',
                axis: 'x',
                intersect: false
            },
            scales: {
                x: {
                    title: {
                        display: true,
                        text: 'Tanggal dalam Bulan',
                        font: {
                            size: 14,
                            weight: 'bold'
                        },
                        color: '#64748b'
                    },
                    grid: {
                        color: 'rgba(148, 163, 184, 0.1)',
                        borderColor: '#e2e8f0'
                    },
                    ticks: {
                        color: '#64748b',
                        font: {
                            size: 12
                        }
                    }
                },
                y: {
                    title: {
                        display: true,
                        text: 'Jumlah Surat',
                        font: {
                            size: 14,
                            weight: 'bold'
                        },
                        color: '#64748b'
                    },
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(148, 163, 184, 0.1)',
                        borderColor: '#e2e8f0'
                    },
                    ticks: {
                        stepSize: 1,
                        color: '#64748b',
                        font: {
                            size: 12
                        }
                    }
                }
            },
            elements: {
                point: {
                    hoverRadius: 8
                }
            }
        }
    });
</script>
@endpush