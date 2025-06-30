@extends('layouts.public')

@section('content')
<div class="relative z-10 bg-white/80 backdrop-blur-md p-6 rounded-xl shadow-xl border border-red-100 animate-fade-in">

    {{-- ❌ Ikon dengan animasi shake --}}
    <div class="flex justify-center mb-4">
        <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center animate-shake">
            <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </div>
    </div>

    {{-- Judul & Penjelasan --}}
    <h2 class="text-xl sm:text-2xl font-bold text-red-700 mb-2 text-center">
        Surat Tidak Ditemukan atau Tidak Valid
    </h2>
    <p class="text-sm text-gray-600 text-center leading-relaxed">
        Kami tidak dapat memverifikasi dokumen ini. QR Code yang Anda scan mungkin tidak berasal dari surat resmi, sudah kedaluwarsa, atau tidak valid.
        <br class="hidden sm:block">
        Pastikan Anda memindai QR Code langsung dari dokumen resmi yang dikeluarkan oleh klinik.
    </p>

</div>

{{-- Animasi Custom --}}
<style>
@keyframes shake {
    0%, 100% { transform: translateX(0); }
    25% { transform: translateX(-5px); }
    50% { transform: translateX(5px); }
    75% { transform: translateX(-4px); }
}
.animate-shake {
    animation: shake 0.5s ease-in-out 1;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-fade-in {
    animation: fadeIn 0.5s ease-out;
}
</style>
@endsection
