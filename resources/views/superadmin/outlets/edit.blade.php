@extends('layouts.app', ['header' => 'Edit Data Outlet'])

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
     integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
     crossorigin=""/>
<style>
    #map { height: 400px; z-index: 10; }
</style>
@endpush

@section('content')
<div class="max-w-5xl mx-auto space-y-8">

    <!-- Kartu Form Utama -->
    <div class="bg-white p-6 sm:p-8 rounded-2xl shadow-lg">
        <form action="{{ route('outlets.update', $outlet) }}" method="POST" id="editOutletForm">
            @csrf
            @method('PUT')

            <!-- Header Form -->
            <div class="mb-8 pb-6 border-b border-slate-200">
                <h1 class="text-2xl font-bold text-slate-900">Edit Outlet: {{ $outlet->name }}</h1>
                <p class="mt-1 text-sm text-slate-500">Perbarui detail, lokasi, dan penanggung jawab untuk outlet ini.</p>
            </div>

            <!-- Wrapper Form dengan Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-x-10 gap-y-10">
                <!-- Kolom Kiri: Informasi Utama & Kontak -->
                <div class="space-y-8">
                    <div>
                        <h2 class="text-lg font-semibold text-slate-800">1. Informasi Utama</h2>
                        <div class="space-y-6 mt-4">
                            <div>
                                <label for="name" class="block text-sm font-medium leading-6 text-slate-700 mb-1.5">Nama Outlet</label>
                                <input type="text" name="name" id="name" value="{{ old('name', $outlet->name) }}" required class="block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('name') border-red-500 @enderror">
                                @error('name')<p class="text-sm text-red-600 mt-2">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="admin_id" class="block text-sm font-medium leading-6 text-slate-700 mb-1.5">Admin Penanggung Jawab</label>
                                <select id="admin_id" name="admin_id" class="block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('admin_id') border-red-500 @enderror">
                                    <option value="">-- Tidak Ada --</option>
                                    @foreach($admins as $admin)
                                        <option value="{{ $admin->id }}" @selected(old('admin_id', $outlet->admin_id) == $admin->id)>
                                            {{ $admin->user->name }} ({{ $admin->region_name }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('admin_id')<p class="text-sm text-red-600 mt-2">{{ $message }}</p>@enderror
                            </div>
                        </div>
                    </div>
                    <div>
                        <h2 class="text-lg font-semibold text-slate-800">2. Detail Kontak</h2>
                         <div class="space-y-6 mt-4">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                <div>
                                    <label for="email" class="block text-sm font-medium leading-6 text-slate-700 mb-1.5">Email <span class="text-slate-400">(Login)</span></label>
                                    <input type="email" name="email" id="email" value="{{ old('email', $outlet->email) }}" placeholder="Email untuk login outlet" class="block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('email') border-red-500 @enderror">
                                    @error('email')<p class="text-sm text-red-600 mt-2">{{ $message }}</p>@enderror
                                </div>
                                <div>
                                    <label for="phone" class="block text-sm font-medium leading-6 text-slate-700 mb-1.5">Telepon <span class="text-slate-400">(Opsional)</span></label>
                                    <input type="text" name="phone" id="phone" value="{{ old('phone', $outlet->phone) }}" placeholder="Nomor telepon outlet" class="block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('phone') border-red-500 @enderror">
                                    @error('phone')<p class="text-sm text-red-600 mt-2">{{ $message }}</p>@enderror
                                </div>
                            </div>
                         </div>
                    </div>
                </div>

                <!-- Kolom Kanan: Alamat -->
                <div class="space-y-8">
                    <div>
                        <h2 class="text-lg font-semibold text-slate-800">3. Alamat Lengkap</h2>
                        <div class="space-y-6 mt-4">
                            <div>
                                <label for="address" class="block text-sm font-medium leading-6 text-slate-700 mb-1.5">Alamat Lengkap</label>
                                <textarea name="address" id="address" rows="3" placeholder="Jalan, nomor, RT/RW..." class="block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('address') border-red-500 @enderror">{{ old('address', $outlet->address) }}</textarea>
                                @error('address')<p class="text-sm text-red-600 mt-2">{{ $message }}</p>@enderror
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                <div>
                                    <label for="city" class="block text-sm font-medium leading-6 text-slate-700 mb-1.5">Kota / Kabupaten</label>
                                    <input type="text" name="city" id="city" value="{{ old('city', $outlet->city) }}" class="block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('city') border-red-500 @enderror">
                                    @error('city')<p class="text-sm text-red-600 mt-2">{{ $message }}</p>@enderror
                                </div>
                                <div>
                                    <label for="province" class="block text-sm font-medium leading-6 text-slate-700 mb-1.5">Provinsi</label>
                                    <input type="text" name="province" id="province" value="{{ old('province', $outlet->province) }}" class="block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('province') border-red-500 @enderror">
                                    @error('province')<p class="text-sm text-red-600 mt-2">{{ $message }}</p>@enderror
                                </div>
                            </div>
                             <div>
                                <label for="postal_code" class="block text-sm font-medium leading-6 text-slate-700 mb-1.5">Kode Pos <span class="text-slate-400">(Opsional)</span></label>
                                <input type="text" name="postal_code" id="postal_code" value="{{ old('postal_code', $outlet->postal_code) }}" class="block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('postal_code') border-red-500 @enderror">
                                @error('postal_code')<p class="text-sm text-red-600 mt-2">{{ $message }}</p>@enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bagian Peta & Koordinat -->
            <div class="mt-10 pt-6 border-t border-slate-200">
                <h2 class="text-lg font-semibold text-slate-800">4. Tentukan Lokasi di Peta</h2>
                <p class="text-sm text-slate-500 mt-1">Klik dan geser pin untuk menentukan koordinat yang akurat.</p>
                <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="md:col-span-1 space-y-4">
                        <div>
                            <label for="latitude" class="block text-sm font-medium text-slate-700">Latitude</label>
                            <input type="text" id="latitude" name="latitude" value="{{ old('latitude', $outlet->latitude) }}" required readonly class="mt-1 block w-full rounded-md border-slate-300 bg-slate-100 shadow-sm sm:text-sm">
                            @error('latitude')<p class="text-sm text-red-600 mt-2">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="longitude" class="block text-sm font-medium text-slate-700">Longitude</label>
                            <input type="text" id="longitude" name="longitude" value="{{ old('longitude', $outlet->longitude) }}" required readonly class="mt-1 block w-full rounded-md border-slate-300 bg-slate-100 shadow-sm sm:text-sm">
                            @error('longitude')<p class="text-sm text-red-600 mt-2">{{ $message }}</p>@enderror
                        </div>
                    </div>
                    <div class="md:col-span-2 rounded-lg overflow-hidden shadow-lg border border-slate-300">
                        <div id="map"></div>
                    </div>
                </div>
            </div>

            <!-- Tombol Aksi Form Utama -->
            <div class="mt-10 pt-6 border-t border-slate-200 flex justify-between items-center">
                <a href="{{ route('superadmin.outlets.index') }}" class="text-sm font-semibold text-slate-600 hover:text-slate-900 transition hover:underline">
                    &larr; Batal
                </a>
                <button type="submit" class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-blue-600 text-white font-bold rounded-lg shadow-md hover:bg-blue-700 transition-all duration-200">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                    <span>Simpan Perubahan</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Zona Berbahaya -->
    <div class="mt-12 pt-8 border-t border-dashed border-slate-300">
        <div class="bg-red-50 border border-red-300 rounded-2xl p-6">
            <h3 class="text-lg font-semibold text-red-900">Zona Berbahaya</h3>
            <div class="mt-4 space-y-4 md:space-y-0 md:flex md:items-center md:justify-between">
                <div>
                    <p class="text-sm text-red-800 font-medium">Reset Password Pengguna</p>
                    <p class="text-sm text-red-700">Mengatur ulang password akun login outlet ini ke default.</p>
                </div>
                <form action="{{ route('outlets.reset-password', $outlet) }}" method="POST" onsubmit="return confirm('Anda yakin ingin mereset password untuk outlet ini?');" class="flex-shrink-0">
                    @csrf
                    <button type="submit" class="px-4 py-2 text-sm bg-yellow-500 text-white font-semibold rounded-lg hover:bg-yellow-600 transition">Reset Password</button>
                </form>
            </div>
            <div class="mt-4 pt-4 border-t border-red-200 md:flex md:items-center md:justify-between">
                 <div>
                    <p class="text-sm text-red-800 font-medium">Hapus Outlet Permanen</p>
                    <p class="text-sm text-red-700">Tindakan ini tidak dapat diurungkan.</p>
                </div>
                <form action="{{ route('outlets.destroy', $outlet) }}" method="POST" onsubmit="return confirm('PERINGATAN! Anda akan menghapus outlet ini secara permanen. Lanjutkan?');" class="flex-shrink-0 mt-2 md:mt-0">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 text-sm bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700 transition">Hapus Permanen</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
     integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
     crossorigin=""></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const latInput = document.getElementById('latitude');
    const lngInput = document.getElementById('longitude');
    
    // Gunakan data dari outlet jika ada, jika tidak, gunakan data lama (old), jika tidak ada juga, gunakan default Jakarta
    const initialLat = parseFloat("{{ old('latitude', $outlet->latitude) ?? -6.2088 }}");
    const initialLng = parseFloat("{{ old('longitude', $outlet->longitude) ?? 106.8456 }}");

    const map = L.map('map').setView([initialLat, initialLng], 14);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);
    
    const marker = L.marker([initialLat, initialLng], {
        draggable: true,
    }).addTo(map);

    marker.on('dragend', function(event) {
        const position = marker.getLatLng();
        latInput.value = position.lat.toFixed(6);
        lngInput.value = position.lng.toFixed(6);
    });
});
</script>
@endpush