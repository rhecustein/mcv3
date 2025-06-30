@extends('layouts.app')

@section('content')
<div class="p-6 space-y-6">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">🏆 Leaderboard Kinerja Outlet (Realtime)</h1>

    <!-- Count Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-blue-100 text-blue-800 p-4 rounded-lg shadow">
            <h3 class="text-sm font-medium">Total Semua Surat</h3>
            <p id="count-all" class="text-2xl font-bold">0</p>
        </div>
        <div class="bg-green-100 text-green-800 p-4 rounded-lg shadow">
            <h3 class="text-sm font-medium">Total Surat Hari Ini</h3>
            <p id="count-today" class="text-2xl font-bold">0</p>
        </div>
        <div class="bg-indigo-100 text-indigo-800 p-4 rounded-lg shadow">
            <h3 class="text-sm font-medium">Total Surat Bulan Ini</h3>
            <p id="count-month" class="text-2xl font-bold">0</p>
        </div>
    </div>

    <!-- Leaderboard Table -->
    <div class="bg-white rounded-xl shadow p-6 overflow-x-auto">
        <table class="w-full text-sm text-left text-gray-700" id="leaderboard-table">
            <thead class="text-xs text-gray-600 uppercase border-b">
                <tr>
                    <th>#</th>
                    <th>Outlet</th>
                    <th>Total Surat</th>
                    <th>MC</th>
                    <th>SKB</th>
                    <th>Pasien</th>
                    <th>Dokter</th>
                    <th>Perusahaan</th>
                    <th>Hari Ini</th>
                    <th>Minggu Ini</th>
                    <th>Bulan Ini</th>
                </tr>
            </thead>
            <tbody id="leaderboard-body">
                <tr><td colspan="11" class="text-center py-4">Memuat data...</td></tr>
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
    async function fetchLeaderboard() {
        try {
            const res = await fetch("{{ route('statistics.api.leaderboard') }}");
            const data = await res.json();

            document.getElementById("count-all").innerText = data.count_all ?? 0;
            document.getElementById("count-today").innerText = data.count_today ?? 0;
            document.getElementById("count-month").innerText = data.count_month ?? 0;

            const tbody = document.getElementById("leaderboard-body");
            tbody.innerHTML = '';

            const ranks = data.ranks;
            const lastIndex = ranks.length - 1;

            ranks.forEach((item, index) => {
                let rowClass = '';
                if (index < 3) {
                    rowClass = 'bg-green-100 text-green-900 font-semibold';
                } else if (index === lastIndex) {
                    rowClass = 'bg-pink-100 text-pink-900';
                }

                const row = document.createElement('tr');
                row.className = rowClass;
                row.innerHTML = `
                    <td class="py-2 px-2">${index + 1}</td>
                    <td class="font-semibold px-2">${item.name}</td>
                    <td class="px-2">${item.total_all}</td>
                    <td class="px-2">${item.total_mc}</td>
                    <td class="px-2">${item.total_skb}</td>
                    <td class="px-2">${item.total_patients}</td>
                    <td class="px-2">${item.total_doctors}</td>
                    <td class="px-2">${item.total_companies}</td>
                    <td class="px-2">${item.total_today}</td>
                    <td class="px-2">${item.total_week}</td>
                    <td class="px-2">${item.total_month}</td>
                `;
                tbody.appendChild(row);
            });
        } catch (err) {
            console.error("❌ Gagal mengambil data leaderboard:", err);
            document.getElementById("leaderboard-body").innerHTML =
                '<tr><td colspan="11" class="text-center py-4 text-red-600">Gagal memuat data.</td></tr>';
        }
    }

    document.addEventListener("DOMContentLoaded", fetchLeaderboard);
</script>
@endpush
