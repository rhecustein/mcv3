@extends('layouts.app', ['header' => 'Tambah Outlet Baru'])

@section('content')
<div class="max-w-4xl mx-auto">

    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-800">Daftarkan Outlet Baru</h1>
        <p class="mt-1 text-sm text-slate-500">Isi detail di bawah ini untuk menambahkan klinik atau fasilitas kesehatan baru ke dalam jaringan.</p>
    </div>

    <form action="{{ route('outlets.store') }}" method="POST">
        @csrf
        <!-- Wrapper Form dengan Grid 2 Kolom -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">

            <!-- Kolom Kiri: Informasi Utama -->
            <div class="space-y-6 bg-white border border-slate-200 rounded-xl p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-slate-800 border-b border-slate-200 pb-3">Informasi Utama</h2>
                <p class="text-sm text-slate-500 -mt-4">Detail inti dan penanggung jawab outlet.</p>

                <!-- Nama Outlet -->
                <div>
                    <label for="name" class="block text-sm font-medium leading-6 text-slate-700 mb-1.5">Nama Outlet</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required placeholder="Contoh: Klinik Sehat Sentosa"
                           class="block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('name') border-red-500 @enderror">
                    @error('name')
                        <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Admin Penanggung Jawab -->
                <div>
                    <label for="admin_id" class="block text-sm font-medium leading-6 text-slate-700 mb-1.5">Admin Penanggung Jawab</label>
                    <select id="admin_id" name="admin_id" class="block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('admin_id') border-red-500 @enderror">
                        <option value="">-- Pilih Admin --</option>
                        @foreach($admins as $admin)
                            <option value="{{ $admin->id }}" @selected(old('admin_id') == $admin->id)>
                                {{ $admin->user->name }} ({{ $admin->region_name }})
                            </option>
                        @endforeach
                    </select>
                     @error('admin_id')
                        <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email Outlet -->
                <div>
                    <label for="email" class="block text-sm font-medium leading-6 text-slate-700 mb-1.5">Email Outlet (Opsional)</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" placeholder="Email resmi outlet"
                           class="block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('email') border-red-500 @enderror">
                     @error('email')
                        <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Nomor Telepon Outlet -->
                 <div>
                    <label for="phone" class="block text-sm font-medium leading-6 text-slate-700 mb-1.5">Telepon Outlet (Opsional)</label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone') }}" placeholder="Nomor telepon yang bisa dihubungi"
                           class="block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('phone') border-red-500 @enderror">
                     @error('phone')
                        <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Kolom Kanan: Lokasi -->
            <div class="space-y-6 bg-white border border-slate-200 rounded-xl p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-slate-800 border-b border-slate-200 pb-3">Informasi Lokasi</h2>
                <p class="text-sm text-slate-500 -mt-4">Detail alamat lengkap untuk outlet.</p>
                
                <!-- Alamat Lengkap -->
                <div>
                    <label for="address" class="block text-sm font-medium leading-6 text-slate-700 mb-1.5">Alamat Lengkap</label>
                    <textarea name="address" id="address" rows="4" placeholder="Jalan, nomor, RT/RW, kelurahan, kecamatan..."
                              class="block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('address') border-red-500 @enderror">{{ old('address') }}</textarea>
                    @error('address')
                        <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Kota/Kabupaten -->
                <div>
                    <label for="city" class="block text-sm font-medium leading-6 text-slate-700 mb-1.5">Kota / Kabupaten</label>
                    <input type="text" name="city" id="city" value="{{ old('city') }}" placeholder="Contoh: Kota Bandung"
                           class="block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('city') border-red-500 @enderror">
                    @error('city')
                        <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Provinsi -->
                <div>
                    <label for="province" class="block text-sm font-medium leading-6 text-slate-700 mb-1.5">Provinsi</label>
                    <input type="text" name="province" id="province" value="{{ old('province') }}" placeholder="Contoh: Jawa Barat"
                           class="block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('province') border-red-500 @enderror">
                    @error('province')
                        <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                    @enderror
                </div>
            </div>

        </div>

        <!-- Tombol Aksi di Bawah Form -->
        <div class="mt-8 flex justify-between items-center">
            <a href="{{ route('outlets.index') }}" class="text-sm text-slate-600 hover:text-slate-900 transition hover:underline">
                &larr; Batal
            </a>
            <button type="submit"
                    class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-blue-600 text-white font-bold rounded-lg shadow-md hover:bg-blue-700 transition-all duration-200">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                <span>Simpan Outlet Baru</span>
            </button>
        </div>
    </form>
</div>
@endsection