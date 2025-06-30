@extends('layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-100">
    <div class="bg-white p-8 rounded shadow max-w-md w-full text-center">
        <h2 class="text-xl font-bold mb-4">📍 Konfirmasi Lokasi Anda</h2>
        <p class="text-sm text-gray-600 mb-6">Untuk membuat surat, kami perlu mengakses lokasi Anda secara akurat.</p>

        <button id="confirmLocationBtn"
            class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            Izinkan Lokasi Sekarang
        </button>

        <form id="redirectForm" method="GET" action="{{ route('outlet.results.create.mc') }}">
            <input type="hidden" name="lat" id="location_lat">
            <input type="hidden" name="long" id="location_long">
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('confirmLocationBtn')?.addEventListener('click', function () {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            function (position) {
                document.getElementById('location_lat').value = position.coords.latitude;
                document.getElementById('location_long').value = position.coords.longitude;
                document.getElementById('redirectForm').submit();
            },
            function (error) {
                alert("❌ Lokasi gagal didapatkan. Mohon izinkan akses lokasi di browser Anda.");
            },
            { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
        );
    } else {
        alert("❌ Browser Anda tidak mendukung geolokasi.");
    }
});
</script>
@endpush
