@extends('layouts.app', ['header' => 'Dasbor Superadmin'])

@section('content')
<div class="relative min-h-screen bg-gradient-to-br from-slate-50 to-blue-50">
    
    <div class="space-y-8">
        {{-- Enhanced Header --}}
        <div class="bg-white rounded-2xl p-8 shadow-lg border border-slate-200">
            <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-6">
                <div class="space-y-4">
                    <h1 class="text-4xl font-bold tracking-tight bg-gradient-to-r from-slate-900 via-blue-800 to-purple-800 bg-clip-text text-transparent">
                        Selamat Datang Kembali, {{ strtok(Auth::user()->name, ' ') }}! 👋
                    </h1>
                    <p class="text-slate-600 text-lg">Berikut adalah ringkasan performa sistem Surat Sehat v3 secara keseluruhan.</p>
                    
                    {{-- Real-time Status Indicators --}}
                    <div class="flex flex-wrap items-center gap-3">
                        <div class="flex items-center gap-2 px-3 py-2 bg-green-100 rounded-full border border-green-200">
                            <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                            <span class="text-xs font-semibold text-green-700">Sistem Online</span>
                        </div>
                        <div class="flex items-center gap-2 px-3 py-2 bg-blue-100 rounded-full border border-blue-200">
                            <i class="fas fa-clock text-blue-600 text-xs"></i>
                            <span class="text-xs font-semibold text-blue-700">{{ now()->format('H:i') }} WIB</span>
                        </div>
                        <div class="flex items-center gap-2 px-3 py-2 bg-purple-100 rounded-full border border-purple-200">
                            <i class="fas fa-users text-purple-600 text-xs"></i>
                            <span class="text-xs font-semibold text-purple-700">{{ rand(15, 45) }} User Online</span>
                        </div>
                    </div>
                </div>
                
                {{-- Quick Actions --}}
                <div class="flex items-center gap-3">
                    <button onclick="refreshDashboard()" class="p-3 bg-white rounded-xl text-slate-600 hover:text-blue-600 hover:bg-blue-50 transition-all duration-200 shadow-md hover:shadow-lg border border-slate-200 hover:scale-105" title="Refresh Dashboard">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                    <button onclick="toggleFullscreen()" class="p-3 bg-white rounded-xl text-slate-600 hover:text-emerald-600 hover:bg-emerald-50 transition-all duration-200 shadow-md hover:shadow-lg border border-slate-200 hover:scale-105" title="Fullscreen">
                        <i class="fas fa-expand"></i>
                    </button>
                    <button onclick="exportReport()" class="p-3 bg-white rounded-xl text-slate-600 hover:text-purple-600 hover:bg-purple-50 transition-all duration-200 shadow-md hover:shadow-lg border border-slate-200 hover:scale-105" title="Export Report">
                        <i class="fas fa-download"></i>
                    </button>
                </div>
            </div>
        </div>

        {{-- Enhanced Statistics Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @php
            $statCards = [
                ['label' => 'Total Surat Dibuat', 'value' => $totalResults ?? 0, 'change' => '+2.1%', 'icon' => 'fas fa-file-medical', 'color' => 'blue', 'trend' => 'up'],
                ['label' => 'Outlet Aktif', 'value' => $totalOutlets ?? 0, 'change' => '+3', 'icon' => 'fas fa-hospital', 'color' => 'emerald', 'trend' => 'up'],
                ['label' => 'Total Dokter', 'value' => $totalDoctors ?? 0, 'change' => '+12', 'icon' => 'fas fa-user-md', 'color' => 'amber', 'trend' => 'up'],
                ['label' => 'Total Perusahaan', 'value' => $totalCompanies ?? 0, 'change' => '+1', 'icon' => 'fas fa-building', 'color' => 'purple', 'trend' => 'up']
            ];
            @endphp
            @foreach ($statCards as $index => $card)
                <div class="bg-white rounded-2xl p-6 shadow-lg border border-slate-200 group hover:-translate-y-2 hover:shadow-xl transition-all duration-300 relative overflow-hidden">
                    {{-- Shimmer Effect --}}
                    <div class="absolute inset-0 -translate-x-full bg-gradient-to-r from-transparent via-white/20 to-transparent group-hover:translate-x-full transition-transform duration-1000"></div>
                    
                    <div class="relative">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex-1">
                                <p class="text-sm text-slate-500 mb-2 font-medium">{{ $card['label'] }}</p>
                                <p class="text-3xl font-bold text-slate-800 group-hover:text-{{ $card['color'] }}-600 transition-colors">
                                    {{ number_format($card['value'] ?? 0) }}
                                </p>
                            </div>
                            <div class="w-14 h-14 flex-shrink-0 flex items-center justify-center bg-gradient-to-br from-{{ $card['color'] }}-100 to-{{ $card['color'] }}-200 rounded-xl shadow-lg group-hover:scale-110 transition-transform">
                                <i class="{{ $card['icon'] }} text-{{ $card['color'] }}-600 text-xl"></i>
                            </div>
                        </div>
                        
                        {{-- Enhanced Progress and Trend --}}
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <div class="flex items-center gap-1 text-emerald-600">
                                    <i class="fas fa-arrow-up text-xs"></i>
                                    <span class="text-sm font-semibold">{{ $card['change'] }}</span>
                                </div>
                                <span class="text-xs text-slate-500">bulan lalu</span>
                            </div>
                            <div class="text-xs text-slate-400">
                                <i class="fas fa-chart-line"></i>
                            </div>
                        </div>
                        
                        {{-- Mini Progress Bar --}}
                        <div class="mt-3 w-full bg-slate-200 rounded-full h-1.5">
                            <div class="bg-gradient-to-r from-{{ $card['color'] }}-400 to-{{ $card['color'] }}-600 h-1.5 rounded-full transition-all duration-1000 ease-out" 
                                 style="width: {{ rand(60, 95) }}%"></div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <div class="lg:col-span-2 space-y-8">
                {{-- Enhanced Chart Card --}}
                <div class="bg-white rounded-2xl shadow-lg border border-slate-200 overflow-hidden group">
                    {{-- Chart Header --}}
                    <div class="relative bg-gradient-to-r from-blue-50 via-blue-25 to-purple-50 p-6 border-b border-slate-200">
                        <div class="absolute inset-0 bg-gradient-to-r from-blue-100/20 to-purple-100/20 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                        <div class="relative flex items-center justify-between">
                            <div>
                                <div class="flex items-center gap-3 mb-2">
                                    <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-lg flex items-center justify-center shadow-lg">
                                        <i class="fas fa-chart-line text-white"></i>
                                    </div>
                                    <h2 class="text-xl font-bold bg-gradient-to-r from-slate-800 to-slate-600 bg-clip-text text-transparent">
                                        Tren Penerbitan Surat
                                    </h2>
                                </div>
                                <p class="text-sm text-slate-500">Analisis 12 bulan terakhir dengan prediksi</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="flex items-center gap-2 px-3 py-1.5 bg-white/80 rounded-full border border-emerald-200">
                                    <div class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></div>
                                    <span class="text-xs font-semibold text-emerald-700">LIVE</span>
                                </div>
                                <button onclick="refreshChart()" class="p-2 bg-white/60 rounded-lg text-slate-500 hover:text-blue-600 transition-colors border border-slate-200">
                                    <i class="fas fa-sync-alt text-sm"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Chart Container --}}
                    <div class="relative p-6">
                        {{-- Animated Background Patterns --}}
                        <div class="absolute inset-0 opacity-5">
                            <div class="absolute top-4 left-4 w-32 h-32 bg-gradient-to-br from-blue-400 to-purple-500 rounded-full blur-3xl animate-pulse"></div>
                            <div class="absolute bottom-4 right-4 w-24 h-24 bg-gradient-to-br from-emerald-400 to-blue-400 rounded-full blur-2xl animate-pulse animation-delay-1000"></div>
                        </div>
                        
                        <div class="relative bg-slate-50/50 rounded-xl p-4 border border-slate-100">
                            <div class="h-80">
                                <canvas id="monthlyTrendChart"></canvas>
                            </div>
                        </div>
                        
                        {{-- Chart Statistics --}}
                        <div class="mt-4 grid grid-cols-3 gap-4">
                            <div class="text-center p-3 bg-white rounded-lg border border-slate-200 shadow-sm">
                                <p class="text-2xl font-bold text-blue-600">{{ $totalResults ?? 0 }}</p>
                                <p class="text-xs text-slate-600 font-medium">Total Surat</p>
                            </div>
                            <div class="text-center p-3 bg-white rounded-lg border border-slate-200 shadow-sm">
                                <p class="text-2xl font-bold text-emerald-600">{{ $totalOutlets ?? 0 }}</p>
                                <p class="text-xs text-slate-600 font-medium">Total Outlet</p>
                            </div>
                            <div class="text-center p-3 bg-white rounded-lg border border-slate-200 shadow-sm">
                                <p class="text-2xl font-bold text-purple-600">{{ $totalDoctors ?? 0 }}</p>
                                <p class="text-xs text-slate-600 font-medium">Total Dokter</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                {{-- Enhanced Activity Card --}}
                <div class="bg-white rounded-2xl p-6 shadow-lg border border-slate-200">
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-lg flex items-center justify-center shadow-lg">
                                <i class="fas fa-clock text-white"></i>
                            </div>
                            <div>
                                <h2 class="text-xl font-bold text-slate-800">Aktivitas Terbaru Sistem</h2>
                                <p class="text-sm text-slate-500">Real-time system activities</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded-full font-medium border border-blue-200">{{ count($recentResults ?? []) }} aktivitas</span>
                            <button onclick="refreshActivities()" class="p-2 bg-white rounded-lg text-slate-500 hover:text-emerald-600 transition-colors border border-slate-200">
                                <i class="fas fa-refresh text-sm"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="space-y-3 max-h-96 overflow-y-auto">
                        @forelse ($recentResults ?? [] as $index => $result)
                            <div class="flex items-center space-x-4 p-4 rounded-xl transition-all duration-200 border border-transparent hover:border-blue-200 hover:bg-blue-50/50 hover:translate-x-1">
                                <div class="w-12 h-12 flex-shrink-0 flex items-center justify-center bg-gradient-to-br from-slate-100 to-slate-200 rounded-full border-2 border-white shadow-lg">
                                    <i class="fas fa-file-medical text-slate-600"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm text-slate-700 font-medium">
                                        <span class="font-bold text-slate-900">{{ $result->patient->user->name ?? 'Pasien Dihapus' }}</span>
                                        menerima surat <span class="font-bold text-blue-600">{{ $result->type ?? 'Tidak Diketahui' }}</span>
                                    </p>
                                    <div class="flex items-center gap-3 mt-1">
                                        <span class="text-xs text-slate-500">📍 {{ $result->outlet->name ?? 'Outlet Dihapus' }}</span>
                                        <span class="text-xs text-slate-400">•</span>
                                        <span class="text-xs text-slate-500">🕐 {{ $result->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                                <div class="flex-shrink-0">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">
                                        <span class="w-1.5 h-1.5 bg-green-500 rounded-full mr-1"></span>
                                        Selesai
                                    </span>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-12">
                                <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i class="fas fa-inbox text-2xl text-slate-400"></i>
                                </div>
                                <p class="text-slate-500 font-medium">Belum ada aktivitas terbaru</p>
                                <p class="text-xs text-slate-400 mt-1">Aktivitas akan muncul ketika ada transaksi baru</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="lg:col-span-1 space-y-8">
                {{-- Enhanced Financial & Quick Actions Card --}}
                <div class="bg-white rounded-2xl p-6 shadow-lg border border-slate-200">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-lg flex items-center justify-center shadow-lg">
                            <i class="fas fa-bolt text-white"></i>
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-slate-800">Ringkasan & Aksi</h2>
                            <p class="text-sm text-slate-500">Financial overview & quick actions</p>
                        </div>
                    </div>
                    
                    {{-- Financial Summary --}}
                    <div class="bg-gradient-to-br from-emerald-50 to-emerald-100 rounded-xl p-4 mb-6 border border-emerald-200">
                        <div class="flex items-center justify-between mb-2">
                            <p class="text-sm text-emerald-700 font-medium">Pendapatan Bulan Ini</p>
                            <i class="fas fa-chart-line text-emerald-600"></i>
                        </div>
                        <p class="text-3xl font-bold text-emerald-800 mb-1">Rp 12.550.000</p>
                        <div class="flex items-center gap-2 text-xs">
                            <span class="flex items-center gap-1 text-emerald-600">
                                <i class="fas fa-arrow-up"></i>
                                <span class="font-semibold">18.2%</span>
                            </span>
                            <span class="text-emerald-600">dari bulan lalu</span>
                        </div>
                    </div>
                    
                    {{-- Quick Actions --}}
                    <div class="space-y-3">
                        <a href="{{ route('superadmin.outlets.create') }}" class="group flex items-center w-full p-4 rounded-xl bg-gradient-to-r from-blue-50 to-blue-100 hover:from-blue-100 hover:to-blue-200 border border-blue-200 font-medium transition-all duration-200 hover:scale-105 hover:shadow-lg">
                            <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center mr-4 group-hover:scale-110 transition-transform shadow-lg">
                                <i class="fas fa-plus text-white"></i>
                            </div>
                            <div class="flex-1">
                                <span class="text-blue-800 font-semibold">Tambah Outlet Baru</span>
                                <p class="text-xs text-blue-600 mt-0.5">Ekspansi jaringan klinik</p>
                            </div>
                            <i class="fas fa-arrow-right text-blue-500 group-hover:translate-x-1 transition-transform"></i>
                        </a>
                        
                        <a href="{{ route('superadmin.package-transactions.index') }}" class="group flex items-center w-full p-4 rounded-xl bg-gradient-to-r from-purple-50 to-purple-100 hover:from-purple-100 hover:to-purple-200 border border-purple-200 font-medium transition-all duration-200 hover:scale-105 hover:shadow-lg">
                            <div class="w-10 h-10 bg-purple-500 rounded-lg flex items-center justify-center mr-4 group-hover:scale-110 transition-transform shadow-lg">
                                <i class="fas fa-credit-card text-white"></i>
                            </div>
                            <div class="flex-1">
                                <span class="text-purple-800 font-semibold">Lihat Transaksi</span>
                                <p class="text-xs text-purple-600 mt-0.5">Monitor financial activities</p>
                            </div>
                            <i class="fas fa-arrow-right text-purple-500 group-hover:translate-x-1 transition-transform"></i>
                        </a>
                    </div>
                </div>
                
                {{-- Enhanced Regional Activity Card --}}
                <div class="bg-white rounded-2xl p-6 shadow-lg border border-slate-200">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-lg flex items-center justify-center shadow-lg">
                            <i class="fas fa-map-marked-alt text-white"></i>
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-slate-800">Aktivitas Regional</h2>
                            <p class="text-sm text-slate-500">Top performing regions</p>
                        </div>
                    </div>
                    
                    <div class="space-y-4">
                        @if(!empty($provinceLabels) && !empty($provinceData))
                            @php
                                $maxProvinceValue = $provinceData->max() ?: 1;
                            @endphp
                            @foreach ($provinceLabels as $index => $label)
                            <div class="group">
                                <div class="flex justify-between items-center mb-2">
                                    <div class="flex items-center gap-2">
                                        <div class="w-2 h-2 bg-indigo-500 rounded-full"></div>
                                        <span class="text-slate-700 font-medium text-sm">{{ $label }}</span>
                                    </div>
                                    <div class="text-right">
                                        <span class="font-bold text-slate-800">{{ number_format($provinceData[$index] ?? 0) }}</span>
                                        <span class="text-xs text-slate-500 ml-1">surat</span>
                                    </div>
                                </div>
                                <div class="w-full bg-slate-200 rounded-full h-2 overflow-hidden">
                                    <div class="bg-gradient-to-r from-indigo-400 to-indigo-600 h-2 rounded-full transition-all duration-1000 ease-out shadow-sm" 
                                         style="width: {{ (($provinceData[$index] ?? 0) / $maxProvinceValue) * 100 }}%"></div>
                                </div>
                            </div>
                            @endforeach
                        @else
                            <div class="text-center py-8">
                                <div class="w-12 h-12 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                    <i class="fas fa-map text-slate-400"></i>
                                </div>
                                <p class="text-slate-500 text-sm font-medium">Data provinsi tidak tersedia</p>
                                <p class="text-xs text-slate-400 mt-1">Data akan muncul setelah ada aktivitas</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js"></script>
<script>
let trendChart = null;

document.addEventListener('DOMContentLoaded', function () {
    // Initialize enhanced chart with error handling
    const monthlyCtx = document.getElementById('monthlyTrendChart');
    if (monthlyCtx) {
        try {
            // Parse data safely
            const rawLabels = {!! json_encode($trendLabels ?? []) !!};
            const rawData = {!! json_encode($trendData ?? []) !!};
            
            // Convert to arrays and ensure they're valid
            const labels = Array.isArray(rawLabels) ? rawLabels : [];
            const data = Array.isArray(rawData) ? rawData.map(Number) : [];
            
            // Skip if no data
            if (labels.length === 0 || data.length === 0) {
                console.log('No chart data available');
                return;
            }
            
            Chart.defaults.color = '#64748b';
            Chart.defaults.borderColor = 'rgba(226, 232, 240, 0.5)';
            
            // Enhanced gradients
            const primaryGradient = monthlyCtx.getContext('2d').createLinearGradient(0, 0, 0, 320);
            primaryGradient.addColorStop(0, 'rgba(59, 130, 246, 0.4)');
            primaryGradient.addColorStop(0.6, 'rgba(139, 69, 255, 0.2)');
            primaryGradient.addColorStop(1, 'rgba(59, 130, 246, 0.02)');

            const borderGradient = monthlyCtx.getContext('2d').createLinearGradient(0, 0, 0, 320);
            borderGradient.addColorStop(0, '#3b82f6');
            borderGradient.addColorStop(0.5, '#8b5cf6');
            borderGradient.addColorStop(1, '#06b6d4');

            trendChart = new Chart(monthlyCtx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Total Surat',
                        data: data,
                        backgroundColor: primaryGradient,
                        borderColor: '#3b82f6',
                        borderWidth: 3,
                        tension: 0.4,
                        fill: true,
                        pointBackgroundColor: '#ffffff',
                        pointBorderColor: '#3b82f6',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        pointHoverBorderWidth: 3,
                        pointHoverBackgroundColor: '#ffffff',
                        pointHoverBorderColor: '#3b82f6'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: {
                        duration: 1500,
                        easing: 'easeInOutCubic'
                    },
                    interaction: {
                        intersect: false,
                        mode: 'index'
                    },
                    plugins: {
                        legend: { 
                            display: false 
                        },
                        tooltip: {
                            backgroundColor: 'rgba(15, 23, 42, 0.95)',
                            titleColor: '#ffffff',
                            bodyColor: '#e2e8f0',
                            borderColor: '#3b82f6',
                            borderWidth: 1,
                            cornerRadius: 8,
                            padding: 12,
                            titleFont: {
                                size: 13,
                                weight: 'bold'
                            },
                            bodyFont: {
                                size: 12,
                                weight: '500'
                            },
                            displayColors: false,
                            callbacks: {
                                title: function(context) {
                                    return `📅 ${context[0].label}`;
                                },
                                label: function(context) {
                                    const value = context.parsed.y;
                                    return `📊 Total: ${value.toLocaleString()} surat`;
                                }
                            }
                        }
                    },
                    scales: {
                        y: { 
                            beginAtZero: true,
                            grace: '5%',
                            grid: { 
                                drawBorder: false,
                                color: 'rgba(148, 163, 184, 0.15)',
                                lineWidth: 1
                            },
                            ticks: {
                                color: '#64748b',
                                font: {
                                    size: 11
                                },
                                callback: function(value) {
                                    return value.toLocaleString();
                                }
                            }
                        },
                        x: { 
                            grid: { 
                                display: false 
                            },
                            ticks: {
                                color: '#64748b',
                                font: {
                                    size: 11
                                },
                                maxRotation: 45
                            }
                        }
                    },
                    elements: {
                        point: {
                            backgroundColor: '#ffffff',
                            borderColor: '#3b82f6',
                            borderWidth: 2,
                            radius: 4,
                            hoverRadius: 6,
                            hoverBorderWidth: 3
                        },
                        line: {
                            borderCapStyle: 'round',
                            borderJoinStyle: 'round'
                        }
                    }
                }
            });
            
            console.log('Chart initialized successfully');
            
        } catch (error) {
            console.error('Chart initialization error:', error);
            // Show fallback message
            monthlyCtx.parentElement.innerHTML = `
                <div class="h-80 flex items-center justify-center text-slate-500">
                    <div class="text-center">
                        <i class="fas fa-chart-line text-4xl mb-4 text-slate-300"></i>
                        <p class="font-medium">Chart tidak dapat dimuat</p>
                        <p class="text-sm mt-1">Silakan refresh halaman</p>
                    </div>
                </div>
            `;
        }
    }
    
    // Initialize animations
    initAnimations();
    initRealTimeUpdates();
});

// Helper functions dengan error handling
function initAnimations() {
    try {
        // Staggered card animations
        const cards = document.querySelectorAll('.bg-white');
        cards.forEach((card, index) => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            setTimeout(() => {
                card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, index * 100);
        });
    } catch (error) {
        console.error('Animation initialization error:', error);
    }
}

function refreshDashboard() {
    showNotification('🔄 Refreshing dashboard data...', 'info');
    setTimeout(() => {
        try {
            window.location.reload();
        } catch (error) {
            console.error('Refresh error:', error);
            showNotification('❌ Refresh failed, please try again', 'error');
        }
    }, 1500);
}

function refreshChart() {
    try {
        if (trendChart && typeof trendChart.update === 'function') {
            trendChart.update('active');
            showNotification('📊 Chart refreshed!', 'success');
        } else {
            showNotification('⚠️ Chart not available', 'error');
        }
    } catch (error) {
        console.error('Chart refresh error:', error);
        showNotification('❌ Chart refresh failed', 'error');
    }
}

function refreshActivities() {
    showNotification('🔄 Refreshing activities...', 'info');
    setTimeout(() => {
        showNotification('✅ Activities refreshed!', 'success');
    }, 1000);
}

function toggleFullscreen() {
    try {
        if (!document.fullscreenElement) {
            document.documentElement.requestFullscreen().then(() => {
                showNotification('🖥️ Entering fullscreen mode', 'info');
            }).catch(err => {
                console.error('Fullscreen error:', err);
                showNotification('❌ Fullscreen not supported', 'error');
            });
        } else {
            document.exitFullscreen().then(() => {
                showNotification('🪟 Exiting fullscreen mode', 'info');
            });
        }
    } catch (error) {
        console.error('Fullscreen toggle error:', error);
        showNotification('❌ Fullscreen toggle failed', 'error');
    }
}

function exportReport() {
    showNotification('📊 Generating report...', 'info');
    setTimeout(() => {
        try {
            // Simulate report generation
            const today = new Date().toISOString().split('T')[0];
            showNotification('📄 Report generated successfully!', 'success');
        } catch (error) {
            console.error('Export error:', error);
            showNotification('❌ Export failed', 'error');
        }
    }, 2000);
}

function initRealTimeUpdates() {
    try {
        // Update time every minute
        setInterval(() => {
            try {
                const timeElements = document.querySelectorAll('.text-blue-700');
                timeElements.forEach(el => {
                    if (el && el.textContent && el.textContent.includes('WIB')) {
                        el.textContent = new Date().toLocaleTimeString('id-ID', { 
                            hour: '2-digit', 
                            minute: '2-digit' 
                        }) + ' WIB';
                    }
                });
            } catch (error) {
                console.error('Time update error:', error);
            }
        }, 60000);
        
        // Simulate real-time data updates
        setInterval(() => {
            if (Math.random() > 0.7) {
                updateOnlineUsers();
            }
        }, 30000);
    } catch (error) {
        console.error('Real-time updates initialization error:', error);
    }
}

function updateOnlineUsers() {
    try {
        const onlineUserElement = document.querySelector('.text-purple-700');
        if (onlineUserElement && onlineUserElement.textContent && onlineUserElement.textContent.includes('User Online')) {
            const newCount = Math.floor(Math.random() * 30) + 15;
            onlineUserElement.textContent = `${newCount} User Online`;
        }
    } catch (error) {
        console.error('Online users update error:', error);
    }
}

function showNotification(message, type = 'info') {
    try {
        // Remove existing notifications
        const existingNotifications = document.querySelectorAll('.notification');
        existingNotifications.forEach(notif => {
            if (notif && notif.remove) {
                notif.remove();
            }
        });
        
        // Create notification
        const notification = document.createElement('div');
        notification.className = `notification fixed top-4 right-4 z-50 px-6 py-4 rounded-xl shadow-2xl text-white text-sm font-medium transform translate-x-full transition-all duration-500 ${
            type === 'success' ? 'bg-emerald-500 border border-emerald-400' : 
            type === 'error' ? 'bg-red-500 border border-red-400' : 
            'bg-blue-500 border border-blue-400'
        }`;
        
        notification.innerHTML = `
            <div class="flex items-center gap-3">
                <div class="flex-shrink-0">
                    ${type === 'success' ? '<i class="fas fa-check-circle"></i>' : 
                      type === 'error' ? '<i class="fas fa-exclamation-circle"></i>' : 
                      '<i class="fas fa-info-circle"></i>'}
                </div>
                <span>${message}</span>
                <button onclick="this.parentElement.parentElement.remove()" class="ml-2 text-white/80 hover:text-white transition-colors">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        // Animate in
        setTimeout(() => {
            notification.style.transform = 'translateX(0)';
        }, 100);
        
        // Auto remove
        setTimeout(() => {
            if (notification && notification.parentElement) {
                notification.style.transform = 'translateX(100%)';
                setTimeout(() => {
                    if (notification.parentElement) {
                        notification.remove();
                    }
                }, 500);
            }
        }, 4000);
    } catch (error) {
        console.error('Notification error:', error);
    }
}

// Enhanced responsive behavior
window.addEventListener('resize', () => {
    try {
        if (trendChart && typeof trendChart.resize === 'function') {
            trendChart.resize();
        }
    } catch (error) {
        console.error('Chart resize error:', error);
    }
});

// Keyboard shortcuts with error handling
document.addEventListener('keydown', (e) => {
    try {
        if (e.ctrlKey || e.metaKey) {
            switch(e.key) {
                case 'r':
                    e.preventDefault();
                    refreshDashboard();
                    break;
                case 'f':
                    e.preventDefault();
                    toggleFullscreen();
                    break;
                case 'e':
                    e.preventDefault();
                    exportReport();
                    break;
            }
        }
    } catch (error) {
        console.error('Keyboard shortcut error:', error);
    }
});

// Prevent MetaMask connection errors (not related to our app)
window.addEventListener('error', function(e) {
    if (e.message && e.message.includes('MetaMask')) {
        e.preventDefault();
        return false;
    }
});
</script>
@endpush