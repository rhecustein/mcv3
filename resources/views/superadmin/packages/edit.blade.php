@extends('layouts.app', ['header' => 'Edit Paket Langganan'])

@section('content')
<div class="max-w-4xl mx-auto">

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-800">Edit Paket: {{ $package->name }}</h1>
        <p class="mt-1 text-sm text-slate-500">Perbarui detail, batasan, dan harga untuk paket langganan ini.</p>
    </div>

    @if ($errors->any())
        <div class="mb-6 bg-red-50 border border-red-200 text-sm text-red-700 rounded-lg p-4" role="alert">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" /></svg>
                </div>
                <div class="ml-3">
                    <h3 class="font-semibold">Terdapat kesalahan pada input Anda:</h3>
                    <ul class="mt-2 list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif


    <form action="{{ route('packages.update', $package) }}" method="POST" id="editPackageForm">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">

            <div class="space-y-6 bg-white border border-slate-200 rounded-xl p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-slate-800 border-b border-slate-200 pb-3">Detail Utama Paket</h2>
                
                <div>
                    <label for="name" class="block text-sm font-medium leading-6 text-slate-700 mb-1.5">Nama Paket</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $package->name) }}" required
                           class="block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                </div>
                
                <div>
                    <label for="description" class="block text-sm font-medium leading-6 text-slate-700 mb-1.5">Deskripsi Singkat</label>
                    <textarea name="description" id="description" rows="3"
                              class="block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">{{ old('description', $package->description) }}</textarea>
                </div>

                <div>
                    <label for="price" class="block text-sm font-medium leading-6 text-slate-700 mb-1.5">Harga Paket (Rp)</label>
                    <div class="relative">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                            <span class="text-slate-500 sm:text-sm">Rp</span>
                        </div>
                        <input type="number" name="price" id="price" value="{{ old('price', $package->price) }}" required step="1000"
                               class="block w-full rounded-md border-slate-300 shadow-sm pl-8 focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    </div>
                </div>
                 
                <div>
                    <label for="duration_in_days" class="block text-sm font-medium leading-6 text-slate-700 mb-1.5">Durasi (dalam hari)</label>
                    <input type="number" name="duration_in_days" id="duration_in_days" value="{{ old('duration_in_days', $package->duration_in_days) }}" required
                           class="block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                </div>
            </div>

            <div class="space-y-6 bg-white border border-slate-200 rounded-xl p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-slate-800 border-b border-slate-200 pb-3">Aturan & Batasan Kuota</h2>
                
                <div>
                    <label for="max_letters" class="block text-sm font-medium leading-6 text-slate-700 mb-1.5">Batas Surat per Bulan</label>
                    <input type="number" name="max_letters" id="max_letters" value="{{ old('max_letters', $package->max_letters) }}"
                           class="block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    <p class="mt-2 text-xs text-slate-500">Biarkan kosong untuk kuota tak terbatas (unlimited).</p>
                </div>

                <div>
                    <label for="max_patients" class="block text-sm font-medium leading-6 text-slate-700 mb-1.5">Batas Total Pasien</label>
                    <input type="number" name="max_patients" id="max_patients" value="{{ old('max_patients', $package->max_patients) }}"
                           class="block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    <p class="mt-2 text-xs text-slate-500">Biarkan kosong untuk kuota tak terbatas (unlimited).</p>
                </div>
                
                <div class="relative flex items-start pt-4 border-t border-slate-200">
                    <div class="flex h-6 items-center">
                        <input id="is_active" name="is_active" type="checkbox" value="1" @checked(old('is_active', $package->is_active))
                               class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-600">
                    </div>
                    <div class="ml-3 text-sm leading-6">
                        <label for="is_active" class="font-medium text-slate-800">Aktifkan Paket</label>
                        <p class="text-slate-500">Jika tidak dicentang, paket ini tidak akan bisa dipilih oleh perusahaan baru.</p>
                    </div>
                </div>
            </div>

        </div>
    </form>
    
    <div class="mt-8 flex justify-between items-center">
        <a href="{{ route('packages.index') }}" class="text-sm text-slate-600 hover:text-slate-900 transition hover:underline">
            &larr; Batal
        </a>
        <button type="submit" form="editPackageForm"
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
                    <p class="text-sm text-red-700 mt-1">Tindakan menghapus paket ini tidak dapat diurungkan.</p>
                </div>
                <div class="flex-shrink-0 mt-4 md:mt-0">
                    <form action="{{ route('packages.destroy', $package) }}" method="POST" onsubmit="return confirm('PERINGATAN! Anda akan menghapus paket ini secara permanen. Lanjutkan?');">
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