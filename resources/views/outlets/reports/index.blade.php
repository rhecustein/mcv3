@extends('layouts.app')

@section('title', 'Laporan & Analytics')

@push('styles')
<style>
    .disabled {
        opacity: 0.6;
        pointer-events: none;
        cursor: not-allowed;
    }
    .loading-dots {
        display: inline-block;
        position: relative;
        width: 80px;
        height: 20px;
    }
    .loading-dots div {
        position: absolute;
        top: 8px;
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: #3b82f6;
        animation-timing-function: cubic-bezier(0, 1, 1, 0);
    }
    .loading-dots div:nth-child(1) {
        left: 8px;
        animation: loading-dots1 0.6s infinite;
    }
    .loading-dots div:nth-child(2) {
        left: 8px;
        animation: loading-dots2 0.6s infinite;
    }
    .loading-dots div:nth-child(3) {
        left: 32px;
        animation: loading-dots2 0.6s infinite;
    }
    .loading-dots div:nth-child(4) {
        left: 56px;
        animation: loading-dots3 0.6s infinite;
    }
    @keyframes loading-dots1 {
        0% { transform: scale(0); }
        100% { transform: scale(1); }
    }
    @keyframes loading-dots3 {
        0% { transform: scale(1); }
        100% { transform: scale(0); }
    }
    @keyframes loading-dots2 {
        0% { transform: translate(0, 0); }
        100% { transform: translate(24px, 0); }
    }
</style>
@endpush

@section('content')
<div class="container mx-auto px-4 py-8 space-y-8">

    <!-- Enhanced Header -->
    <div class="bg-gradient-to-br from-blue-50 to-indigo-100 rounded-2xl shadow-lg border border-blue-200 overflow-hidden">
        <div class="p-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-6">
                    <div class="flex items-center justify-center w-20 h-20 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl shadow-lg">
                        <svg class="w-10 h-10 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-4xl font-bold text-slate-800 mb-2">Laporan & Analytics</h1>
                        <p class="text-slate-600 text-lg">Buat, preview, dan ekspor laporan komprehensif untuk analisis data</p>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <div class="text-right">
                        <p class="text-sm text-slate-500">Laporan Tersedia</p>
                        <p class="text-2xl font-bold text-blue-600">{{ count($reportTypes) }}</p>
                    </div>
                    <div class="w-3 h-3 bg-emerald-400 rounded-full animate-pulse"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Report Types Overview -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @php
            $reportIcons = [
                'rekap_surat' => '<path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />',
                'aktivitas_dokter' => '<path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />',
                'aktivitas_perusahaan' => '<path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m2.25-18v18m13.5-18v18m2.25-18v18M6.75 9h.008v.008H6.75V9z" />',
                'statistik_penyakit' => '<path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />',
                'feedback_pasien' => '<path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 01.865-.501 48.172 48.172 0 003.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z" />',
                'log_pengguna' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" />',
                'rekap_pasien' => '<path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0z" />'
            ];
            $reportColors = [
                'rekap_surat' => 'blue',
                'aktivitas_dokter' => 'emerald',
                'aktivitas_perusahaan' => 'purple',
                'statistik_penyakit' => 'orange',
                'feedback_pasien' => 'pink',
                'log_pengguna' => 'slate',
                'rekap_pasien' => 'indigo'
            ];
        @endphp

        @foreach($reportTypes as $key => $label)
            @php $color = $reportColors[$key] ?? 'blue'; @endphp
            <div class="bg-white rounded-xl shadow-md border border-slate-200 p-6 hover:shadow-lg hover:-translate-y-1 transition-all duration-300 cursor-pointer group" onclick="selectReportType('{{ $key }}')">
                <div class="flex items-center space-x-4">
                    <div class="flex items-center justify-center w-12 h-12 bg-{{ $color }}-100 rounded-lg group-hover:bg-{{ $color }}-200 transition-colors">
                        <svg class="w-6 h-6 text-{{ $color }}-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            {!! $reportIcons[$key] ?? $reportIcons['rekap_surat'] !!}
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-sm font-semibold text-slate-800 group-hover:text-{{ $color }}-600 transition-colors">{{ $label }}</h3>
                        <p class="text-xs text-slate-500 mt-1">Klik untuk pilih</p>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Enhanced Filter Form -->
    <form id="reportForm" method="POST" action="{{ route('outlet.reports.export') }}" class="bg-white rounded-2xl shadow-lg border border-slate-200 overflow-hidden">
        @csrf
        <div class="p-8">
            <div class="flex items-center space-x-3 mb-6">
                <div class="p-2 bg-blue-100 rounded-lg">
                    <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3c2.755 0 5.455.232 8.083.678.533.09.917.556.917 1.096v1.044a2.25 2.25 0 01-.659 1.591l-5.432 5.432a2.25 2.25 0 00-.659 1.591v2.927a2.25 2.25 0 01-1.244 2.013L9.75 21v-6.568a2.25 2.25 0 00-.659-1.591L3.659 7.409A2.25 2.25 0 013 5.818V4.774c0-.54.384-1.006.917-1.096A48.32 48.32 0 0112 3z" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-slate-800">Konfigurasi Laporan</h2>
                    <p class="text-slate-600">Atur parameter dan filter untuk laporan yang diinginkan</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Report Type -->
                <div class="space-y-2">
                    <label for="type" class="block text-sm font-semibold text-slate-700">Jenis Laporan</label>
                    <select name="type" id="type" class="w-full px-4 py-3 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                        @foreach ($reportTypes as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Outlet Filter -->
                <div class="space-y-2">
                    <label for="outlet_id" class="block text-sm font-semibold text-slate-700">Outlet</label>
                    <select name="outlet_id" id="outlet_id" class="w-full px-4 py-3 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                        <option value="">Semua Outlet</option>
                        @foreach ($outlets as $outlet)
                            <option value="{{ $outlet->id }}">{{ $outlet->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Doctor Filter -->
                <div class="space-y-2">
                    <label for="doctor_id" class="block text-sm font-semibold text-slate-700">Dokter</label>
                    <select name="doctor_id" id="doctor_id" class="w-full px-4 py-3 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                        <option value="">Semua Dokter</option>
                        @foreach ($doctors as $doctor)
                            <option value="{{ $doctor->id }}">{{ $doctor->user->name ?? 'Dokter Tidak Diketahui' }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Company Filter -->
                <div class="space-y-2">
                    <label for="company_id" class="block text-sm font-semibold text-slate-700">Perusahaan</label>
                    <select name="company_id" id="company_id" class="w-full px-4 py-3 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                        <option value="">Semua Perusahaan</option>
                        @foreach ($companies as $company)
                            <option value="{{ $company->id }}">{{ $company->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Start Date -->
                <div class="space-y-2">
                    <label for="start_date" class="block text-sm font-semibold text-slate-700">Dari Tanggal</label>
                    <input type="date" 
                           name="start_date" 
                           id="start_date" 
                           class="w-full px-4 py-3 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all" 
                           value="{{ now()->startOfMonth()->toDateString() }}">
                </div>

                <!-- End Date -->
                <div class="space-y-2">
                    <label for="end_date" class="block text-sm font-semibold text-slate-700">Sampai Tanggal</label>
                    <input type="date" 
                           name="end_date" 
                           id="end_date" 
                           class="w-full px-4 py-3 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all" 
                           value="{{ now()->endOfMonth()->toDateString() }}">
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="pt-8 border-t border-slate-200 mt-8">
                <div class="flex flex-wrap items-center gap-4">
                    <button type="button" 
                            id="previewBtn" 
                            class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow-md hover:shadow-lg transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Preview Data
                    </button>

                    <div id="loadingSpinner" class="hidden flex items-center text-blue-600">
                        <div class="loading-dots mr-3">
                            <div></div>
                            <div></div>
                            <div></div>
                            <div></div>
                        </div>
                        <span class="text-sm font-medium">Memuat data...</span>
                    </div>

                    <button type="submit" 
                            name="format" 
                            value="pdf" 
                            id="pdfBtn" 
                            class="inline-flex items-center px-6 py-3 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg shadow-md hover:shadow-lg transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 disabled" 
                            disabled>
                        <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                        </svg>
                        Download PDF
                    </button>

                    <button type="submit" 
                            name="format" 
                            value="excel" 
                            id="excelBtn" 
                            class="inline-flex items-center px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-lg shadow-md hover:shadow-lg transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 disabled" 
                            disabled>
                        <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                        </svg>
                        Download Excel
                    </button>

                    <button type="button" 
                            id="resetBtn" 
                            class="inline-flex items-center px-6 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-lg transition-all duration-200">
                        <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                        </svg>
                        Reset
                    </button>
                </div>
            </div>
        </div>
    </form>

    <!-- Enhanced Preview Container -->
    <div id="previewContainer" class="bg-white rounded-2xl shadow-lg border border-slate-200 overflow-hidden hidden">
        <div class="p-6 border-b border-slate-200 bg-slate-50">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="p-2 bg-blue-100 rounded-lg">
                        <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-slate-800">Preview Data</h3>
                        <p class="text-sm text-slate-600">Data yang akan dieksport dalam laporan</p>
                    </div>
                </div>
                <div id="dataCounter" class="text-right">
                    <p class="text-sm text-slate-500">Total Records</p>
                    <p class="text-2xl font-bold text-blue-600" id="recordCount">0</p>
                </div>
            </div>
        </div>
        
        <div class="p-6">
            <div class="overflow-x-auto">
                <table id="previewTable" class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr id="previewHeader"></tr>
                    </thead>
                    <tbody id="previewRows" class="bg-white divide-y divide-slate-200"></tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Error Messages -->
    @if (session('error'))
        <div class="bg-red-50 border border-red-200 text-red-800 px-6 py-4 rounded-xl shadow-sm">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-red-400 mr-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                </svg>
                <div>
                    <p class="font-semibold">Error</p>
                    <p class="text-sm">{{ session('error') }}</p>
                </div>
            </div>
        </div>
    @endif

    @if (session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-6 py-4 rounded-xl shadow-sm">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-emerald-400 mr-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                    <p class="font-semibold">Success</p>
                    <p class="text-sm">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    function selectReportType(type) {
        document.getElementById('type').value = type;
        
        // Add visual feedback
        document.querySelectorAll('.group').forEach(card => {
            card.classList.remove('ring-2', 'ring-blue-500');
        });
        event.currentTarget.classList.add('ring-2', 'ring-blue-500');
        
        // Scroll to form
        document.getElementById('reportForm').scrollIntoView({ 
            behavior: 'smooth', 
            block: 'start' 
        });
    }

    // Preview functionality
    document.getElementById('previewBtn').addEventListener('click', function () {
        const formData = new FormData();
        formData.append('type', document.getElementById('type').value);
        formData.append('start_date', document.getElementById('start_date').value);
        formData.append('end_date', document.getElementById('end_date').value);
        formData.append('outlet_id', document.getElementById('outlet_id').value);
        formData.append('doctor_id', document.getElementById('doctor_id').value);
        formData.append('company_id', document.getElementById('company_id').value);

        // Show loading state
        document.getElementById('loadingSpinner').classList.remove('hidden');
        document.getElementById('previewContainer').classList.add('hidden');
        this.disabled = true;

        // Build query string
        const params = new URLSearchParams();
        for (let [key, value] of formData.entries()) {
            if (value) params.append(key, value);
        }

        fetch(`/outlet/reports/preview?${params.toString()}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                displayPreviewData(data);
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Gagal memuat data preview. Silakan coba lagi.', 'error');
            })
            .finally(() => {
                document.getElementById('loadingSpinner').classList.add('hidden');
                this.disabled = false;
            });
    });

    function displayPreviewData(data) {
        const header = document.getElementById('previewHeader');
        const tbody = document.getElementById('previewRows');
        const recordCount = document.getElementById('recordCount');

        // Clear previous data
        header.innerHTML = '';
        tbody.innerHTML = '';

        if (data && data.length > 0) {
            // Update record count
            recordCount.textContent = data.length.toLocaleString();

            // Get the correct headers based on report type
            const reportType = document.getElementById('type').value;
            const headers = getReportHeaders(reportType, data);
            
            headers.forEach(headerText => {
                const th = document.createElement('th');
                th.className = 'px-6 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider';
                th.textContent = headerText;
                header.appendChild(th);
            });

            // Create table rows
            data.slice(0, 100).forEach((row, index) => { // Limit to first 100 rows for performance
                const tr = document.createElement('tr');
                tr.className = index % 2 === 0 ? 'bg-white' : 'bg-slate-50';
                
                // If row is array, use index; if object, use keys
                if (Array.isArray(row)) {
                    row.forEach(cell => {
                        const td = document.createElement('td');
                        td.className = 'px-6 py-4 whitespace-nowrap text-sm text-slate-900';
                        td.textContent = cell ?? '-';
                        tr.appendChild(td);
                    });
                } else {
                    Object.values(row).forEach(cell => {
                        const td = document.createElement('td');
                        td.className = 'px-6 py-4 whitespace-nowrap text-sm text-slate-900';
                        td.textContent = cell ?? '-';
                        tr.appendChild(td);
                    });
                }
                
                tbody.appendChild(tr);
            });

            if (data.length > 100) {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td colspan="${headers.length}" class="px-6 py-4 text-center text-sm text-slate-500 bg-slate-50">
                        <div class="flex items-center justify-center space-x-2">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                            </svg>
                            <span>Menampilkan 100 dari ${data.length.toLocaleString()} total records. Download untuk melihat semua data.</span>
                        </div>
                    </td>
                `;
                tbody.appendChild(tr);
            }

            // Enable download buttons
            enableDownloadButtons();
            
            showNotification(`Data berhasil dimuat: ${data.length.toLocaleString()} records ditemukan`, 'success');
        } else {
            recordCount.textContent = '0';
            header.innerHTML = '<th class="px-6 py-4 text-center text-sm text-slate-500" colspan="100%">Tidak ada data ditemukan</th>';
            tbody.innerHTML = `
                <tr>
                    <td class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center space-y-3">
                            <svg class="w-12 h-12 text-slate-300" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                            </svg>
                            <div class="text-slate-500">
                                <p class="font-semibold">Tidak ada data</p>
                                <p class="text-sm">Coba ubah filter atau rentang tanggal</p>
                            </div>
                        </div>
                    </td>
                </tr>
            `;
            disableDownloadButtons();
            showNotification('Tidak ada data ditemukan dengan filter yang dipilih', 'warning');
        }

        // Show preview container
        document.getElementById('previewContainer').classList.remove('hidden');
        document.getElementById('previewContainer').scrollIntoView({ 
            behavior: 'smooth', 
            block: 'start' 
        });
    }

    // Function to get proper headers based on report type
    function getReportHeaders(reportType, data) {
        const headerMappings = {
            'rekap_surat': [
                'Tanggal Input', 'Kode Unik', 'No Surat', 'Tipe', 'Durasi (Hari)', 
                'Tanggal Mulai', 'Tanggal Selesai', 'Tanggal SKB', 'Jam SKB', 
                'Verifikasi', 'Print', 'Tanda Tangan', 'Value TTD', 'Pasien', 
                'ID Pegawai', 'Dokter', 'Klinik', 'Perusahaan', 'Diagnosa', 
                'Notif WA', 'Notif Email', 'Status Edit'
            ],
            'aktivitas_dokter': [
                'Dokter', 'Total Surat', 'Surat Sakit (MC)', 'Surat Sehat (SKB)'
            ],
            'aktivitas_perusahaan': [
                'Perusahaan', 'Total Surat', 'Surat Sakit (MC)', 'Surat Sehat (SKB)'
            ],
            'statistik_penyakit': [
                'Diagnosa', 'Jumlah Kasus'
            ],
            'feedback_pasien': [
                'Pasien', 'Klinik', 'Rating', 'Komentar', 'Tanggal'
            ],
            'log_pengguna': [
                'User', 'Role', 'Aksi', 'Waktu'
            ],
            'rekap_pasien': [
                'Nama Pasien', 'Perusahaan', 'Total Surat'
            ]
        };

        // Return predefined headers if available, otherwise generate from data
        if (headerMappings[reportType]) {
            return headerMappings[reportType];
        }

        // Fallback: generate headers from data structure
        if (data && data.length > 0) {
            if (Array.isArray(data[0])) {
                // If data is array of arrays, create generic headers
                return data[0].map((_, index) => `Column ${index + 1}`);
            } else {
                // If data is array of objects, use object keys
                return Object.keys(data[0]).map(key => key.replace(/_/g, ' ').toUpperCase());
            }
        }

        return [];
    }

    function enableDownloadButtons() {
        const pdfBtn = document.getElementById('pdfBtn');
        const excelBtn = document.getElementById('excelBtn');
        
        pdfBtn.classList.remove('disabled');
        excelBtn.classList.remove('disabled');
        pdfBtn.disabled = false;
        excelBtn.disabled = false;
    }

    function disableDownloadButtons() {
        const pdfBtn = document.getElementById('pdfBtn');
        const excelBtn = document.getElementById('excelBtn');
        
        pdfBtn.classList.add('disabled');
        excelBtn.classList.add('disabled');
        pdfBtn.disabled = true;
        excelBtn.disabled = true;
    }

    function showNotification(message, type = 'info') {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 z-50 max-w-sm w-full transform transition-all duration-300 translate-x-full`;
        
        const bgColor = type === 'success' ? 'bg-emerald-50 border-emerald-200 text-emerald-800' :
                       type === 'error' ? 'bg-red-50 border-red-200 text-red-800' :
                       type === 'warning' ? 'bg-amber-50 border-amber-200 text-amber-800' :
                       'bg-blue-50 border-blue-200 text-blue-800';
        
        const iconPath = type === 'success' ? 'M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z' :
                        type === 'error' ? 'M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z' :
                        type === 'warning' ? 'M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z' :
                        'M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z';
        
        notification.innerHTML = `
            <div class="${bgColor} border rounded-lg shadow-lg p-4">
                <div class="flex items-start">
                    <svg class="w-5 h-5 mt-0.5 mr-3 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="${iconPath}" />
                    </svg>
                    <div class="flex-1">
                        <p class="text-sm font-medium">${message}</p>
                    </div>
                    <button onclick="this.parentElement.parentElement.parentElement.remove()" class="ml-3 text-slate-400 hover:text-slate-600">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        // Animate in
        setTimeout(() => {
            notification.classList.remove('translate-x-full');
        }, 100);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            notification.classList.add('translate-x-full');
            setTimeout(() => {
                if (notification.parentElement) {
                    notification.remove();
                }
            }, 300);
        }, 5000);
    }

    // Reset form functionality
    document.getElementById('resetBtn').addEventListener('click', function() {
        // Reset form fields
        document.getElementById('reportForm').reset();
        
        // Reset to default values
        document.getElementById('start_date').value = '{{ now()->startOfMonth()->toDateString() }}';
        document.getElementById('end_date').value = '{{ now()->endOfMonth()->toDateString() }}';
        
        // Hide preview and disable download buttons
        document.getElementById('previewContainer').classList.add('hidden');
        disableDownloadButtons();
        
        // Remove selection styling
        document.querySelectorAll('.group').forEach(card => {
            card.classList.remove('ring-2', 'ring-blue-500');
        });
        
        showNotification('Form berhasil direset', 'info');
    });

    // Form submission handling
    document.getElementById('reportForm').addEventListener('submit', function(e) {
        const format = e.submitter.value; // Get which button was clicked
        
        // Add loading state to the clicked button
        e.submitter.disabled = true;
        const originalText = e.submitter.innerHTML;
        
        e.submitter.innerHTML = `
            <div class="loading-dots mr-2">
                <div></div>
                <div></div>
                <div></div>
                <div></div>
            </div>
            Processing...
        `;
        
        showNotification(`Menggenerate laporan ${format.toUpperCase()}...`, 'info');
        
        // Re-enable button after 5 seconds (in case of download)
        setTimeout(() => {
            e.submitter.disabled = false;
            e.submitter.innerHTML = originalText;
        }, 5000);
    });

    // Date validation
    document.getElementById('start_date').addEventListener('change', function() {
        const startDate = new Date(this.value);
        const endDate = new Date(document.getElementById('end_date').value);
        
        if (startDate > endDate) {
            document.getElementById('end_date').value = this.value;
            showNotification('Tanggal akhir disesuaikan dengan tanggal mulai', 'warning');
        }
    });

    document.getElementById('end_date').addEventListener('change', function() {
        const startDate = new Date(document.getElementById('start_date').value);
        const endDate = new Date(this.value);
        
        if (endDate < startDate) {
            document.getElementById('start_date').value = this.value;
            showNotification('Tanggal mulai disesuaikan dengan tanggal akhir', 'warning');
        }
    });

    // Initialize form state
    disableDownloadButtons();
</script>
@endpush