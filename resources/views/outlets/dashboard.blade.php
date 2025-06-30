@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto p-6 space-y-6">

    <h2 class="text-2xl font-bold text-gray-800 mb-6">📊 Dashboard Outlet</h2>

    <!-- Banner Outlet dengan Informasi & Peta -->
    <div class="bg-white rounded-xl shadow border border-gray-200 overflow-hidden flex flex-col md:flex-row">
        <div class="md:w-1/2 p-6 space-y-2">
            <h3 class="text-lg font-semibold text-gray-700 mb-2">📌 Informasi Outlet</h3>
            <dl class="text-sm text-gray-600 space-y-1">
                <div><strong>Nama:</strong> {{ $outlet->name }}</div>
                <div><strong>Email:</strong> {{ $outlet->email ?? '-' }}</div>
                <div><strong>Telepon:</strong> {{ $outlet->phone ?? '-' }}</div>
                <div><strong>Lokasi:</strong> {{ $outlet->city ?? '-' }}, {{ $outlet->province ?? '-' }}</div>
                <div><strong>Alamat:</strong> {{ $outlet->address ?? '-' }}</div>

                @if($outlet->latitude && $outlet->longitude)
                    <div>
                        <a href="https://www.google.com/maps?q={{ $outlet->latitude }},{{ $outlet->longitude }}"
                           target="_blank" class="text-blue-600 underline">
                            🌍 Lihat di Google Maps
                        </a>
                    </div>
                @endif
            </dl>
        </div>

        <div class="md:w-1/2 h-64 md:h-auto">
            @if($outlet->latitude && $outlet->longitude)
                <iframe
                    src="https://maps.google.com/maps?q={{ $outlet->latitude }},{{ $outlet->longitude }}&hl=es&z=15&output=embed"
                    class="w-full h-full border-0"
                    allowfullscreen
                    loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade">
                </iframe>
            @else
                <div class="flex items-center justify-center h-full text-gray-400 italic">
                    Lokasi belum tersedia
                </div>
            @endif
        </div>
    </div>

    <!-- Statistik Ringkas -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="bg-blue-50 p-6 rounded-xl shadow text-center">
            <div class="text-5xl font-bold text-blue-700 mb-1">{{ $totalDoctors }}</div>
            <div class="text-sm text-blue-800 uppercase tracking-wide">Total Dokter</div>
        </div>
        <div class="bg-green-50 p-6 rounded-xl shadow text-center">
            <div class="text-5xl font-bold text-green-700 mb-1">{{ $totalLetters }}</div>
            <div class="text-sm text-green-800 uppercase tracking-wide">Total Surat Diterbitkan</div>
        </div>
    </div>

    <!-- Riwayat Surat Terbaru -->
    <div class="bg-white p-5 rounded-xl shadow border border-gray-100">
        <h4 class="text-md font-semibold text-gray-700 mb-4">📝 Riwayat Surat Terbaru</h4>

        @if($latestLetters->count())
            <table class="w-full text-sm text-gray-700">
                <thead class="bg-gray-100 text-xs uppercase">
                    <tr>
                        <th class="p-2 text-left">Pasien</th>
                        <th class="p-2 text-left">Jenis Surat</th>
                        <th class="p-2 text-left">Dibuat oleh</th>
                        <th class="p-2 text-left">Tanggal</th>
                        <th class="p-2 text-right">Lihat</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach($latestLetters as $letter)
                        <tr>
                            <td class="p-2">{{ $letter->patient->full_name ?? '-' }}</td>
                            <td class="p-2">{{ $letter->type ?? '-' }}</td>
                            <td class="p-2">{{ $letter->doctor->user->name ?? '-' }}</td>
                            <td class="p-2">{{ \Carbon\Carbon::parse($letter->created_at)->format('d M Y') }}</td>
                            <td class="p-2 text-right">
                                @if(in_array($letter->type, ['mc', 'skb']) && $letter->document_name)
                                    @php
                                        $previewRoute = $letter->type === 'mc'
                                            ? route('outlet.results.mc.preview', Crypt::encrypt($letter->id))
                                            : route('outlet.results.skb.preview', Crypt::encrypt($letter->id));
                                    @endphp

                                    <a href="{{ $previewRoute }}"
                                    target="_blank"
                                    class="inline-flex items-center gap-1 bg-indigo-100 hover:bg-indigo-200 text-indigo-800 text-xs font-medium px-3 py-1 rounded transition">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12H9m12 0A9 9 0 1112 3a9 9 0 019 9z" />
                                        </svg>
                                        Preview
                                    </a>
                                @else
                                    <span class="text-gray-400 text-xs italic">Tidak tersedia</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p class="text-sm text-gray-500">Belum ada surat diterbitkan.</p>
        @endif
    </div>

    <!-- Grafik Surat -->
    <div class="bg-white p-5 rounded-xl shadow border">
        <h4 class="font-semibold text-gray-700 mb-2">📈 Grafik Surat per Bulan</h4>
        <canvas id="chartSurat"></canvas>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('chartSurat').getContext('2d');
    const chartSurat = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: @json($labels),
            datasets: [{
                label: 'Jumlah Surat',
                data: @json($data),
                backgroundColor: 'rgba(34, 197, 94, 0.6)',
                borderColor: 'rgba(34, 197, 94, 1)',
                borderWidth: 1,
                borderRadius: 6
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
</script>
@endpush
