@extends('layouts.app', ['header' => 'Tambah Perusahaan Baru'])

@section('content')
<div class="max-w-4xl mx-auto">

    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-800">Daftarkan Perusahaan Klien Baru</h1>
        <p class="mt-1 text-sm text-slate-500">Isi detail di bawah untuk menambahkan perusahaan baru dan mengaitkannya dengan paket langganan.</p>
    </div>

    <form action="{{ route('companies.store') }}" method="POST">
        @csrf
        <!-- Wrapper Form dengan Grid 2 Kolom -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">

            <!-- Kolom Kiri: Informasi Perusahaan -->
            <div class="space-y-6 bg-white border border-slate-200 rounded-xl p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-slate-800 border-b border-slate-200 pb-3">Informasi Perusahaan</h2>
                
                <!-- Nama Perusahaan -->
                <div>
                    <label for="name" class="block text-sm font-medium leading-6 text-slate-700 mb-1.5">Nama Perusahaan</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required placeholder="Contoh: PT Sejahtera Abadi"
                           class="block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('name') border-red-500 @enderror">
                    @error('name')
                        <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Jenis Industri & Nomor Registrasi -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                     <div>
                        <label for="industry_type" class="block text-sm font-medium leading-6 text-slate-700 mb-1.5">Jenis Industri</label>
                        <input type="text" name="industry_type" id="industry_type" value="{{ old('industry_type') }}" placeholder="Manufaktur, IT, dll."
                               class="block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    </div>
                     <div>
                        <label for="registration_number" class="block text-sm font-medium leading-6 text-slate-700 mb-1.5">No. Registrasi (NIB)</label>
                        <input type="text" name="registration_number" id="registration_number" value="{{ old('registration_number') }}" placeholder="Opsional"
                               class="block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    </div>
                </div>

                <!-- Email Perusahaan -->
                <div>
                    <label for="email" class="block text-sm font-medium leading-6 text-slate-700 mb-1.5">Email Perusahaan</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" placeholder="kontak@perusahaan.com"
                           class="block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('email') border-red-500 @enderror">
                     @error('email')
                        <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Nomor Telepon Perusahaan -->
                 <div>
                    <label for="phone" class="block text-sm font-medium leading-6 text-slate-700 mb-1.5">Telepon Perusahaan</label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone') }}" placeholder="Nomor telepon kantor"
                           class="block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('phone') border-red-500 @enderror">
                     @error('phone')
                        <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Kolom Kanan: Alamat & Langganan -->
            <div class="space-y-6 bg-white border border-slate-200 rounded-xl p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-slate-800 border-b border-slate-200 pb-3">Alamat & Langganan</h2>
                
                <!-- Alamat Lengkap -->
                <div>
                    <label for="address" class="block text-sm font-medium leading-6 text-slate-700 mb-1.5">Alamat Lengkap</label>
                    <textarea name="address" id="address" rows="3" placeholder="Jalan, nomor, RT/RW, kelurahan, kecamatan..."
                              class="block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('address') border-red-500 @enderror">{{ old('address') }}</textarea>
                </div>
                
                <!-- Kota, Provinsi, Kode Pos -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="city" class="block text-sm font-medium leading-6 text-slate-700 mb-1.5">Kota / Kabupaten</label>
                        <input type="text" name="city" id="city" value="{{ old('city') }}" placeholder="Contoh: Jakarta Selatan"
                               class="block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    </div>
                     <div>
                        <label for="province" class="block text-sm font-medium leading-6 text-slate-700 mb-1.5">Provinsi</label>
                        <input type="text" name="province" id="province" value="{{ old('province') }}" placeholder="Contoh: DKI Jakarta"
                               class="block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    </div>
                </div>
                 <div>
                    <label for="postal_code" class="block text-sm font-medium leading-6 text-slate-700 mb-1.5">Kode Pos (Opsional)</label>
                    <input type="text" name="postal_code" id="postal_code" value="{{ old('postal_code') }}"
                           class="block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                </div>
                
                <!-- Paket Langganan -->
                <div>
                    <label for="package_id" class="block text-sm font-medium leading-6 text-slate-700 mb-1.5">Paket Langganan</label>
                    <select id="package_id" name="package_id" class="block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('package_id') border-red-500 @enderror">
                        <option value="">-- Pilih Paket Langganan --</option>
                        {{-- Asumsi variabel $packages dikirim dari controller --}}
                        @foreach($packages ?? [] as $package) 
                            <option value="{{ $package->id }}" @selected(old('package_id') == $package->id)>
                                {{ $package->name }} ({{ $package->duration_in_days }} hari) - Rp {{ number_format($package->price) }}
                            </option>
                        @endforeach
                    </select>
                     @error('package_id')
                        <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                    @enderror
                </div>
                 <!-- Status Aktif -->
                <div class="relative flex items-start">
                    <div class="flex h-6 items-center">
                        <input id="is_active" name="is_active" type="checkbox" value="1" checked
                               class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-600">
                    </div>
                    <div class="ml-3 text-sm leading-6">
                        <label for="is_active" class="font-medium text-slate-700">Aktifkan Perusahaan</label>
                        <p class="text-slate-500">Langsung aktifkan perusahaan setelah disimpan.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tombol Aksi di Bawah Form -->
        <div class="mt-8 flex justify-between items-center">
            <a href="{{ route('companies.index') }}" class="text-sm text-slate-600 hover:text-slate-900 transition hover:underline">
                &larr; Batal
            </a>
            <button type="submit"
                    class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-blue-600 text-white font-bold rounded-lg shadow-md hover:bg-blue-700 transition-all duration-200">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                <span>Simpan Perusahaan Baru</span>
            </button>
        </div>
    </form>
</div>
@endsection