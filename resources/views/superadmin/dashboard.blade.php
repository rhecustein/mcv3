@extends('layouts.app')

@section('content')
<div class="p-6 space-y-6">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">📊 Superadmin Dashboard</h1>

    <!-- Ringkasan Statistik -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        @foreach ([
            ['label' => 'Total Surat', 'value' => $totalResults, 'color' => 'blue', 'icon' => '📄'],
            ['label' => 'Surat SKB', 'value' => $totalSKB, 'color' => 'green', 'icon' => '✅'],
            ['label' => 'Surat MC', 'value' => $totalMC, 'color' => 'red', 'icon' => '❌'],
            ['label' => 'Total Admin', 'value' => $totalAdmins, 'color' => 'purple', 'icon' => '👤'],
            ['label' => 'Total Outlet', 'value' => $totalOutlets, 'color' => 'yellow', 'icon' => '🏢'],
            ['label' => 'Total Dokter', 'value' => $totalDoctors, 'color' => 'orange', 'icon' => '🧑‍⚕️'],
            ['label' => 'Total Pasien', 'value' => $totalPatients, 'color' => 'gray', 'icon' => '🧍'],
            ['label' => 'Total Perusahaan', 'value' => $totalCompanies, 'color' => 'teal', 'icon' => '🏭'],
        ] as $card)
        <div class="bg-gradient-to-r from-{{ $card['color'] }}-100 to-{{ $card['color'] }}-200 rounded-2xl shadow-lg p-5 transition hover:scale-105 duration-300">
            <h2 class="text-sm text-{{ $card['color'] }}-800">{{ $card['icon'] }} {{ $card['label'] }}</h2>
            <p class="text-3xl font-bold text-{{ $card['color'] }}-900 mt-1">{{ $card['value'] }}</p>
        </div>
        @endforeach
    </div>

    <!-- Grafik Tren Surat Bulanan -->
    <div class="bg-white rounded-xl shadow p-6">
        <h2 class="text-xl font-semibold mb-4 text-gray-700">📈 Tren Surat Bulanan</h2>
        <canvas id="monthlyTrendChart" class="w-full h-64"></canvas>
    </div>

    <!-- Pie Chart Distribusi Surat per Provinsi -->
    <div class="bg-white rounded-xl shadow p-6">
        <h2 class="text-xl font-semibold mb-4 text-gray-700">🗺️ Distribusi Surat per Provinsi</h2>
        <canvas id="provinceDistributionChart" class="w-full h-64"></canvas>
    </div>

    <!-- Top 5 Admin Aktif -->
    <div class="bg-white rounded-xl shadow p-6">
        <h2 class="text-xl font-semibold mb-4 text-gray-700">👤 Top 5 Admin Aktif</h2>
        <ul class="divide-y divide-gray-100">
            @foreach ($topAdmins as $admin)
            <li class="py-3 flex justify-between text-gray-700">
                <span class="font-medium">{{ $admin->user->name ?? '-' }}</span>
                <span class="text-sm">{{ $admin->outlets_count }} outlet, {{ $admin->doctors_count }} dokter, {{ $admin->patients_count }} pasien</span>
            </li>
            @endforeach
        </ul>
    </div>

    <!-- Aktivitas Terbaru -->
    <div class="bg-white rounded-xl shadow p-6">
        <h2 class="text-xl font-semibold mb-4 text-gray-700">🕒 Aktivitas Terbaru</h2>
        <ul class="divide-y divide-gray-100">
            @foreach ($recentResults as $result)
            <li class="py-3 text-gray-700">
                📄 <strong>{{ $result->patient->user->name ?? 'Pasien' }}</strong> membuat surat <em>{{ $result->type }}</em> di outlet {{ $result->outlet->name ?? '-' }}
                <span class="text-xs text-gray-500">({{ $result->created_at->diffForHumans() }})</span>
            </li>
            @endforeach
        </ul>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const monthlyCtx = document.getElementById('monthlyTrendChart').getContext('2d');
new Chart(monthlyCtx, {
    type: 'line',
    data: {
        labels: {!! json_encode($trendLabels) !!},
        datasets: [
            {
                label: 'Total Surat',
                data: {!! json_encode($trendData) !!},
                backgroundColor: 'rgba(59, 130, 246, 0.2)',
                borderColor: 'rgba(59, 130, 246, 1)',
                borderWidth: 2,
                tension: 0.4,
                fill: true
            }
        ]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, ticks: { color: '#4B5563' } },
            x: { ticks: { color: '#4B5563' } }
        }
    }
});

const provinceCtx = document.getElementById('provinceDistributionChart').getContext('2d');
new Chart(provinceCtx, {
    type: 'doughnut',
    data: {
        labels: {!! json_encode($provinceLabels) !!},
        datasets: [
            {
                data: {!! json_encode($provinceData) !!},
                backgroundColor: [
                    '#60A5FA', '#34D399', '#FBBF24', '#F87171', '#A78BFA', '#F472B6', '#10B981'
                ]
            }
        ]
    },
    options: {
        responsive: true,
        plugins: { legend: { position: 'bottom' } }
    }
});
</script>
@endpush