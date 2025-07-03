@extends('layouts.app', ['header' => 'Tambah Dokter Baru'])

@section('content')
<div class="max-w-4xl mx-auto">

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-800">Daftarkan Dokter Baru</h1>
        <p class="mt-1 text-sm text-slate-500">Isi detail di bawah untuk menambahkan dokter baru ke dalam sistem dan menugaskannya ke outlet.</p>
    </div>

    <form action="{{ route('doctors.store') }}" method="POST">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">

            <div class="space-y-6 bg-white border border-slate-200 rounded-xl p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-slate-800 border-b border-slate-200 pb-3">Informasi Akun & Personal</h2>
                
                <div>
                    <label for="name" class="block text-sm font-medium leading-6 text-slate-700 mb-1.5">Nama Lengkap</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required placeholder="Contoh: Dr. Budi Santoso"
                           class="block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('name') border-red-500 @enderror">
                    @error('name')
                        <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium leading-6 text-slate-700 mb-1.5">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required placeholder="email@dokter.com"
                           class="block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('email') border-red-500 @enderror">
                     @error('email')
                        <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="phone" class="block text-sm font-medium leading-6 text-slate-700 mb-1.5">Nomor HP (Opsional)</label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone') }}" placeholder="08xxxxxxxxxx"
                           class="block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('phone') border-red-500 @enderror">
                     @error('phone')
                        <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="gender" class="block text-sm font-medium leading-6 text-slate-700 mb-1.5">Jenis Kelamin</label>
                        <select id="gender" name="gender" class="block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('gender') border-red-500 @enderror">
                            <option value="">-- Pilih --</option>
                            <option value="male" @selected(old('gender') == 'male')>Laki-laki</option>
                            <option value="female" @selected(old('gender') == 'female')>Perempuan</option>
                        </select>
                    </div>
                    <div>
                        <label for="birth_date" class="block text-sm font-medium leading-6 text-slate-700 mb-1.5">Tanggal Lahir</label>
                        <input type="date" name="birth_date" id="birth_date" value="{{ old('birth_date') }}" class="block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium leading-6 text-slate-700 mb-1.5">Password</label>
                    <input type="password" name="password" id="password" class="block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('password') border-red-500 @enderror">
                    <p class="text-xs text-slate-500 mt-2">Kosongkan untuk menggunakan password default: <code class="font-mono bg-slate-200 text-slate-700 p-0.5 rounded">doctor123</code></p>
                    @error('password')
                        <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="space-y-6 bg-white border border-slate-200 rounded-xl p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-slate-800 border-b border-slate-200 pb-3">Informasi Profesional</h2>
                
                <div>
                    <label for="specialist" class="block text-sm font-medium leading-6 text-slate-700 mb-1.5">Spesialisasi</label>
                    <input type="text" name="specialist" id="specialist" value="{{ old('specialist') }}" placeholder="Contoh: Dokter Umum"
                           class="block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                </div>

                <div>
                    <label for="license_number" class="block text-sm font-medium leading-6 text-slate-700 mb-1.5">Nomor Izin Praktek (STR/SIP)</label>
                    <input type="text" name="license_number" id="license_number" value="{{ old('license_number') }}"
                           class="block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                </div>
                
                <div>
                    <label for="outlet_id" class="block text-sm font-medium leading-6 text-slate-700 mb-1.5">Ditugaskan di Outlet</label>
                    <select id="outlet_id" name="outlet_id" class="block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('outlet_id') border-red-500 @enderror">
                        <option value="">-- Pilih Outlet --</option>
                        @foreach($outlets as $outlet)
                            <option value="{{ $outlet->id }}" @selected(old('outlet_id') == $outlet->id)>
                                {{ $outlet->name }} ({{ $outlet->city ?? '-' }})
                            </option>
                        @endforeach
                    </select>
                     @error('outlet_id')
                        <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                    @enderror
                </div>
            </div>

        </div>

        <div class="mt-8 flex justify-between items-center">
            <a href="{{ route('doctors.index') }}" class="text-sm text-slate-600 hover:text-slate-900 transition hover:underline">
                &larr; Batal
            </a>
            <button type="submit"
                    class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-blue-600 text-white font-bold rounded-lg shadow-md hover:bg-blue-700 transition-all duration-200">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                <span>Simpan Dokter Baru</span>
            </button>
        </div>
    </form>
</div>
@endsection