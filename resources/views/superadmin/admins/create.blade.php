@extends('layouts.app', ['header' => 'Tambah Admin Baru'])

@section('content')
<div class="max-w-4xl mx-auto">

    <div class="bg-white p-6 sm:p-8 rounded-2xl shadow-lg">
        <form action="{{ route('admins.store') }}" method="POST">
            @csrf

            <div class="mb-8 pb-6 border-b border-slate-200">
                <h1 class="text-2xl font-bold text-slate-900">Buat Akun Administrator Baru</h1>
                <p class="mt-1 text-sm text-slate-500">Isi detail di bawah ini untuk mendaftarkan admin regional baru ke dalam sistem.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-10">

                <div class="space-y-6">
                    <h2 class="text-lg font-semibold text-slate-800">Informasi Akun</h2>

                    <div>
                        <label for="name" class="block text-sm font-medium leading-6 text-slate-700 mb-1.5">Nama Lengkap</label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required
                               class="block w-full rounded-md border-slate-300 py-2 px-3 text-slate-900 shadow-sm focus:ring-2 focus:ring-inset focus:ring-cyan-500 @error('name') border-red-500 ring-red-500 @enderror">
                        @error('name')
                            <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium leading-6 text-slate-700 mb-1.5">Email</label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" required
                               class="block w-full rounded-md border-slate-300 py-2 px-3 text-slate-900 shadow-sm focus:ring-2 focus:ring-inset focus:ring-cyan-500 @error('email') border-red-500 ring-red-500 @enderror">
                         @error('email')
                            <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="phone" class="block text-sm font-medium leading-6 text-slate-700 mb-1.5">Nomor HP</label>
                        <input type="text" name="phone" id="phone" value="{{ old('phone') }}" required
                               class="block w-full rounded-md border-slate-300 py-2 px-3 text-slate-900 shadow-sm focus:ring-2 focus:ring-inset focus:ring-cyan-500 @error('phone') border-red-500 ring-red-500 @enderror">
                        @error('phone')
                            <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    <div x-data="{ 
                        password: '', 
                        isRevealed: false,
                        generatePassword() {
                            const length = 12;
                            const charset = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()';
                            let retVal = '';
                            for (let i = 0, n = charset.length; i < length; ++i) {
                                retVal += charset.charAt(Math.floor(Math.random() * n));
                            }
                            this.password = retVal;
                        }
                    }">
                        <label for="password" class="block text-sm font-medium leading-6 text-slate-700 mb-1.5">Password</label>
                        <div class="relative">
                            <input :type="isRevealed ? 'text' : 'password'" name="password" id="password" x-model="password"
                                   class="block w-full rounded-md border-slate-300 py-2 px-3 text-slate-900 shadow-sm focus:ring-2 focus:ring-inset focus:ring-cyan-500 @error('password') border-red-500 ring-red-500 @enderror">
                            <button type="button" @click="isRevealed = !isRevealed" class="absolute inset-y-0 right-0 flex items-center px-3 text-slate-500 hover:text-slate-800">
                                <svg x-show="!isRevealed" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                <svg x-show="isRevealed" x-cloak class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L6.228 6.228" /></svg>
                            </button>
                        </div>
                        <div class="mt-2 flex items-center justify-between">
                             <p class="text-xs text-slate-500">Kosongkan untuk default: <code class="font-mono bg-slate-100 p-1 rounded">admin123</code></p>
                             <button type="button" @click="generatePassword()" class="text-xs font-semibold text-cyan-600 hover:text-cyan-800">Generate</button>
                        </div>
                        @error('password')
                            <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="space-y-6">
                    <h2 class="text-lg font-semibold text-slate-800">Detail Administratif</h2>
                    
                    <div>
                        <label for="region_name" class="block text-sm font-medium leading-6 text-slate-700 mb-1.5">Nama Wilayah</label>
                        <input type="text" name="region_name" id="region_name" value="{{ old('region_name') }}" required placeholder="Contoh: Jakarta Selatan"
                               class="block w-full rounded-md border-slate-300 py-2 px-3 text-slate-900 shadow-sm focus:ring-2 focus:ring-inset focus:ring-cyan-500 @error('region_name') border-red-500 ring-red-500 @enderror">
                        @error('region_name')
                            <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="province" class="block text-sm font-medium leading-6 text-slate-700 mb-1.5">Provinsi</label>
                        <input type="text" name="province" id="province" value="{{ old('province') }}" required placeholder="Contoh: DKI Jakarta"
                               class="block w-full rounded-md border-slate-300 py-2 px-3 text-slate-900 shadow-sm focus:ring-2 focus:ring-inset focus:ring-cyan-500 @error('province') border-red-500 ring-red-500 @enderror">
                        @error('province')
                            <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="position_title" class="block text-sm font-medium leading-6 text-slate-700 mb-1.5">Jabatan <span class="text-slate-400">(Opsional)</span></label>
                        <input type="text" name="position_title" id="position_title" value="{{ old('position_title') }}" placeholder="Contoh: Kepala Regional"
                               class="block w-full rounded-md border-slate-300 py-2 px-3 text-slate-900 shadow-sm focus:ring-2 focus:ring-inset focus:ring-cyan-500">
                    </div>

                    <div>
                        <label for="contact_number" class="block text-sm font-medium leading-6 text-slate-700 mb-1.5">Kontak Langsung <span class="text-slate-400">(Opsional)</span></label>
                        <input type="text" name="contact_number" id="contact_number" value="{{ old('contact_number') }}" placeholder="Kontak darurat atau kantor"
                               class="block w-full rounded-md border-slate-300 py-2 px-3 text-slate-900 shadow-sm focus:ring-2 focus:ring-inset focus:ring-cyan-500">
                    </div>
                </div>
            </div>

            <div class="mt-10 pt-6 border-t border-slate-200 flex justify-between items-center">
                <a href="{{ route('admins.index') }}" class="text-sm font-semibold text-slate-600 hover:text-slate-800 transition">
                    &larr; Batal
                </a>
                <button type="submit"
                        class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-blue-600 text-white font-bold rounded-lg shadow-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                    <span>Simpan Admin Baru</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection