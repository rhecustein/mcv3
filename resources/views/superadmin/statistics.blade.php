@extends('layouts.app', ['header' => 'Statistik & Distribusi'])

@section('content')
<div class="space-y-6">

    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Statistik Outlet & Distribusi</h1>
            <p class="mt-1 text-sm text-slate-500">Analisis performa outlet dan sebaran geografis penerbitan surat.</p>
        </div>
        <div class="flex items-center gap-2 p-1 bg-slate-100 border border-slate-200 rounded-lg">
            @php
                $currentPeriod = request('period', 'all_time');
                $periods = [
                    'today' => 'Hari Ini',
                    'last_7_days' => '7 Hari',
                    'this_month' => 'Bulan Ini',
                    'all_time' => 'Semua Waktu',
                ];
            @endphp
            @foreach($periods as $key => $label)
                <a href="{{ route('statistics.index', ['period' => $key]) }}"
                   class="px-3 py-1.5 text-sm font-medium rounded-md transition-colors duration-200 {{ $currentPeriod === $key ? 'bg-white shadow text-blue-600' : 'text-slate-500 hover:text-slate-800' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl shadow-md border border-slate-200 p-6">
            <h2 class="text-xl font-semibold text-slate-800 mb-4">🏆 Papan Peringkat Outlet Teratas</h2>
            <ol class="space-y-3">
                @forelse ($outletRanks as $index => $outlet)
                    <li class="flex items-center gap-4 p-3 rounded-lg transition-colors hover:bg-slate-50">
                        @php
                            $rank = $index + 1;
                            $rankColor = 'text-slate-500';
                            $rankIcon = "#{$rank}";
                            if ($rank == 1) { $rankColor = 'text-amber-400'; $rankIcon = '🥇'; }
                            if ($rank == 2) { $rankColor = 'text-slate-400'; $rankIcon = '🥈'; }
                            if ($rank == 3) { $rankColor = 'text-amber-600'; $rankIcon = '🥉'; }
                        @endphp
                        <div class="text-lg font-bold w-8 text-center {{ $rankColor }}">{{ $rankIcon }}</div>
                        <div class="flex-1">
                            <div class="font-semibold text-slate-800">{{ $outlet->name }}</div>
                            <div class="text-xs text-slate-500">{{ $outlet->city ?? 'Lokasi tidak diketahui' }}</div>
                        </div>
                        <div class="text-lg font-bold text-blue-600">{{ number_format($outlet->total) }} <span class="text-sm font-medium text-slate-500">surat</span></div>
                    </li>
                @empty
                    <p class="text-center text-slate-500 py-8">Tidak ada data peringkat untuk ditampilkan.</p>
                @endforelse
            </ol>
        </div>

        <div class="bg-white rounded-xl shadow-md border border-slate-200 p-6">
            <h2 class="text-xl font-semibold text-slate-800 mb-4">📍 Peta Sebaran Penerbitan Surat</h2>
            <div id="mapRiauSurat" class="w-full h-96 rounded-lg border border-slate-200"></div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-md border border-slate-200 p-6">
        <h2 class="text-xl font-semibold text-slate-800 mb-4">📊 Demografi Surat Berdasarkan Kota</h2>
         <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
             <div class="h-80">
                <canvas id="cityDemographicChart"></canvas>
            </div>
            <div class="text-sm">
                <p class="font-semibold text-slate-700 mb-2">Ringkasan Data:</p>
                <div class="space-y-2">
                    <div class="flex justify-between items-center">
                        <span class="text-slate-500">Total Kota/Kab.</span>
                        <span class="font-bold text-slate-800">{{ $cityLabels->count() }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-slate-500">Kota Tertinggi</span>
                        <span class="font-bold text-green-600">{{ $maxCity->created_city ?? '—' }} ({{ number_format($maxCity->total ?? 0) }})</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-slate-500">Kota Terendah</span>
                        <span class="font-bold text-red-600">{{ $minCity->created_city ?? '—' }} ({{ number_format($minCity->total ?? 0) }})</span>
                    </div>
                </div>
            </div>
         </div>
    </div>
</div>
@endsection

@push('styles')
{{-- Gunakan Peta & Cluster versi CDN --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.Default.css" />
<style>
    /* [PENINGKATAN] Kustomisasi Popup Peta untuk Light Mode */
    .leaflet-popup-content-wrapper {
        background: #ffffff;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    .leaflet-popup-tip-container {
        display: none;
    }
    .leaflet-popup-content {
        margin: 12px;
        font-size: 14px;
        color: #334155; /* slate-700 */
    }
    .leaflet-popup-content strong {
        color: #1e293b; /* slate-800 */
    }
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet.markercluster@1.5.3/dist/leaflet.markercluster.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // [PENINGKATAN] Menggunakan tema peta "Positron" dari CartoDB untuk light mode
    const mapTileUrl = 'https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png';
    const mapAttribution = '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>';
    
    // Map Sebaran Surat
    const mapSurat = L.map('mapRiauSurat').setView([0.5104, 101.4383], 7);
    L.tileLayer(mapTileUrl, { attribution: mapAttribution }).addTo(mapSurat);
    
    const suratCluster = L.markerClusterGroup();
    const resultCoordinates = @json($resultCoordinates);

    resultCoordinates.forEach(result => {
        if(result.created_latitude && result.created_longitude) {
            const popupContent = `<strong>${result.outlet_name}</strong><br>${result.created_city}`;
            const marker = L.marker([result.created_latitude, result.created_longitude]).bindPopup(popupContent);
            suratCluster.addLayer(marker);
        }
    });
    mapSurat.addLayer(suratCluster);

    // Konfigurasi Chart.js untuk Light Mode
    Chart.defaults.color = '#64748b'; // slate-500
    Chart.defaults.borderColor = '#e2e8f0'; // slate-200
    Chart.defaults.font.family = "'Inter', sans-serif";

    // Chart Demografi Kota
    const cityCtx = document.getElementById('cityDemographicChart').getContext('2d');
    new Chart(cityCtx, {
        type: 'bar',
        data: {
            labels: @json($cityLabels),
            datasets: [{
                label: 'Jumlah Surat',
                data: @json($cityData),
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
                legend: { display: false }
            },
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 1 } }
            }
        }
    });
});
</script>
@endpush