@extends('layouts.app', ['header' => 'Dasbor Superadmin'])

@section('content')
<div class="space-y-8">
    {{-- Header --}}
    <div>
        <h1 class="text-3xl font-bold tracking-tight text-slate-900">Selamat Datang Kembali, {{ strtok(Auth::user()->name, ' ') }}! 👋</h1>
        <p class="text-slate-500 mt-2 text-lg">Berikut adalah ringkasan performa sistem Surat Sehat v3 secara keseluruhan.</p>
    </div>

    {{-- Kartu Statistik --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        @php
        $statCards = [
            ['label' => 'Total Surat Dibuat', 'value' => $totalResults ?? 0, 'change' => '+2.1%', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m9.75 9.75l-2.25 2.25m0 0l-2.25 2.25m2.25-2.25l2.25 2.25m-2.25-2.25l-2.25-2.25" />', 'color' => 'blue'],
            ['label' => 'Outlet Aktif', 'value' => $totalOutlets ?? 0, 'change' => '+3', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.25M19.5 21v-7.5a.75.75 0 00-.75-.75h-3a.75.75 0 00-.75.75V21m-4.5 0H2.25m19.5 0H2.25M4.5 9.75v8.25a2.25 2.25 0 002.25 2.25h10.5a2.25 2.25 0 002.25-2.25V9.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25z" />', 'color' => 'sky'],
            ['label' => 'Total Dokter', 'value' => $totalDoctors ?? 0, 'change' => '+12', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 18.75a6 6 0 006-6v-1.5m-6 7.5a6 6 0 01-6-6v-1.5m6 7.5v3.75m-3.75 0h7.5M12 15.75a3 3 0 01-3-3V4.5a3 3 0 116 0v8.25a3 3 0 01-3 3z" />', 'color' => 'amber'],
            ['label' => 'Total Perusahaan', 'value' => $totalCompanies ?? 0, 'change' => '+1', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h6M9 11.25h6M9 15.75h6M9 20.25h6" />', 'color' => 'indigo']
        ];
        @endphp
        @foreach ($statCards as $card)
            <div class="bg-white/70 backdrop-blur-xl rounded-2xl p-5 shadow-lg ring-1 ring-black/5 transition-all duration-300 hover:shadow-xl hover:-translate-y-1 border-t-4 border-t-{{$card['color']}}-500">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm text-slate-500 mb-1">{{ $card['label'] }}</p>
                        {{-- [IMPROVEMENT] Null coalescing operator (??) untuk menangani nilai null --}}
                        <p class="text-4xl font-bold text-slate-800">{{ number_format($card['value'] ?? 0) }}</p>
                    </div>
                    <div class="w-12 h-12 flex-shrink-0 flex items-center justify-center bg-{{$card['color']}}-100 rounded-xl">
                        <svg class="w-7 h-7 text-{{$card['color']}}-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">{!! $card['icon'] !!}</svg>
                    </div>
                </div>
                <p class="text-xs text-green-600 mt-3 font-semibold">{{ $card['change'] }} dari bulan lalu</p>
            </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <div class="lg:col-span-2 space-y-8">
            {{-- Kartu Grafik --}}
            <div class="bg-white/70 backdrop-blur-xl rounded-2xl p-6 shadow-lg ring-1 ring-black/5">
                <h2 class="text-lg font-semibold mb-1 text-slate-800">📈 Tren Penerbitan Surat</h2>
                <p class="text-sm text-slate-500 mb-4">Data 12 bulan terakhir</p>
                <div class="h-80">
                    <canvas id="monthlyTrendChart"></canvas>
                </div>
            </div>
            
            {{-- Kartu Aktivitas Terbaru --}}
            <div class="bg-white/70 backdrop-blur-xl rounded-2xl p-6 shadow-lg ring-1 ring-black/5">
                <h2 class="text-lg font-semibold mb-4 text-slate-800">🕒 Aktivitas Terbaru Sistem</h2>
                <ul class="space-y-3">
                    {{-- [IMPROVEMENT] Menggunakan @forelse yang sudah aman untuk koleksi kosong --}}
                    @forelse ($recentResults ?? [] as $result)
                        <li class="flex items-center space-x-4 p-2 rounded-lg transition-colors hover:bg-slate-500/10">
                            <div class="w-10 h-10 flex-shrink-0 flex items-center justify-center bg-slate-100 rounded-full border border-slate-200">
                                <svg class="w-5 h-5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25" /></svg>
                            </div>
                            <div class="flex-1 text-sm">
                                {{-- [IMPROVEMENT] Menambahkan pengecekan untuk relasi yang mungkin null --}}
                                <p class="text-slate-700">
                                    <span class="font-semibold text-slate-900">{{ $result->patient->user->name ?? 'Pasien Dihapus' }}</span>
                                    menerima surat <span class="font-semibold text-blue-600">{{ $result->type ?? 'Tidak Diketahui' }}</span>.
                                </p>
                                <p class="text-xs text-slate-400 mt-0.5">di {{ $result->outlet->name ?? 'Outlet Dihapus' }} &middot; {{ $result->created_at->diffForHumans() }}</p>
                            </div>
                        </li>
                    @empty
                        <li class="text-center text-slate-500 py-8">Belum ada aktivitas terbaru.</li>
                    @endforelse
                </ul>
            </div>
        </div>

        <div class="lg:col-span-1 space-y-8">
            {{-- Kartu Finansial & Akses Cepat --}}
            <div class="bg-white/70 backdrop-blur-xl rounded-2xl p-6 shadow-lg ring-1 ring-black/5">
                <h2 class="text-lg font-semibold text-slate-800">⚡ Ringkasan & Aksi</h2>
                <div class="mt-4 space-y-4">
                    <div>
                        <p class="text-sm text-slate-500">Pendapatan Bulan Ini</p>
                        <p class="text-2xl font-bold text-green-600">Rp 12.550.000</p>
                    </div>
                    <div class="border-t border-slate-200/80 !my-3"></div>
                    <div class="space-y-2">
                         <a href="{{ route('outlets.create') }}" class="flex items-center w-full p-3 rounded-lg text-slate-600 bg-slate-500/5 hover:bg-slate-500/10 hover:text-blue-600 font-medium transition-colors duration-200">
                            <svg class="w-5 h-5 mr-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            <span>Tambah Outlet Baru</span>
                        </a>
                        <a href="{{ route('package-transactions.index') }}" class="flex items-center w-full p-3 rounded-lg text-slate-600 bg-slate-500/5 hover:bg-slate-500/10 hover:text-blue-600 font-medium transition-colors duration-200">
                           <svg class="w-5 h-5 mr-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h6m-6 2.25h6M3 13.5l3 3m0 0l3-3m-3 3v-6m1.5 9H21a2.25 2.25 0 002.25-2.25V6.75a2.25 2.25 0 00-2.25-2.25H3.75A2.25 2.25 0 001.5 6.75v10.5a2.25 2.25 0 002.25 2.25z" /></svg>
                           <span>Lihat Transaksi</span>
                        </a>
                    </div>
                </div>
            </div>
            
            {{-- Kartu Aktivitas Regional --}}
            <div class="bg-white/70 backdrop-blur-xl rounded-2xl p-6 shadow-lg ring-1 ring-black/5">
                <h2 class="text-lg font-semibold text-slate-800">🗺️ Aktivitas Regional Teratas</h2>
                <div class="mt-4">
                    <ul class="space-y-4 text-sm">
                        {{-- [IMPROVEMENT] Memastikan variabel tidak null sebelum digunakan --}}
                        @if(!empty($provinceLabels) && !empty($provinceData))
                            @php
                                $maxProvinceValue = $provinceData->max() ?: 1;
                            @endphp
                            @foreach ($provinceLabels as $index => $label)
                            <li>
                                <div class="flex justify-between items-center mb-1">
                                    <span class="text-slate-600 font-medium">{{ $label }}</span>
                                    <span class="font-semibold text-slate-800">{{ number_format($provinceData[$index] ?? 0) }}</span>
                                </div>
                                <div class="w-full bg-slate-200 rounded-full h-1.5">
                                    <div class="bg-sky-500 h-1.5 rounded-full" style="width: {{ (($provinceData[$index] ?? 0) / $maxProvinceValue) * 100 }}%"></div>
                                </div>
                            </li>
                            @endforeach
                        @else
                            <li class="text-center text-slate-400 text-xs py-4">Data provinsi tidak tersedia.</li>
                        @endif
                    </ul>
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
    // [IMPROVEMENT] Menambahkan pengecekan jika elemen canvas tidak ada
    const monthlyCtx = document.getElementById('monthlyTrendChart');
    if (monthlyCtx) {
        // Konfigurasi Chart.js
        Chart.defaults.color = '#64748b'; 
        Chart.defaults.borderColor = 'rgba(226, 232, 240, 0.5)';
        
        const gradient = monthlyCtx.getContext('2d').createLinearGradient(0, 0, 0, 320);
        gradient.addColorStop(0, 'rgba(59, 130, 246, 0.5)');
        gradient.addColorStop(1, 'rgba(59, 130, 246, 0)');

        new Chart(monthlyCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($trendLabels ?? []) !!}, // Memberi nilai default array kosong
                datasets: [{
                    label: 'Total Surat',
                    data: {!! json_encode($trendData ?? []) !!}, // Memberi nilai default array kosong
                    backgroundColor: gradient,
                    borderColor: '#3b82f6',
                    borderWidth: 2,
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#3b82f6',
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
                        titleColor: '#1e293b',
                        bodyColor: '#475569',
                        padding: 10,
                        cornerRadius: 8,
                        borderColor: '#e2e8f0',
                        borderWidth: 1,
                        boxShadow: '0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1)'
                    }
                },
                scales: {
                    y: { beginAtZero: true, grid: { drawBorder: false } },
                    x: { grid: { display: false } }
                }
            }
        });
    }
});
</script>
@endpush