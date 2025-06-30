@extends('layouts.public')

@section('content')
<div class="relative overflow-hidden rounded-xl shadow-xl">

    {{-- 🌊 Wave Background --}}
    <div class="absolute top-0 left-0 w-full h-32 bg-gradient-to-r from-blue-100 via-white to-green-100 animate-wave z-0">
        <svg viewBox="0 0 500 100" preserveAspectRatio="none" class="absolute bottom-0 w-full h-full opacity-30">
            <path d="M0,30 C150,90 350,0 500,30 L500,100 L0,100 Z" fill="#3B82F6" />
        </svg>
    </div>

    {{-- 🌈 Main Container --}}
    <div class="relative z-10 bg-white bg-opacity-90 backdrop-blur-md p-6 rounded-xl space-y-6 animate-fade-in">
       <div class="text-center mb-6">
        {{-- ✅ Ikon animasi masuk --}}
        <div class="inline-flex items-center justify-center w-16 h-16 bg-green-100 rounded-full mb-3 animate-bounce-in">
            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
            </svg>
        </div>

        {{-- Judul utama --}}
        <h2 class="text-2xl font-bold text-green-700 mb-1">Surat Ini Telah Terverifikasi</h2>

        {{-- Penjelasan detail --}}
        <p class="text-sm text-gray-600 leading-relaxed">
            Dokumen ini adalah <strong>surat resmi</strong> yang telah diverifikasi oleh sistem kami.<br>
            Informasi di bawah ini bersumber langsung dari database dan dapat dipercaya sepenuhnya.
        </p>
    </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
            <div>
                <p class="font-medium text-gray-700">Nama Pasien<br><span class="text-gray-900 text-base">{{ $result->patient->full_name }}</span></p>
            </div>
            <div>
                <p class="font-medium text-gray-700">Dokter Pemeriksa<br><span class="text-gray-900 text-base">{{ $result->doctor->user->name }}</span></p>
            </div>
        </div>

        @if($result->type === 'mc')
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 bg-blue-50 p-4 rounded-xl border border-blue-200">
                <div>
                    <p class="text-gray-600 font-semibold uppercase text-xs">Durasi / Duration</p>
                    <p class="text-base font-bold text-gray-800">{{ $result->duration }} Hari / {{ $result->duration }} Days</p>
                </div>
                <div>
                    <p class="text-gray-600 font-semibold uppercase text-xs">Tanggal Mulai / Start From</p>
                    <p class="text-base font-bold text-gray-800">{{ \Carbon\Carbon::parse($result->start_date)->translatedFormat('d F Y') }} / {{ \Carbon\Carbon::parse($result->start_date)->format('F jS, Y') }}</p>
                </div>
                <div>
                    <p class="text-gray-600 font-semibold uppercase text-xs">Tanggal Selesai / Until Date</p>
                    <p class="text-base font-bold text-gray-800">{{ \Carbon\Carbon::parse($result->end_date)->translatedFormat('d F Y') }} / {{ \Carbon\Carbon::parse($result->end_date)->format('F jS, Y') }}</p>
                </div>
                <div>
                    <p class="text-gray-600 font-semibold uppercase text-xs">Klinik / Healthcare Facility</p>
                    <p class="text-base font-bold text-gray-800">{{ $result->outlet->name }}</p>
                </div>
            </div>
        @elseif($result->type === 'skb')
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 bg-green-50 p-4 rounded-xl border border-green-200">
                <div>
                    <p class="text-gray-600 font-semibold uppercase text-xs">Hari / Day</p>
                    <p class="text-base font-bold text-gray-800">{{ \Carbon\Carbon::parse($result->date)->translatedFormat('l') }} / {{ \Carbon\Carbon::parse($result->date)->format('l') }}</p>
                </div>
                <div>
                    <p class="text-gray-600 font-semibold uppercase text-xs">Tanggal / Date</p>
                    <p class="text-base font-bold text-gray-800">{{ \Carbon\Carbon::parse($result->date)->translatedFormat('d F Y') }} / {{ \Carbon\Carbon::parse($result->date)->format('F jS, Y') }}</p>
                </div>
                <div>
                    <p class="text-gray-600 font-semibold uppercase text-xs">Waktu / Time</p>
                    <p class="text-base font-bold text-gray-800">{{ $result->time }} WIB</p>
                </div>
                <div>
                    <p class="text-gray-600 font-semibold uppercase text-xs">Klinik / Clinic</p>
                    <p class="text-base font-bold text-gray-800">{{ $result->outlet->name }}</p>
                </div>
            </div>
        @endif

        <div class="text-center mt-6">
            <p class="text-xs text-gray-400">Kode Verifikasi: {{ $result->unique_code }}</p>
            <p class="text-xs text-gray-400">Diverifikasi pada: {{ now()->translatedFormat('d F Y, H:i') }}</p>
        </div>
    </div>
</div>

{{-- Custom Animations --}}
<style>
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(15px); }
        to { opacity: 1; transform: translateY(0); }
    }
    @keyframes waveMove {
        0% { transform: translateX(0); }
        100% { transform: translateX(-50%); }
    }
    .animate-fade-in {
        animation: fadeIn 0.6s ease-out both;
    }
    .animate-wave {
        animation: waveMove 6s linear infinite alternate;
    }
</style>
@endsection
