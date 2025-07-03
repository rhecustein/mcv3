@extends('layouts.app', ['header' => 'Edit Administrator'])

@section('content')
<div class="max-w-4xl mx-auto">

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-white">Edit Data Admin: {{ $admin->user->name }}</h1>
        <p class="mt-1 text-sm text-slate-400">Perbarui detail akun dan informasi administratif untuk pengguna ini.</p>
    </div>

    <form action="{{ route('admins.update', $admin) }}" method="POST" id="editAdminForm">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">

            <div class="space-y-6 bg-slate-800/50 border border-slate-700/80 rounded-xl p-6">
                <h2 class="text-lg font-semibold text-white border-b border-slate-700 pb-3">Informasi Akun</h2>
                
                <div>
                    <label for="name" class="block text-sm font-medium leading-6 text-slate-300 mb-1.5">Nama Lengkap</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $admin->user->name) }}" required
                           class="block w-full rounded-md border-0 bg-white/5 py-2 px-3 text-white ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-inset focus:ring-cyan-500 @error('name') ring-red-500 @enderror">
                    @error('name')
                        <p class="text-sm text-red-400 mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium leading-6 text-slate-300 mb-1.5">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $admin->user->email) }}" required
                           class="block w-full rounded-md border-0 bg-white/5 py-2 px-3 text-white ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-inset focus:ring-cyan-500 @error('email') ring-red-500 @enderror">
                     @error('email')
                        <p class="text-sm text-red-400 mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="phone" class="block text-sm font-medium leading-6 text-slate-300 mb-1.5">Nomor HP</label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone', $admin->user->phone) }}" required
                           class="block w-full rounded-md border-0 bg-white/5 py-2 px-3 text-white ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-inset focus:ring-cyan-500 @error('phone') ring-red-500 @enderror">
                     @error('phone')
                        <p class="text-sm text-red-400 mt-2">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="space-y-6 bg-slate-800/50 border border-slate-700/80 rounded-xl p-6">
                <h2 class="text-lg font-semibold text-white border-b border-slate-700 pb-3">Detail Administratif</h2>
                
                <div>
                    <label for="region_name" class="block text-sm font-medium leading-6 text-slate-300 mb-1.5">Nama Wilayah</label>
                    <input type="text" name="region_name" id="region_name" value="{{ old('region_name', $admin->region_name) }}" required placeholder="Contoh: Jakarta Selatan"
                           class="block w-full rounded-md border-0 bg-white/5 py-2 px-3 text-white ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-inset focus:ring-cyan-500 @error('region_name') ring-red-500 @enderror">
                    @error('region_name')
                        <p class="text-sm text-red-400 mt-2">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="province" class="block text-sm font-medium leading-6 text-slate-300 mb-1.5">Provinsi</label>
                    <input type="text" name="province" id="province" value="{{ old('province', $admin->province) }}" required placeholder="Contoh: DKI Jakarta"
                           class="block w-full rounded-md border-0 bg-white/5 py-2 px-3 text-white ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-inset focus:ring-cyan-500 @error('province') ring-red-500 @enderror">
                    @error('province')
                        <p class="text-sm text-red-400 mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="position_title" class="block text-sm font-medium leading-6 text-slate-300 mb-1.5">Jabatan (Opsional)</label>
                    <input type="text" name="position_title" id="position_title" value="{{ old('position_title', $admin->position_title) }}" placeholder="Contoh: Kepala Regional"
                           class="block w-full rounded-md border-0 bg-white/5 py-2 px-3 text-white ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-inset focus:ring-cyan-500">
                </div>

                <div>
                    <label for="contact_number" class="block text-sm font-medium leading-6 text-slate-300 mb-1.5">Kontak Langsung (Opsional)</label>
                    <input type="text" name="contact_number" id="contact_number" value="{{ old('contact_number', $admin->contact_number) }}" placeholder="Kontak darurat atau kantor"
                           class="block w-full rounded-md border-0 bg-white/5 py-2 px-3 text-white ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-inset focus:ring-cyan-500">
                </div>
            </div>
        </div>
    </form>

    <div class="mt-8 flex justify-between items-center">
        <a href="{{ route('admins.index') }}" class="text-sm text-slate-400 hover:text-white transition">
            &larr; Batal
        </a>
        <button type="submit" form="editAdminForm"
                class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-gradient-to-r from-blue-600 to-cyan-500 text-white font-bold rounded-lg shadow-lg shadow-cyan-500/20 transition-transform duration-200 hover:scale-105">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
            <span>Simpan Perubahan</span>
        </button>
    </div>

    <div class="mt-12 pt-6 border-t border-slate-700/50">
         <div class="bg-red-900/20 border border-red-500/30 rounded-xl p-6">
            <div class="flex flex-col md:flex-row gap-4 md:items-center">
                <div class="flex-shrink-0">
                    <svg class="w-10 h-10 text-red-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" /></svg>
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-semibold text-white">Zona Berbahaya</h3>
                    <p class="text-sm text-red-300 mt-1">Tindakan ini tidak dapat diurungkan. Menghapus admin akan menghapus data mereka secara permanen.</p>
                </div>
                <div class="flex-shrink-0 mt-4 md:mt-0">
                    <form action="{{ route('admins.destroy', $admin) }}" method="POST" onsubmit="return confirm('PERINGATAN! Anda akan menghapus admin ini secara permanen. Lanjutkan?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center gap-2 px-5 py-2 bg-red-600/80 text-white font-semibold rounded-lg hover:bg-red-600 transition">
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