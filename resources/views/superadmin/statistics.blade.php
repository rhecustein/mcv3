@extends('layouts.app')

@section('content')
<div class="p-6 space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-3xl font-bold text-gray-800">📍 Statistik Outlet & Distribusi Riau</h1>
        <a href="{{ route('statistics.leaderboard') }}"
           class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition">
            🏆 Leaderboard Realtime
        </a>
    </div>

    <!-- Rank dan Peta Penerbitan -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- 🏅 Rank Outlet -->
        <div class="bg-white rounded-xl shadow p-6">
            <h2 class="text-xl font-semibold text-gray-700 mb-4">🏅 10 Besar Outlet Penerbit Surat</h2>
            <div class="space-y-3">
                @foreach ($outletRanks as $index => $outlet)
                    @php
                        $colors = [
                            'bg-green-100 text-green-800 border-green-300',
                            'bg-lime-100 text-lime-800 border-lime-300',
                            'bg-emerald-100 text-emerald-800 border-emerald-300',
                            'bg-yellow-100 text-yellow-800 border-yellow-300',
                            'bg-orange-100 text-orange-800 border-orange-300',
                            'bg-red-100 text-red-800 border-red-300',
                            'bg-pink-100 text-pink-800 border-pink-300',
                            'bg-purple-100 text-purple-800 border-purple-300',
                            'bg-indigo-100 text-indigo-800 border-indigo-300',
                            'bg-blue-100 text-blue-800 border-blue-300'
                        ];
                        $style = $colors[$index] ?? 'bg-gray-100 text-gray-800 border-gray-300';
                    @endphp
                    <div class="flex items-center justify-between border rounded px-4 py-2 {{ $style }}">
                        <span class="font-medium">#{{ $index + 1 }} {{ $outlet->name }}</span>
                        <span class="text-sm">{{ $outlet->total }} surat</span>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- 🗘️ Lokasi Penerbitan -->
        <div class="bg-white rounded-xl shadow p-6">
            <h2 class="text-xl font-semibold text-gray-700 mb-4">🗘️ Lokasi Penerbitan Surat</h2>
            <div id="mapRiauSurat" class="w-full h-96 rounded"></div>
        </div>
    </div>

    <!-- 🔐 Lokasi Login -->
    <div class="bg-white rounded-xl shadow p-6">
        <div class="flex justify-between items-center mb-2">
            <h2 class="text-xl font-semibold text-gray-700">🔐 Lokasi Login MCV3</h2>
            <span class="text-sm text-gray-600">Total sesi login: 
                <strong class="text-blue-700">{{ $sessionLogins->count() }}</strong>
            </span>
        </div>
        <div id="mapRiauLogin" class="w-full h-96 rounded"></div>
    </div>

    <!-- 📊 Statistik Demografi -->
    <div class="bg-white rounded-xl shadow p-6">
        <h2 class="text-xl font-semibold text-gray-700 mb-4">🏣️ Demografi Surat Berdasarkan Kota (Riau)</h2>
        <div class="text-sm text-gray-500 mb-4">
            Total kota: <span class="font-semibold text-gray-700">{{ $cityLabels->count() }}</span><br>
            Kota terbanyak: <span class="font-semibold text-green-700">{{ $maxCity->created_city ?? '—'}}</span> ({{ $maxCity->total ?? '—' }} surat)<br>
            Kota tersedikit: <span class="font-semibold text-red-700">{{ $minCity->created_city ?? '—'}}</span> ({{ $minCity->total ?? '—'}} surat)
        </div>
        <canvas id="cityDemographicChart" class="w-full h-64"></canvas>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster/dist/MarkerCluster.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster/dist/MarkerCluster.Default.css" />
<style>
.marker-wave {
    position: relative;
    width: 14px;
    height: 14px;
    background: #3b82f6;
    border-radius: 50%;
    box-shadow: 0 0 0 rgba(59, 130, 246, 0.4);
    animation: pulse 2s infinite;
}
@keyframes pulse {
    0% { box-shadow: 0 0 0 0 rgba(59, 130, 246, 0.4); }
    70% { box-shadow: 0 0 0 12px rgba(59, 130, 246, 0); }
    100% { box-shadow: 0 0 0 0 rgba(59, 130, 246, 0); }
}
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://unpkg.com/leaflet.markercluster/dist/leaflet.markercluster.js"></script>
<script>
const sessionLogins = @json($sessionLogins);

// Map Surat
const mapRiauSurat = L.map('mapRiauSurat').setView([0.5104, 101.4383], 7);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; OpenStreetMap contributors'
}).addTo(mapRiauSurat);
@foreach ($resultCoordinates as $result)
    @if($result->created_latitude && $result->created_longitude)
        L.marker([{{ $result->created_latitude }}, {{ $result->created_longitude }}])
            .addTo(mapRiauSurat)
            .bindPopup("<strong>{{ $result->outlet_name }}</strong><br>{{ $result->created_city }}");
    @endif
@endforeach

// Map Login
const mapRiauLogin = L.map('mapRiauLogin').setView([0.5104, 101.4383], 7);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; OpenStreetMap contributors'
}).addTo(mapRiauLogin);
const loginCluster = L.markerClusterGroup();
const ipGrouped = {};
sessionLogins.forEach(login => {
    if (!login.latitude || !login.longitude || !login.ip_address) return;
    if (!ipGrouped[login.ip_address]) ipGrouped[login.ip_address] = [];
    ipGrouped[login.ip_address].push(login);
});
Object.entries(ipGrouped).forEach(([ip, logins]) => {
    const first = logins[0];
    const users = logins.map(l => l.user?.name || 'Anonim');
    const uniqueUsers = [...new Set(users)];
    const popup = `
        <strong>${first.city ?? 'Tidak diketahui'}</strong><br>
        IP: ${ip}<br>
        Pengguna: <ul class="list-disc pl-4 text-sm text-gray-700">
        ${uniqueUsers.map(u => `<li>${u}</li>`).join('')}
        </ul>
        Login: ${new Date(first.logged_in_at).toLocaleString('id-ID')}
    `;
    const customIcon = L.divIcon({ className: 'marker-wave', iconSize: [14, 14] });
    const marker = L.marker([first.latitude, first.longitude], { icon: customIcon }).bindPopup(popup);
    loginCluster.addLayer(marker);
});
mapRiauLogin.addLayer(loginCluster);

// Chart Kota
const cityCtx = document.getElementById('cityDemographicChart').getContext('2d');
new Chart(cityCtx, {
    type: 'bar',
    data: {
        labels: @json($cityLabels),
        datasets: [{
            label: 'Jumlah Surat',
            data: @json($cityData),
            backgroundColor: 'rgba(59, 130, 246, 0.7)'
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: { beginAtZero: true, ticks: { stepSize: 1 } }
        }
    }
});
</script>
@endpush
