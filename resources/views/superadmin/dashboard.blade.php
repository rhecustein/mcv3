@extends('layouts.app', ['header' => 'Dasbor Superadmin'])

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Selamat Datang Kembali, {{ Auth::user()->name }}!</h1>
        <p class="text-slate-500 mt-1">Berikut adalah ringkasan performa sistem Surat Sehat v3 secara keseluruhan.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <div class="lg:col-span-2 space-y-6">

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @php
                $statCards = [
                    ['label' => 'Total Surat Dibuat', 'value' => $totalResults, 'change' => '+2.1%', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25M9 16.5h.008v.008H9v-.008z" /> <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />', 'color' => 'blue'],
                    ['label' => 'Total Outlet Aktif', 'value' => $totalOutlets, 'change' => '+3', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.25M19.5 21v-7.5a.75.75 0 00-.75-.75h-3a.75.75 0 00-.75.75V21m-4.5 0H2.25m19.5 0H2.25M4.5 9.75v8.25a2.25 2.25 0 002.25 2.25h10.5a2.25 2.25 0 002.25-2.25V9.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25z" />', 'color' => 'sky'],
                    ['label' => 'Total Dokter', 'value' => $totalDoctors, 'change' => '+12', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 18.75a6 6 0 006-6v-1.5m-6 7.5a6 6 0 01-6-6v-1.5m6 7.5v3.75m-3.75 0h7.5M12 15.75a3 3 0 01-3-3V4.5a3 3 0 116 0v8.25a3 3 0 01-3 3z" />', 'color' => 'amber'],
                    ['label' => 'Total Perusahaan', 'value' => $totalCompanies, 'change' => '+1', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h6M9 11.25h6M9 15.75h6M9 20.25h6" />', 'color' => 'indigo']
                ];
                @endphp
                @foreach ($statCards as $card)
                <div class="bg-white border border-slate-200 rounded-xl p-4 flex items-start justify-between transition-all duration-300 hover:shadow-lg hover:-translate-y-1">
                    <div>
                        <p class="text-sm text-slate-500 mb-1">{{ $card['label'] }}</p>
                        <p class="text-3xl font-bold text-slate-800">{{ number_format($card['value']) }}</p>
                        <p class="text-xs text-green-600 mt-1">{{ $card['change'] }} dari bulan lalu</p>
                    </div>
                    <div class="w-10 h-10 flex-shrink-0 flex items-center justify-center bg-{{$card['color']}}-100 rounded-lg">
                        <svg class="w-6 h-6 text-{{$card['color']}}-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">{!! $card['icon'] !!}</svg>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-6">
                <h2 class="text-lg font-semibold mb-4 text-slate-800">📈 Tren Penerbitan Surat (12 Bulan Terakhir)</h2>
                <div class="h-80">
                    <canvas id="monthlyTrendChart"></canvas>
                </div>
            </div>
            
            <div class="bg-white border border-slate-200 rounded-xl p-6">
                 <h2 class="text-lg font-semibold mb-4 text-slate-800">🕒 Aktivitas Terbaru Sistem</h2>
                 <ul class="space-y-4">
                     @forelse ($recentResults as $result)
                         <li class="flex items-start space-x-3">
                            <div class="w-8 h-8 flex-shrink-0 flex items-center justify-center bg-slate-100 rounded-full">
                                <svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25" /></svg>
                            </div>
                            <div class="flex-1 text-sm">
                                <p class="text-slate-600">
                                    <span class="font-semibold text-slate-800">{{ $result->patient->user->name ?? 'Pasien' }}</span>
                                    menerima surat <span class="font-semibold text-blue-600">{{ $result->type }}</span>
                                    di outlet {{ $result->outlet->name ?? '-' }}.
                                </p>
                                <p class="text-xs text-slate-400 mt-0.5">{{ $result->created_at->diffForHumans() }}</p>
                            </div>
                         </li>
                     @empty
                        <li class="text-center text-slate-500 py-4">Belum ada aktivitas terbaru.</li>
                     @endforelse
                 </ul>
            </div>
        </div>

        <div class="lg:col-span-1 space-y-6">

            <div class="bg-white border border-slate-200 rounded-xl p-6">
                <h2 class="text-lg font-semibold text-slate-800">💰 Ringkasan Finansial</h2>
                <div class="mt-4 grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-slate-500">Pendapatan Bulan Ini</p>
                        <p class="text-2xl font-bold text-green-600">Rp 12.550.000</p>
                    </div>
                     <div>
                        <p class="text-sm text-slate-500">Langganan Baru</p>
                        <p class="text-2xl font-bold text-slate-800">15</p>
                    </div>
                </div>
            </div>
            
             <div class="bg-white border border-slate-200 rounded-xl p-6">
                <h2 class="text-lg font-semibold text-slate-800">🗺️ Aktivitas Regional</h2>
                <div class="mt-4">
                    <ul class="space-y-3 text-sm">
                        @forelse ($provinceLabels as $index => $label)
                        <li class="flex justify-between items-center">
                            <span class="text-slate-600">{{ $label }}</span>
                            <span class="font-semibold text-slate-800">{{ number_format($provinceData[$index]) }}</span>
                        </li>
                        @empty
                        <li class="text-center text-slate-400 text-xs">Data provinsi tidak tersedia.</li>
                        @endforelse
                    </ul>
                </div>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-6">
                 <h2 class="text-lg font-semibold text-slate-800 mb-4">⚡ Akses Cepat</h2>
                 <div class="space-y-2">
                     <a href="{{ route('outlets.create') }}" class="flex items-center w-full p-3 rounded-lg text-slate-600 hover:bg-slate-100 hover:text-blue-600 font-medium transition-colors duration-200">
                         <svg class="w-5 h-5 mr-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                         <span>Tambah Outlet Baru</span>
                     </a>
                     <a href="{{ route('package-transactions.index') }}" class="flex items-center w-full p-3 rounded-lg text-slate-600 hover:bg-slate-100 hover:text-blue-600 font-medium transition-colors duration-200">
                        <svg class="w-5 h-5 mr-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h6m-6 2.25h6M3 13.5l3 3m0 0l3-3m-3 3v-6m1.5 9H21a2.25 2.25 0 002.25-2.25V6.75a2.25 2.25 0 00-2.25-2.25H3.75A2.25 2.25 0 001.5 6.75v10.5a2.25 2.25 0 002.25 2.25z" /></svg>
                         <span>Lihat Transaksi</span>
                     </a>
                     <a href="{{ route('settings.index') }}" class="flex items-center w-full p-3 rounded-lg text-slate-600 hover:bg-slate-100 hover:text-blue-600 font-medium transition-colors duration-200">
                        <svg class="w-5 h-5 mr-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 11-3 0m3 0a1.5 1.5 0 10-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-9.75 0h9.75" /></svg>
                         <span>Pengaturan Sistem</span>
                     </a>
                 </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Konfigurasi Chart.js untuk Tema Terang (Light Mode)
    Chart.defaults.color = '#64748b'; // slate-500
    Chart.defaults.borderColor = '#e2e8f0'; // slate-200

    // Grafik Tren Bulanan
    const monthlyCtx = document.getElementById('monthlyTrendChart').getContext('2d');
    
    // Gradien untuk area chart versi light
    const gradient = monthlyCtx.createLinearGradient(0, 0, 0, 320);
    gradient.addColorStop(0, 'rgba(59, 130, 246, 0.2)');  // blue-500
    gradient.addColorStop(1, 'rgba(59, 130, 246, 0)');

    new Chart(monthlyCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($trendLabels) !!},
            datasets: [{
                label: 'Total Surat',
                data: {!! json_encode($trendData) !!},
                backgroundColor: gradient,
                borderColor: '#3b82f6', // blue-500
                borderWidth: 2,
                tension: 0.4,
                fill: true,
                pointBackgroundColor: '#fff',
                pointBorderColor: '#3b82f6', // blue-500
                pointHoverBackgroundColor: '#3b82f6',
                pointHoverBorderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#fff',
                    titleColor: '#1e293b',    // slate-800
                    bodyColor: '#475569',     // slate-600
                    padding: 10,
                    cornerRadius: 8,
                    borderColor: '#e2e8f0',  // slate-200
                    borderWidth: 1
                }
            },
            scales: {
                y: { beginAtZero: true },
                x: { grid: { display: false } }
            }
        }
    });

});
</script>
@endpush