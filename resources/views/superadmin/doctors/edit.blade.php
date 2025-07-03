@extends('layouts.app', ['header' => 'Edit Data Dokter'])

@section('content')
<div class="max-w-4xl mx-auto">

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-800">Edit Data: {{ $doctor->user->name }}</h1>
        <p class="mt-1 text-sm text-slate-500">Perbarui detail personal, profesional, dan penugasan untuk dokter ini.</p>
    </div>

    <form action="{{ route('doctors.update', $doctor) }}" method="POST" id="editDoctorForm">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">

            <div class="space-y-6 bg-white border border-slate-200 rounded-xl p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-slate-800 border-b border-slate-200 pb-3">Informasi Akun & Personal</h2>
                
                <div>
                    <label for="name" class="block text-sm font-medium leading-6 text-slate-700 mb-1.5">Nama Lengkap</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $doctor->user->name) }}" required
                           class="block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('name') border-red-500 @enderror">
                    @error('name')
                        <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium leading-6 text-slate-700 mb-1.5">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $doctor->user->email) }}" required
                           class="block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('email') border-red-500 @enderror">
                     @error('email')
                        <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="phone" class="block text-sm font-medium leading-6 text-slate-700 mb-1.5">Nomor HP (Opsional)</label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone', $doctor->user->phone) }}"
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
                            <option value="male" @selected(old('gender', $doctor->gender) == 'male')>Laki-laki</option>
                            <option value="female" @selected(old('gender', $doctor->gender) == 'female')>Perempuan</option>
                        </select>
                    </div>
                    <div>
                        <label for="birth_date" class="block text-sm font-medium leading-6 text-slate-700 mb-1.5">Tanggal Lahir</label>
                        <input type="date" name="birth_date" id="birth_date" value="{{ old('birth_date', optional($doctor->birth_date)->format('Y-m-d')) }}" class="block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    </div>
                </div>
            </div>

            <div class="space-y-6 bg-white border border-slate-200 rounded-xl p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-slate-800 border-b border-slate-200 pb-3">Informasi Profesional</h2>
                
                <div>
                    <label for="specialist" class="block text-sm font-medium leading-6 text-slate-700 mb-1.5">Spesialisasi</label>
                    <input type="text" name="specialist" id="specialist" value="{{ old('specialist', $doctor->specialist) }}"
                           class="block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                </div>

                <div>
                    <label for="license_number" class="block text-sm font-medium leading-6 text-slate-700 mb-1.5">Nomor Izin Praktek (STR/SIP)</label>
                    <input type="text" name="license_number" id="license_number" value="{{ old('license_number', $doctor->license_number) }}"
                           class="block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                </div>
                
                <div>
                    <label for="outlet_id" class="block text-sm font-medium leading-6 text-slate-700 mb-1.5">Ditugaskan di Outlet</label>
                    <select id="outlet_id" name="outlet_id" class="block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('outlet_id') border-red-500 @enderror">
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
    </form>
    
    <div class="mt-8 flex justify-between items-center">
        <a href="{{ route('doctors.index') }}" class="text-sm text-slate-600 hover:text-slate-900 transition hover:underline">
            &larr; Batal
        </a>
        <button type="submit" form="editDoctorForm"
                class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-blue-600 text-white font-bold rounded-lg shadow-md hover:bg-blue-700 transition-all duration-200">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
            <span>Simpan Perubahan</span>
        </button>
    </div>

    <div class="mt-12 pt-6 border-t border-slate-200">
         <div class="bg-red-50 border border-red-200 rounded-xl p-6">
            <div class="flex flex-col md:flex-row gap-4 md:items-center">
                <div class="flex-shrink-0">
                    <svg class="w-10 h-10 text-red-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" /></svg>
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-semibold text-slate-800">Zona Berbahaya</h3>
                    <p class="text-sm text-red-700 mt-1">Tindakan menghapus akun dokter tidak dapat diurungkan. Ini akan menghapus data user dan dokter terkait.</p>
                </div>
                <div class="flex-shrink-0 mt-4 md:mt-0">
                    <form action="{{ route('doctors.destroy', $doctor) }}" method="POST" onsubmit="return confirm('PERINGATAN! Anda akan menghapus dokter ini secara permanen. Lanjutkan?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center gap-2 px-5 py-2 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700 transition">
                             <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" /></svg>
                            <span>Hapus Permanen</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection