@extends('layouts.app')

@section('title', 'Statistik Aktivitas Surat')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    #map {
        height: 400px;
        width: 100%;
        border-radius: 0.75rem;
    }
</style>
@endpush

@section('content')
<div class="container mx-auto px-4 py-6 space-y-6">

    <!-- 🗺️ Peta Lokasi Klinik -->
    <div class="bg-white rounded-2xl shadow-md p-4">
        <h2 class="text-xl font-semibold mb-4">📍 Lokasi Klinik Anda</h2>
        <div id="map"></div>
    </div>

    <!-- 📊 Ringkasan -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        @foreach ($summaryStats as $stat)
        <div class="bg-white p-4 rounded-lg shadow text-center">
            <p class="text-sm text-gray-500">{{ $stat['label'] }}</p>
            <p class="text-xl font-bold">{{ $stat['value'] }}</p>
        </div>
        @endforeach
    </div>

    <!-- 📈 Grafik -->
    <div class="bg-white rounded-2xl shadow-md p-4">
        <h2 class="text-xl font-semibold mb-4">📈 Surat Terbit per Hari (Bulan Ini)</h2>
        <canvas id="outletLineChart" class="w-full h-32"></canvas>
    </div>

    <!-- 🏆 Ranking -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">

        <!-- Outlet -->
        <div class="bg-blue-50 rounded-xl p-4 shadow border border-blue-100">
            <h3 class="font-semibold text-lg text-blue-700 mb-2">🏥 Klinik Penerbit Surat</h3>
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-blue-600 border-b border-blue-200">
                        <th class="text-left py-1">#</th>
                        <th class="text-left">Outlet</th>
                        <th class="text-center">Bulan</th>
                        <th class="text-center">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($outletRanking as $i => $row)
                    <tr class="border-t border-blue-100 hover:bg-blue-100/30">
                        <td class="py-1">{{ $i + 1 }}</td>
                        <td>{{ $row['name'] }}</td>
                        <td class="text-center">{{ $row['bulan'] }}</td>
                        <td class="text-center">{{ $row['total'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Dokter -->
        <div class="bg-green-50 rounded-xl p-4 shadow border border-green-100">
            <h3 class="font-semibold text-lg text-green-700 mb-2">👨‍⚕️ Dokter Penerbit Surat</h3>
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-green-600 border-b border-green-200">
                        <th class="text-left py-1">#</th>
                        <th class="text-left">Dokter</th>
                        <th class="text-center">Bulan</th>
                        <th class="text-center">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($doctorRanking as $i => $row)
                    <tr class="border-t border-green-100 hover:bg-green-100/30">
                        <td class="py-1">{{ $i + 1 }}</td>
                        <td>{{ $row['name'] }}</td>
                        <td class="text-center">{{ $row['bulan'] }}</td>
                        <td class="text-center">{{ $row['total'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Perusahaan -->
        <div class="bg-yellow-50 rounded-xl p-4 shadow border border-yellow-100">
            <h3 class="font-semibold text-lg text-yellow-700 mb-2">🏢 Perusahaan Teraktif</h3>
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-yellow-600 border-b border-yellow-200">
                        <th class="text-left py-1">#</th>
                        <th class="text-left">Perusahaan</th>
                        <th class="text-center">Bulan</th>
                        <th class="text-center">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($companyRanking as $i => $row)
                    <tr class="border-t border-yellow-100 hover:bg-yellow-100/30">
                        <td class="py-1">{{ $i + 1 }}</td>
                        <td>{{ $row['name'] }}</td>
                        <td class="text-center">{{ $row['bulan'] }}</td>
                        <td class="text-center">{{ $row['total'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    const kliniks = @json($mapKliniks);
    const map = L.map('map').setView([1.1, 104.05], 9);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    kliniks.forEach(k => {
        const markerHtml = `
            <div class="relative ${k.has_new_surat ? 'animate-bounce' : ''}">
                <div class="bg-blue-600 text-white text-xs px-2 py-1 rounded-lg shadow-lg">
                    🏥 ${k.name}<br><span class="font-bold">${k.surat_count} Surat</span>
                </div>
                ${k.has_new_surat ? `<div class="absolute inset-0 rounded-lg bg-blue-400 opacity-50 animate-ping z-[-1]"></div>` : ''}
            </div>
        `;

        const customIcon = L.divIcon({
            className: '',
            html: markerHtml,
            iconSize: [120, 40],
            popupAnchor: [0, -10]
        });

        const marker = L.marker([k.lat, k.lon], { icon: customIcon }).addTo(map);
        marker.bindPopup(`
            <b>${k.name}</b><br>
            🗒️ Total Surat Hari Ini: ${k.surat_count}<br>
            ${k.has_new_surat ? '🆕 Ada surat baru hari ini!' : ''}
        `);
    });
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
                backgroundColor: 'transparent',
                tension: 0.3
            }))
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom' },
                tooltip: { mode: 'index', intersect: false }
            },
            interaction: { mode: 'nearest', axis: 'x', intersect: false },
            scales: {
                x: { title: { display: true, text: 'Tanggal' } },
                y: {
                    title: { display: true, text: 'Jumlah Surat' },
                    beginAtZero: true,
                    ticks: { stepSize: 1 }
                }
            }
        }
    });
</script>
@endpush
