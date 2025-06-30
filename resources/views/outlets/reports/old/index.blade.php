@extends('layouts.app')

@section('title', 'Laporan Data Lama')

@push('styles')
<style>
    .disabled {
        opacity: 0.5;
        pointer-events: none;
    }
</style>
@endpush

@section('content')
<div class="container mx-auto px-4 py-6 space-y-6">

    <!-- 📄 Header -->
    <div class="bg-yellow-50 p-6 rounded-xl shadow-md border border-yellow-200">
        <h2 class="text-2xl font-semibold text-yellow-800 mb-1">📁 Laporan Surat Lama</h2>
        <p class="text-yellow-700">Data bersumber dari arsip statis pada tabel <code>result_old</code>.</p>
    </div>

    <!-- 🔢 Filter Form -->
    <form id="reportFormOld" method="POST" action="{{ route('outlet.reports.old.export') }}" class="bg-white p-6 rounded-xl shadow space-y-4">
        @csrf
        <div class="grid md:grid-cols-3 gap-4">
            <div>
                <label for="type" class="block font-medium mb-1">Jenis Surat</label>
                <select name="type" id="type" class="w-full border-gray-300 rounded px-3 py-2">
                    <option value="mc">MC (Medical Certificate)</option>
                    <option value="skb">SKB (Surat Keterangan Bebas)</option>
                </select>
            </div>

            <div>
                <label for="start_date" class="block font-medium mb-1">Dari Tanggal</label>
                <input type="date" name="start_date" id="start_date" class="w-full border-gray-300 rounded px-3 py-2"
                    value="{{ now()->startOfYear()->toDateString() }}">
            </div>

            <div>
                <label for="end_date" class="block font-medium mb-1">Sampai Tanggal</label>
                <input type="date" name="end_date" id="end_date" class="w-full border-gray-300 rounded px-3 py-2"
                    value="{{ now()->toDateString() }}">
            </div>
        </div>

        <!-- Tombol Aksi -->
        <div class="pt-4 flex gap-3 items-center">
            <button type="button" id="previewBtnOld" class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded shadow">
                🔍 Tampilkan Data
            </button>
            <span id="loadingSpinner" class="hidden text-yellow-600">⏳ Memuat data...</span>
            <button type="submit" name="format" value="pdf" id="pdfBtn" class="bg-red-600 text-white px-4 py-2 rounded shadow disabled" disabled>
                🔽️ Unduh PDF
            </button>
            <button type="submit" name="format" value="excel" id="excelBtn" class="bg-green-600 text-white px-4 py-2 rounded shadow disabled" disabled>
                🔽️ Unduh Excel
            </button>
        </div>
    </form>

    <!-- 📊 Preview Data -->
    <div id="previewContainer" class="bg-white p-4 rounded-xl shadow hidden">
        <h3 class="text-lg font-semibold text-gray-700 mb-3">📊 Preview Data</h3>
        <div class="overflow-x-auto">
            <table id="previewTable" class="min-w-full text-sm border border-gray-200">
                <thead class="bg-yellow-100 text-yellow-800 font-semibold">
                    <tr id="previewHeader"></tr>
                </thead>
                <tbody id="previewRows" class="text-gray-700"></tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div id="paginationNav" class="flex justify-center gap-4 mt-4 text-sm font-semibold"></div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    let currentPage = 1;

    function fetchPreview(page = 1) {
        const type = document.getElementById('type').value;
        const start = document.getElementById('start_date').value;
        const end = document.getElementById('end_date').value;

        document.getElementById('loadingSpinner').classList.remove('hidden');
        document.getElementById('previewContainer').classList.add('hidden');

        fetch(`/outlet/reports/old/preview?type=${type}&start_date=${start}&end_date=${end}&page=${page}`)
            .then(res => res.json())
            .then(data => {
                const header = document.getElementById('previewHeader');
                const tbody = document.getElementById('previewRows');
                header.innerHTML = '';
                tbody.innerHTML = '';

                if (data.data.length > 0) {
                    Object.keys(data.data[0]).forEach(key => {
                        header.innerHTML += `<th class="px-2 py-2 border">${key.replace(/_/g, ' ').toUpperCase()}</th>`;
                    });

                    data.data.forEach(row => {
                        let rowHTML = '<tr>';
                        Object.values(row).forEach(val => {
                            rowHTML += `<td class="px-2 py-2 border">${val ?? '-'}</td>`;
                        });
                        rowHTML += '</tr>';
                        tbody.innerHTML += rowHTML;
                    });

                    document.getElementById('pdfBtn').classList.remove('disabled');
                    document.getElementById('excelBtn').classList.remove('disabled');
                    document.getElementById('pdfBtn').disabled = false;
                    document.getElementById('excelBtn').disabled = false;

                    updatePagination(data.current_page, data.last_page);
                } else {
                    header.innerHTML = '<th class="px-2 py-2 text-center">Tidak ada data ditemukan</th>';
                }

                document.getElementById('previewContainer').classList.remove('hidden');
                document.getElementById('loadingSpinner').classList.add('hidden');
            })
            .catch(err => {
                alert('Gagal memuat data.');
                console.error(err);
                document.getElementById('loadingSpinner').classList.add('hidden');
            });
    }

    function updatePagination(current, last) {
        const nav = document.getElementById('paginationNav');
        nav.innerHTML = '';

        if (last <= 1) return;

        if (current > 1) {
            nav.innerHTML += `<button onclick="fetchPreview(${current - 1})" class="text-yellow-600 px-2">← Prev</button>`;
        }

        nav.innerHTML += `<span>Halaman ${current} dari ${last}</span>`;

        if (current < last) {
            nav.innerHTML += `<button onclick="fetchPreview(${current + 1})" class="text-yellow-600 px-2">Next →</button>`;
        }
    }

    document.getElementById('previewBtnOld').addEventListener('click', () => {
        fetchPreview(1);
    });
</script>
@endpush
