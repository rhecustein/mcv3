@extends('layouts.app', ['header' => 'Dasbor Outlet'])

@section('content')
<div class="space-y-6">

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="grid grid-cols-1 md:grid-cols-2">
            <div class="p-6 space-y-3">
                <h1 class="text-2xl font-bold text-slate-800">Selamat Datang di, {{ $outlet->name }}!</h1>
                <p class="text-sm text-slate-500">Ini adalah pusat kendali operasional Anda. Kelola dokter, pasien, dan terbitkan surat kesehatan dengan mudah.</p>
                
                <dl class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-2 text-sm text-slate-600">
                    <div class="flex gap-2 items-center"><svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" /></svg> <span>{{ $outlet->email ?? '-' }}</span></div>
                    <div class="flex gap-2 items-center"><svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" /></svg> <span>{{ $outlet->phone ?? '-' }}</span></div>
                    <div class="flex gap-2 items-start col-span-full"><svg class="w-4 h-4 text-slate-400 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" /></svg> <span>{{ $outlet->address ?? 'Alamat belum diatur' }}</span></div>
                </dl>
            </div>
            <div class="md:w-full h-64 md:h-auto bg-slate-200">
                @if($outlet->latitude && $outlet->longitude)
                    <iframe src="https://maps.google.com/maps?q={{ $outlet->latitude }},{{ $outlet->longitude }}&hl=es&z=15&amp;output=embed" class="w-full h-full border-0" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                @else
                    <div class="flex items-center justify-center h-full text-slate-500 italic">Lokasi peta belum diatur</div>
                @endif
            </div>
        </div>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <a href="#" class="bg-blue-500 hover:bg-blue-600 text-white p-6 rounded-xl shadow-lg flex items-center gap-4 transition-all hover:scale-105">
            <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m9.75 9.75l-2.25 2.25m0 0l-2.25 2.25m2.25-2.25l2.25 2.25m-2.25-2.25l-2.25-2.25M12 21l-2.25-2.25m0 0l-2.25-2.25m2.25 2.25l2.25-2.25m2.25 2.25l-2.25 2.25M12 3l2.25 2.25m0 0l2.25 2.25M12 3l-2.25 2.25m0 0l-2.25 2.25" /></svg>
            <div>
                <span class="text-lg font-semibold">Input Surat Baru</span>
                <p class="text-sm opacity-80">Mulai buat surat sehat atau sakit.</p>
            </div>
        </a>
        <a href="#" class="bg-white hover:bg-slate-50 border border-slate-200 text-slate-800 p-6 rounded-xl shadow-sm flex items-center gap-4 transition-all hover:-translate-y-1">
             <svg class="w-10 h-10 text-slate-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m-7.5-2.962a3.752 3.752 0 01-4.133-4.133A3.752 3.752 0 019.5 3.752a3.752 3.752 0 014.133 4.133c0 1.8-1.02 3.356-2.53 4.02" /><path stroke-linecap="round" stroke-linejoin="round" d="M9.5 19.118a9.094 9.094 0 01-3.741-.479 3 3 0 01-4.682-2.72m7.5-2.962a3.752 3.752 0 004.133-4.133A3.752 3.752 0 009.5 3.752a3.752 3.752 0 00-4.133 4.133c0 1.8 1.02 3.356 2.53 4.02" /></svg>
            <div>
                <span class="text-lg font-semibold">Tambah Pasien</span>
                <p class="text-sm text-slate-500">Daftarkan pasien baru.</p>
            </div>
        </a>
        <a href="#" class="bg-white hover:bg-slate-50 border border-slate-200 text-slate-800 p-6 rounded-xl shadow-sm flex items-center gap-4 transition-all hover:-translate-y-1">
             <svg class="w-10 h-10 text-slate-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 18.75a6 6 0 006-6v-1.5m-6 7.5a6 6 0 01-6-6v-1.5m6 7.5v3.75m-3.75 0h7.5M12 15.75a3 3 0 01-3-3V4.5a3 3 0 116 0v8.25a3 3 0 01-3 3z" /></svg>
            <div>
                <span class="text-lg font-semibold">Kelola Dokter</span>
                <p class="text-sm text-slate-500">Lihat daftar dokter di outlet Anda.</p>
            </div>
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-white p-6 rounded-xl shadow-sm border border-slate-200">
            <h2 class="text-lg font-semibold text-slate-800 mb-4">📈 Grafik Penerbitan Surat</h2>
            <div class="h-80">
                <canvas id="chartSurat"></canvas>
            </div>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
            <h2 class="text-lg font-semibold text-slate-800 mb-4">📊 Komposisi Surat</h2>
            <div class="h-80 flex items-center justify-center">
                 <canvas id="compositionChart"></canvas>
            </div>
        </div>
    </div>
    
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Konfigurasi Umum Chart.js untuk Light Mode
    Chart.defaults.color = '#64748b'; // slate-500
    Chart.defaults.borderColor = '#e2e8f0'; // slate-200
    Chart.defaults.font.family = "'Inter', sans-serif";

    // Grafik Garis (Line Chart) Tren Surat
    const ctxLine = document.getElementById('chartSurat').getContext('2d');
    const gradient = ctxLine.createLinearGradient(0, 0, 0, 320);
    gradient.addColorStop(0, 'rgba(59, 130, 246, 0.2)');
    gradient.addColorStop(1, 'rgba(59, 130, 246, 0)');
    
    new Chart(ctxLine, {
        type: 'line',
        data: {
            labels: @json($labels),
            datasets: [{
                label: 'Jumlah Surat',
                data: @json($data),
                backgroundColor: gradient,
                borderColor: '#3b82f6', // blue-600
                borderWidth: 2,
                tension: 0.4,
                fill: true,
                pointBackgroundColor: '#fff',
                pointBorderColor: '#3b82f6'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true }, x: { grid: { display: false } } }
        }
    });

    // [BARU] Grafik Donat Komposisi Surat
    const ctxDoughnut = document.getElementById('compositionChart').getContext('2d');
    new Chart(ctxDoughnut, {
        type: 'doughnut',
        data: {
            // Anda perlu mengirimkan data ini dari controller
            labels: ['Surat Sehat', 'Surat Sakit (MC)'],
            datasets: [{
                label: 'Jumlah',
                data: [65, 35], // Contoh: 65 surat sehat, 35 surat sakit
                backgroundColor: ['#3b82f6', '#10b981'], // blue-600, emerald-500
                borderColor: '#fff',
                borderWidth: 4,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                }
            }
        }
    });
});
</script>
@endpush