@extends('layouts.app')

@section('title', 'Laporan Rekap & Aktivitas')

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
    <div class="bg-blue-50 p-6 rounded-xl shadow-md border border-blue-200">
        <h2 class="text-2xl font-semibold text-blue-800 mb-1">📄 Laporan Aktivitas & Rekap Data</h2>
        <p class="text-blue-600">Pilih jenis laporan, rentang tanggal, dan filter tambahan sebelum ekspor.</p>
    </div>

    <!-- 🔢 Filter Form -->
    <form id="reportForm" method="POST" action="{{ route('outlet.reports.export') }}" class="bg-white p-6 rounded-xl shadow space-y-4">
        @csrf
        <div class="grid md:grid-cols-3 gap-4">
            <div>
                <label for="type" class="block font-medium mb-1">Jenis Laporan</label>
                <select name="type" id="type" class="w-full border-gray-300 rounded px-3 py-2">
                    @foreach ($reportTypes as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="outlet_id" class="block font-medium mb-1">Outlet</label>
                <select name="outlet_id" id="outlet_id" class="w-full border-gray-300 rounded px-3 py-2">
                    <option value="">Semua</option>
                    @foreach ($outlets as $o)
                        <option value="{{ $o->id }}">{{ $o->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="doctor_id" class="block font-medium mb-1">Dokter</label>
                <select name="doctor_id" id="doctor_id" class="w-full border-gray-300 rounded px-3 py-2">
                    <option value="">Semua</option>
                    @foreach ($doctors as $d)
                        <option value="{{ $d->id }}">{{ $d->user->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="company_id" class="block font-medium mb-1">Perusahaan</label>
                <select name="company_id" id="company_id" class="w-full border-gray-300 rounded px-3 py-2">
                    <option value="">Semua</option>
                    @foreach ($companies as $c)
                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="start_date" class="block font-medium mb-1">Dari Tanggal</label>
                <input type="date" name="start_date" id="start_date" class="w-full border-gray-300 rounded px-3 py-2" value="{{ now()->startOfMonth()->toDateString() }}">
            </div>

            <div>
                <label for="end_date" class="block font-medium mb-1">Sampai Tanggal</label>
                <input type="date" name="end_date" id="end_date" class="w-full border-gray-300 rounded px-3 py-2" value="{{ now()->endOfMonth()->toDateString() }}">
            </div>
        </div>

        <!-- Tombol Aksi -->
        <div class="pt-4 flex gap-3 items-center">
            <button type="button" id="previewBtn" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded shadow">
                🔍 Tampilkan Data
            </button>
            <span id="loadingSpinner" class="hidden text-blue-600">⏳ Memuat data...</span>
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
                <thead class="bg-blue-100 text-blue-800 font-semibold">
                    <tr id="previewHeader"></tr>
                </thead>
                <tbody id="previewRows" class="text-gray-700"></tbody>
            </table>
        </div>
    </div>

    <!-- ⚠️ Error -->
    @if (session('error'))
        <div class="bg-red-100 border border-red-300 text-red-800 px-4 py-2 rounded">
            ⚠️ {{ session('error') }}
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('previewBtn').addEventListener('click', function () {
        const type = document.getElementById('type').value;
        const start = document.getElementById('start_date').value;
        const end = document.getElementById('end_date').value;
        const outlet = document.getElementById('outlet_id').value;
        const doctor = document.getElementById('doctor_id').value;
        const company = document.getElementById('company_id').value;

        document.getElementById('loadingSpinner').classList.remove('hidden');
        document.getElementById('previewContainer').classList.add('hidden');

        fetch(`/outlet/reports/preview?type=${type}&start_date=${start}&end_date=${end}&outlet_id=${outlet}&doctor_id=${doctor}&company_id=${company}`)
            .then(res => res.json())
            .then(data => {
                const header = document.getElementById('previewHeader');
                const tbody = document.getElementById('previewRows');

                header.innerHTML = '';
                tbody.innerHTML = '';

                if (data.length > 0) {
                    Object.keys(data[0]).forEach(key => {
                        header.innerHTML += `<th class="px-2 py-2 border">${key.replace(/_/g, ' ').toUpperCase()}</th>`;
                    });

                    data.forEach(row => {
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
    });
</script>
@endpush
