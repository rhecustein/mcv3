@extends('layouts.app')

@section('content')
<div class="p-6 space-y-6">

    <div class="flex justify-between items-center">
        <h2 class="text-2xl font-semibold">Monitoring Antrian Generate PDF</h2>
        <div class="flex items-center gap-2">
            <select id="status-filter" class="border rounded px-3 py-1 text-sm">
                <option value="">All Status</option>
                <option value="pending">Pending</option>
                <option value="processing">Processing</option>
                <option value="success">Success</option>
                <option value="failed">Failed</option>
            </select>
            <button onclick="fetchQueue(true)" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-sm">
                🔄 Refresh
            </button>
            <a href="{{ route('outlet.healthletter.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-3 py-1 rounded text-sm">
                ← Kembali
            </a>
        </div>
    </div>

    {{-- Status Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-white text-center text-sm font-semibold">
        <div class="bg-gray-800 rounded-lg p-4 shadow">
            Total Queue
            <div class="text-2xl mt-1" id="count-total">-</div>
        </div>
        <div class="bg-yellow-500 rounded-lg p-4 shadow">
            Pending
            <div class="text-2xl mt-1" id="count-pending">-</div>
        </div>
        <div class="bg-blue-500 rounded-lg p-4 shadow">
            Processing
            <div class="text-2xl mt-1" id="count-processing">-</div>
        </div>
        <div class="bg-red-600 rounded-lg p-4 shadow">
            Failed
            <div class="text-2xl mt-1" id="count-failed">-</div>
        </div>
    </div>

    <div class="overflow-x-auto bg-white shadow rounded-lg border">
        <table class="min-w-full text-sm text-left">
            <thead class="bg-gray-100 border-b text-xs font-semibold uppercase">
                <tr>
                    <th class="px-4 py-3">Result ID</th>
                    <th class="px-4 py-3">No Letter</th>
                    <th class="px-4 py-3">Patient Name</th>
                    <th class="px-4 py-3">Type Surat</th>
                    <th class="px-4 py-3">Outlet</th>
                    <th class="px-4 py-3">Tanggal Dibuat</th>
                    <th class="px-4 py-3">Tanggal Selesai</th>
                    <th class="px-4 py-3">Waktu Proses</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">File</th>
                    <th class="px-4 py-3">Retry</th>
                    <th class="px-4 py-3">Aksi</th>
                </tr>
            </thead>
            <tbody id="queue-body" class="divide-y divide-gray-200 bg-white"></tbody>
        </table>
    </div>
</div>

<script>
    async function fetchQueue(force = false) {
        const statusFilter = document.getElementById('status-filter').value;
        const url = "{{ route('outlet.queue.data') }}" + (statusFilter ? `?status=${statusFilter}` : '');

        try {
            const res = await fetch(url);
            const data = await res.json();

            // Count
            const total = data.length;
            const pending = data.filter(q => q.status === 'pending').length;
            const processing = data.filter(q => q.status === 'processing').length;
            const failed = data.filter(q => q.status === 'failed').length;

            document.getElementById('count-total').textContent = total;
            document.getElementById('count-pending').textContent = pending;
            document.getElementById('count-processing').textContent = processing;
            document.getElementById('count-failed').textContent = failed;

            // Table
            let html = '';
            data.forEach(q => {
                html += `<tr>
                    <td class="px-4 py-2">${q.result_id}</td>
                    <td class="px-4 py-2">${q.no_letters ?? '-'}</td>
                    <td class="px-4 py-2">${q.patient_name ?? '-'}</td>
                    <td class="px-4 py-2 uppercase">${q.type_surat ?? '-'}</td>
                    <td class="px-4 py-2">${q.outlet_name ?? '-'}</td>
                    <td class="px-4 py-2">${formatDate(q.created_at)}</td>
                    <td class="px-4 py-2">${formatDate(q.completed_at)}</td>
                    <td class="px-4 py-2">${calculateDuration(q.created_at, q.completed_at)}</td>
                    <td class="px-4 py-2">
                        <span class="px-2 py-1 text-white rounded text-xs ${getBadge(q.status)}"
                              title="${q.status === 'failed' ? q.error_log : ''}">
                            ${q.status}
                        </span>
                    </td>
                    <td class="px-4 py-2 text-xs break-all">${q.generated_file ?? '-'}</td>
                    <td class="px-4 py-2 text-center">${q.retry_count}</td>
                   <td class="px-4 py-2 space-y-1">
    ${q.status === 'failed' ? `
        <form method="POST" action="/outlet/queue-monitor/retry/${q.id}">
            @csrf
            <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-xs w-full">
                Retry
            </button>
        </form>` : ''
    }

    ${q.status === 'pending' ? `
        <form method="POST" action="/outlet/queue-monitor/delete/${q.id}" onsubmit="return confirm('Yakin ingin menghapus queue ini?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-3 py-1 rounded text-xs w-full">
                Hapus
            </button>
        </form>` : ''
    }

    ${q.result_id && q.type_surat === 'mc' ? `
        <a href="/outlet/results/mc/regenerate/${q.result_id}" class="block">
            <button type="button"
                class="bg-blue-100 hover:bg-blue-200 text-blue-800 px-3 py-1 rounded text-xs w-full">
                Regenerate MC
            </button>
        </a>` : ''
    }

    ${q.result_id && q.type_surat === 'skb' ? `
        <a href="/outlet/results/skb/regenerate/${q.result_id}" class="block">
            <button type="button"
                class="bg-green-100 hover:bg-green-200 text-green-800 px-3 py-1 rounded text-xs w-full">
                Regenerate SKB
            </button>
        </a>` : ''
    }
</td>
                </tr>`;
            });

            document.getElementById('queue-body').innerHTML = html;

        } catch (err) {
            console.error("Fetch error:", err);
        }
    }

    function formatDate(dateStr) {
        if (!dateStr) return '-';
        const d = new Date(dateStr);
        return d.toLocaleDateString() + ' ' + d.toLocaleTimeString();
    }

    function calculateDuration(start, end) {
        if (!start || !end) return '-';
        const s = new Date(start);
        const e = new Date(end);
        const diff = Math.floor((e - s) / 1000); // in seconds
        const minutes = Math.floor(diff / 60);
        const seconds = diff % 60;
        return `${minutes}m ${seconds}s`;
    }

    function getBadge(status) {
        switch (status) {
            case 'pending': return 'bg-yellow-500';
            case 'processing': return 'bg-blue-500';
            case 'success': return 'bg-green-600';
            case 'failed': return 'bg-red-600';
            default: return 'bg-gray-400';
        }
    }

    fetchQueue();
    setInterval(() => fetchQueue(), 8000);
    document.getElementById('status-filter').addEventListener('change', () => fetchQueue(true));
</script>
@endsection
