@extends('layouts.app', ['header' => 'Transaksi Paket Langganan'])

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
            <h1 class="text-2xl font-bold text-slate-800">Riwayat Transaksi Paket</h1>
            <p class="mt-1 text-sm text-slate-500">Pantau semua transaksi pembelian paket langganan oleh perusahaan klien.</p>
        </div>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        @php
            $statCards = [
                ['label' => 'Total Transaksi', 'value' => number_format($totalTransactions), 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h6m-6 2.25h6M3 13.5l3 3m0 0l3-3m-3 3v-6m1.5 9H21a2.25 2.25 0 002.25-2.25V6.75a2.25 2.25 0 00-2.25-2.25H3.75A2.25 2.25 0 001.5 6.75v10.5a2.25 2.25 0 002.25 2.25z" />', 'color' => 'blue'],
                ['label' => 'Pendapatan Bulan Ini', 'value' => 'Rp '.number_format($totalThisMonth), 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.75A.75.75 0 013 4.5h.75m0 0h.75A.75.75 0 015.25 6v.75m0 0v.75A.75.75 0 014.5 8.25h-.75m0 0H3.75m0 0A.75.75 0 013 9h-.75m0 0v.75a.75.75 0 01.75.75h.75m0 0H21m-12-3.75h.008v.008H9v-.008zm3.75 0h.008v.008h-.008v-.008zm3.75 0h.008v.008h-.008v-.008z" />', 'color' => 'green'],
                ['label' => 'Pendapatan Total', 'value' => 'Rp '.number_format($totalAllTime), 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 3v17.25m0 0c-1.472 0-2.882.265-4.185.75M12 20.25c1.472 0 2.882.265 4.185.75M18.75 4.97A48.416 48.416 0 0012 4.5c-2.291 0-4.545.16-6.75.47m13.5 0c1.01.143 2.01.317 3 .52m-3-.52l2.62 10.726c.122.499-.106 1.028-.589 1.202a5.988 5.988 0 01-2.186.24c-1.858 0-3.596-.584-4.893-1.588m-2.618-4.411A5.988 5.988 0 0112 15.75c-1.858 0-3.596-.584-4.893-1.588m0 0A5.988 5.988 0 013.75 14.25m0 0l2.62 10.726c.122.499.604 1.028 1.087 1.202a5.988 5.988 0 012.186.24c1.858 0 3.596-.584 4.893-1.588" />', 'color' => 'indigo'],
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
    
    <div class="bg-white border border-slate-200 rounded-xl p-6">
        <h2 class="text-lg font-semibold mb-4 text-slate-800">📊 Grafik Pendapatan</h2>
        <div class="h-80">
            <canvas id="revenueChart"></canvas>
        </div>
    </div>

    <div class="bg-white border border-slate-200 rounded-xl p-4">
        <form method="GET" class="flex flex-col md:flex-row gap-4 items-center">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama perusahaan..."
                   class="w-full md:flex-1 rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
            
            <select name="month" class="w-full md:w-auto rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                <option value="">Semua Bulan</option>
                @foreach(range(1, 12) as $m)
                    <option value="{{ $m }}" @selected(request('month') == $m)>{{ date('F', mktime(0, 0, 0, $m, 10)) }}</option>
                @endforeach
            </select>
            <select name="year" class="w-full md:w-auto rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                <option value="">Semua Tahun</option>
                @php $currentYear = date('Y'); @endphp
                @for($y = $currentYear; $y >= $currentYear - 5; $y--)
                    <option value="{{ $y }}" @selected(request('year', $currentYear) == $y)>{{ $y }}</option>
                @endfor
            </select>

            <button type="submit" class="w-full md:w-auto px-4 py-2 bg-slate-800 text-white rounded-md text-sm font-semibold hover:bg-slate-700 transition">Filter</button>
            <a href="{{ route('package-transactions.index') }}" class="w-full md:w-auto text-center px-4 py-2 bg-slate-200 text-slate-700 rounded-md text-sm font-medium hover:bg-slate-300 transition">Reset</a>
        </form>
    </div>

    <div class="bg-white border border-slate-200 rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-xs uppercase text-slate-500 tracking-wider">
                    <tr>
                        <th class="px-6 py-3 font-medium">Perusahaan</th>
                        <th class="px-6 py-3 font-medium">Detail Transaksi</th>
                        <th class="px-6 py-3 font-medium text-right">Jumlah</th>
                        <th class="px-6 py-3 font-medium text-center">Status</th>
                        <th class="px-6 py-3 font-medium text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse ($transactions as $trx)
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="font-semibold text-slate-800">{{ $trx->company->name ?? '-' }}</div>
                                <div class="text-slate-500">{{ $trx->company->email ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="font-semibold text-slate-800">{{ $trx->package->name ?? 'N/A' }}</div>
                                <div class="text-slate-500 text-xs">ID Transaksi: {{ $trx->id }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <div class="font-semibold text-slate-800">Rp {{ number_format($trx->price, 0, ',', '.') }}</div>
                            </td>
                             <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if($trx->status === 'paid')
                                    <span class="inline-flex items-center gap-1.5 px-2 py-1 text-xs font-medium text-green-700 bg-green-100 rounded-full">
                                        <span class="h-1.5 w-1.5 rounded-full bg-green-500"></span> Lunas
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2 py-1 text-xs font-medium text-amber-700 bg-amber-100 rounded-full">
                                         <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span> {{ ucfirst($trx->status) }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <a href="#" class="px-3 py-1.5 text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-md font-medium">Lihat Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-slate-500">
                                <p class="font-semibold">Data Transaksi Tidak Ditemukan</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if ($transactions->hasPages())
        <div class="mt-6">
            {{ $transactions->appends(request()->query())->links() }}
        </div>
    @endif
</div>
@endsection


@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Konfigurasi Chart.js untuk Tema Terang
    Chart.defaults.color = '#64748b'; // slate-500
    Chart.defaults.borderColor = '#e2e8f0'; // slate-200
    Chart.defaults.font.family = "'Inter', sans-serif";

    // Grafik Pendapatan
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    new Chart(revenueCtx, {
        type: 'bar',
        data: {
            // Anda perlu mengirimkan data ini dari controller
            labels: ["Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Ags", "Sep", "Okt", "Nov", "Des"],
            datasets: [{
                label: 'Pendapatan (Rp)',
                // data: [12000000, 19000000, ...], 
                data: [12,19,10,13,8,11,15,17,14,20,18,22].map(x => x * 1000000), // Contoh data
                backgroundColor: 'rgba(59, 130, 246, 0.7)',
                borderColor: 'rgba(59, 130, 246, 1)',
                borderWidth: 1,
                borderRadius: 4
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
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            if (context.parsed.y !== null) {
                                label += new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(context.parsed.y);
                            }
                            return label;
                        }
                    }
                }
            },
            scales: {
                y: { 
                    beginAtZero: true,
                    ticks: {
                        callback: function(value, index, values) {
                            return 'Rp ' + (value / 1000000) + ' Jt';
                        }
                    }
                },
                x: { grid: { display: false } }
            }
        }
    });
});
</script>
@endpush