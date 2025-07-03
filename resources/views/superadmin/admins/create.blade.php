@extends('layouts.app', ['header' => 'Tambah Admin Baru'])

@section('content')
<div class="max-w-4xl mx-auto">

    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-white">Buat Akun Administrator Baru</h1>
        <p class="mt-1 text-sm text-slate-400">Isi detail di bawah ini untuk mendaftarkan admin regional baru ke dalam sistem.</p>
    </div>

    <form action="{{ route('admins.store') }}" method="POST">
        @csrf
        <!-- Wrapper Form dengan Grid 2 Kolom -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">

            <!-- Kolom Kiri: Informasi Akun -->
            <div class="space-y-6 bg-slate-800/50 border border-slate-700/80 rounded-xl p-6">
                <h2 class="text-lg font-semibold text-white border-b border-slate-700 pb-3">Informasi Akun</h2>
                <p class="text-sm text-slate-500 -mt-4">Detail ini akan digunakan oleh admin untuk login ke sistem.</p>

                <!-- Nama Lengkap -->
                <div>
                    <label for="name" class="block text-sm font-medium leading-6 text-slate-300 mb-1.5">Nama Lengkap</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                           class="block w-full rounded-md border-0 bg-white/5 py-2 px-3 text-white ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-inset focus:ring-cyan-500 @error('name') ring-red-500 @enderror">
                    @error('name')
                        <p class="text-sm text-red-400 mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium leading-6 text-slate-300 mb-1.5">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required
                           class="block w-full rounded-md border-0 bg-white/5 py-2 px-3 text-white ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-inset focus:ring-cyan-500 @error('email') ring-red-500 @enderror">
                     @error('email')
                        <p class="text-sm text-red-400 mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Nomor HP -->
                 <div>
                    <label for="phone" class="block text-sm font-medium leading-6 text-slate-300 mb-1.5">Nomor HP</label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone') }}" required
                           class="block w-full rounded-md border-0 bg-white/5 py-2 px-3 text-white ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-inset focus:ring-cyan-500 @error('phone') ring-red-500 @enderror">
                     @error('phone')
                        <p class="text-sm text-red-400 mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-medium leading-6 text-slate-300 mb-1.5">Password</label>
                    <input type="password" name="password" id="password"
                           class="block w-full rounded-md border-0 bg-white/5 py-2 px-3 text-white ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-inset focus:ring-cyan-500 @error('password') ring-red-500 @enderror">
                    <p class="text-xs text-slate-500 mt-2">Kosongkan untuk menggunakan password default: <code class="font-mono bg-slate-700 p-1 rounded">admin123</code></p>
                    @error('password')
                        <p class="text-sm text-red-400 mt-2">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Kolom Kanan: Detail Administratif -->
            <div class="space-y-6 bg-slate-800/50 border border-slate-700/80 rounded-xl p-6">
                <h2 class="text-lg font-semibold text-white border-b border-slate-700 pb-3">Detail Administratif</h2>
                <p class="text-sm text-slate-500 -mt-4">Informasi terkait peran dan wilayah kerja admin.</p>
                
                <!-- Wilayah (Contoh: Jakarta Selatan) -->
                <div>
                    <label for="region_name" class="block text-sm font-medium leading-6 text-slate-300 mb-1.5">Nama Wilayah</label>
                    <input type="text" name="region_name" id="region_name" value="{{ old('region_name') }}" required placeholder="Contoh: Jakarta Selatan"
                           class="block w-full rounded-md border-0 bg-white/5 py-2 px-3 text-white ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-inset focus:ring-cyan-500 @error('region_name') ring-red-500 @enderror">
                    @error('region_name')
                        <p class="text-sm text-red-400 mt-2">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Provinsi -->
                <div>
                    <label for="province" class="block text-sm font-medium leading-6 text-slate-300 mb-1.5">Provinsi</label>
                    <input type="text" name="province" id="province" value="{{ old('province') }}" required placeholder="Contoh: DKI Jakarta"
                           class="block w-full rounded-md border-0 bg-white/5 py-2 px-3 text-white ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-inset focus:ring-cyan-500 @error('province') ring-red-500 @enderror">
                    @error('province')
                        <p class="text-sm text-red-400 mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Jabatan -->
                <div>
                    <label for="position_title" class="block text-sm font-medium leading-6 text-slate-300 mb-1.5">Jabatan (Opsional)</label>
                    <input type="text" name="position_title" id="position_title" value="{{ old('position_title') }}" placeholder="Contoh: Kepala Regional"
                           class="block w-full rounded-md border-0 bg-white/5 py-2 px-3 text-white ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-inset focus:ring-cyan-500">
                </div>

                <!-- Kontak Langsung -->
                 <div>
                    <label for="contact_number" class="block text-sm font-medium leading-6 text-slate-300 mb-1.5">Kontak Langsung (Opsional)</label>
                    <input type="text" name="contact_number" id="contact_number" value="{{ old('contact_number') }}" placeholder="Kontak darurat atau kantor"
                           class="block w-full rounded-md border-0 bg-white/5 py-2 px-3 text-white ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-inset focus:ring-cyan-500">
                </div>
            </div>

        </div>

        <!-- Tombol Aksi di Bawah Form -->
        <div class="mt-8 flex justify-between items-center">
            <a href="{{ route('admins.index') }}" class="text-sm text-slate-400 hover:text-white transition">
                &larr; Batal dan Kembali
            </a>
            <button type="submit"
                    class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-gradient-to-r from-blue-600 to-cyan-500 text-white font-bold rounded-lg shadow-lg shadow-cyan-500/20 transition-transform duration-200 hover:scale-105">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                <span>Simpan Admin Baru</span>
            </button>
        </div>
    </form>
</div>
@endsection