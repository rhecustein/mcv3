@extends('layouts.app', ['header' => 'Edit Data Dokter'])

@section('content')
{{-- Memberi background abu-abu lembut pada halaman agar card putih lebih menonjol --}}
<div class="bg-slate-50 min-h-screen">
    <div class="max-w-5xl mx-auto py-12 px-4 sm:px-6 lg:px-8">

        {{-- Header Halaman --}}
        <div class="mb-8 text-center">
            <h1 class="text-3xl font-bold tracking-tight text-slate-900">Edit Data: {{ $doctor->user->name }}</h1>
            <p class="mt-2 text-base text-slate-600">Perbarui detail personal, profesional, dan penugasan untuk dokter ini.</p>
        </div>

        {{-- Form Utama --}}
        <form action="{{ route('superadmin.doctors.update', $doctor) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="bg-white rounded-2xl shadow-lg mb-8">
                <div class="grid grid-cols-1 lg:grid-cols-2">

                    {{-- Kolom Kiri: Informasi Personal & Akun --}}
                    <div class="p-8 border-b lg:border-b-0 lg:border-r border-slate-200">
                        <h2 class="text-xl font-semibold text-slate-800 flex items-center gap-3">
                            <svg class="w-6 h-6 text-blue-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                            </svg>
                            Informasi Personal & Akun
                        </h2>
                        <div class="mt-6 space-y-6">
                            
                            <!-- Input Nama Lengkap -->
                            <div>
                                <label for="name" class="block text-sm font-medium leading-6 text-slate-700 mb-1.5">Nama Lengkap</label>
                                <div class="relative">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                        <svg class="h-5 w-5 text-slate-400" viewBox="0 0 20 20" fill="currentColor"><path d="M10 8a3 3 0 100-6 3 3 0 000 6zM3.465 14.493a1.23 1.23 0 00.41 1.412A9.957 9.957 0 0010 18c2.31 0 4.438-.784 6.131-2.1.43-.333.604-.903.408-1.41a7.002 7.002 0 00-13.074.003z" /></svg>
                                    </div>
                                    <input type="text" name="name" id="name" value="{{ old('name', $doctor->user->name) }}" required
                                           class="block w-full rounded-lg border-0 py-2.5 pl-10 bg-slate-50 text-slate-900 ring-1 ring-inset ring-slate-300 placeholder:text-slate-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm @error('name') ring-red-500 @enderror">
                                </div>
                                @error('name')
                                    <p class="text-sm text-red-600 mt-2 flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" /></svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <!-- Input Email -->
                            <div>
                                <label for="email" class="block text-sm font-medium leading-6 text-slate-700 mb-1.5">Alamat Email</label>
                                <div class="relative">
                                     <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                        <svg class="h-5 w-5 text-slate-400" viewBox="0 0 20 20" fill="currentColor"><path d="M3 4a2 2 0 00-2 2v1.161l8.441 4.221a1.25 1.25 0 001.118 0L19 7.162V6a2 2 0 00-2-2H3z" /><path d="M19 8.839l-7.77 3.885a2.75 2.75 0 01-2.46 0L1 8.839V14a2 2 0 002 2h14a2 2 0 002-2V8.839z" /></svg>
                                    </div>
                                    <input type="email" name="email" id="email" value="{{ old('email', $doctor->user->email) }}" required
                                           class="block w-full rounded-lg border-0 py-2.5 pl-10 bg-slate-50 text-slate-900 ring-1 ring-inset ring-slate-300 placeholder:text-slate-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm @error('email') ring-red-500 @enderror">
                                </div>
                                @error('email')
                                    <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Input Nomor HP -->
                            <div>
                                <label for="phone" class="block text-sm font-medium leading-6 text-slate-700 mb-1.5">Nomor HP (Opsional)</label>
                                <div class="relative">
                                     <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                        <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 0 0 6 3.75v16.5a2.25 2.25 0 0 0 2.25 2.25h7.5A2.25 2.25 0 0 0 18 20.25V3.75a2.25 2.25 0 0 0-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3" /></svg>
                                    </div>
                                    <input type="text" name="phone" id="phone" value="{{ old('phone', $doctor->user->phone) }}"
                                           class="block w-full rounded-lg border-0 py-2.5 pl-10 bg-slate-50 text-slate-900 ring-1 ring-inset ring-slate-300 placeholder:text-slate-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm @error('phone') ring-red-500 @enderror">
                                </div>
                                @error('phone')
                                    <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Kolom Kanan: Informasi Profesional --}}
                    <div class="p-8">
                        <h2 class="text-xl font-semibold text-slate-800 flex items-center gap-3">
                             <svg class="w-6 h-6 text-blue-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                            </svg>
                            Informasi Profesional & Penugasan
                        </h2>
                        <div class="mt-6 space-y-6">
                            
                            <!-- Grid untuk Jenis Kelamin & Tanggal Lahir -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                <div>
                                    <label for="gender" class="block text-sm font-medium leading-6 text-slate-700 mb-1.5">Jenis Kelamin</label>
                                    <select id="gender" name="gender" class="block w-full rounded-lg border-0 py-2.5 bg-slate-50 text-slate-900 ring-1 ring-inset ring-slate-300 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm @error('gender') ring-red-500 @enderror">
                                        <option value="">-- Pilih --</option>
                                        <option value="male" @selected(old('gender', $doctor->gender) == 'male')>Laki-laki</option>
                                        <option value="female" @selected(old('gender', $doctor->gender) == 'female')>Perempuan</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="birth_date" class="block text-sm font-medium leading-6 text-slate-700 mb-1.5">Tanggal Lahir</label>
                                    <input type="date" name="birth_date" id="birth_date" value="{{ old('birth_date', optional($doctor->birth_date)->format('Y-m-d')) }}" class="block w-full rounded-lg border-0 py-2 bg-slate-50 text-slate-900 ring-1 ring-inset ring-slate-300 placeholder:text-slate-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm">
                                </div>
                            </div>

                            <!-- Input Spesialisasi -->
                            <div>
                                <label for="specialist" class="block text-sm font-medium leading-6 text-slate-700 mb-1.5">Spesialisasi</label>
                                <input type="text" name="specialist" id="specialist" value="{{ old('specialist', $doctor->specialist) }}"
                                       class="block w-full rounded-lg border-0 py-2.5 px-3 bg-slate-50 text-slate-900 ring-1 ring-inset ring-slate-300 placeholder:text-slate-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm">
                            </div>

                            <!-- Input Nomor Izin Praktek -->
                            <div>
                                <label for="license_number" class="block text-sm font-medium leading-6 text-slate-700 mb-1.5">Nomor Izin Praktek (STR/SIP)</label>
                                <input type="text" name="license_number" id="license_number" value="{{ old('license_number', $doctor->license_number) }}"
                                       class="block w-full rounded-lg border-0 py-2.5 px-3 bg-slate-50 text-slate-900 ring-1 ring-inset ring-slate-300 placeholder:text-slate-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm">
                            </div>

                             <!-- Input Penugasan Outlet -->
                            <div>
                                <label for="outlet_id" class="block text-sm font-medium leading-6 text-slate-700 mb-1.5">Ditugaskan di Outlet</label>
                                <select id="outlet_id" name="outlet_id" class="block w-full rounded-lg border-0 py-2.5 bg-slate-50 text-slate-900 ring-1 ring-inset ring-slate-300 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm @error('outlet_id') ring-red-500 @enderror">
                                    <option value="">-- Pilih Outlet --</option>
                                    @foreach($outlets as $outlet)
                                        <option value="{{ $outlet->id }}" @selected(old('outlet_id', $doctor->outlet_id) == $outlet->id)>
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
                </div>

                {{-- Footer Form: Tombol Aksi --}}
                <div class="flex items-center justify-between gap-6 bg-slate-50 rounded-b-2xl px-8 py-5">
                    <a href="{{ route('superadmin.doctors.index') }}" class="text-sm font-semibold text-slate-600 hover:text-slate-800 transition-colors">
                        Batal
                    </a>
                    <button type="submit"
                            class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-blue-600 text-white font-semibold rounded-lg shadow-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                        <span>Simpan Perubahan</span>
                    </button>
                </div>
            </div>
        </form>

        {{-- Zona Berbahaya --}}
        <div class="mt-12 pt-8 border-t border-slate-200">
             <div class="bg-red-50/50 border border-red-200 rounded-2xl p-6">
                <div class="flex flex-col md:flex-row gap-6 md:items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                            <svg class="w-7 h-7 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                            </svg>
                        </div>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-red-900">Zona Berbahaya</h3>
                        <p class="text-sm text-red-800 mt-1 max-w-xl">
                            Menghapus akun dokter adalah tindakan permanen dan tidak dapat diurungkan. Semua data terkait, termasuk jadwal dan rekam medis yang terhubung, akan dihapus.
                        </p>
                    </div>
                    <div class="flex-shrink-0 mt-4 md:mt-0">
                        <form action="{{ route('superadmin.doctors.destroy', $doctor) }}" method="POST" onsubmit="return confirm('PERINGATAN! Anda akan menghapus dokter ini secara permanen. Lanjutkan?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 text-white font-semibold text-sm rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" /></svg>
                                <span>Hapus Permanen</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
