@extends('layouts.app', ['header' => 'Tambah Paket Baru'])

@section('content')
<div class="max-w-4xl mx-auto">

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-800">Buat Paket Langganan Baru</h1>
        <p class="mt-1 text-sm text-slate-500">Definisikan fitur, batasan, dan harga untuk paket baru yang akan ditawarkan ke perusahaan.</p>
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


    <form action="{{ route('packages.store') }}" method="POST">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">

            <div class="space-y-6 bg-white border border-slate-200 rounded-xl p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-slate-800 border-b border-slate-200 pb-3">Detail Utama Paket</h2>
                
                <div>
                    <label for="name" class="block text-sm font-medium leading-6 text-slate-700 mb-1.5">Nama Paket</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required placeholder="Contoh: Basic, Pro, Enterprise"
                           class="block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                </div>
                
                <div>
                    <label for="description" class="block text-sm font-medium leading-6 text-slate-700 mb-1.5">Deskripsi Singkat</label>
                    <textarea name="description" id="description" rows="3" placeholder="Jelaskan untuk siapa paket ini cocok..."
                              class="block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">{{ old('description') }}</textarea>
                </div>

                <div>
                    <label for="price" class="block text-sm font-medium leading-6 text-slate-700 mb-1.5">Harga Paket (Rp)</label>
                    <div class="relative">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                            <span class="text-slate-500 sm:text-sm">Rp</span>
                        </div>
                        <input type="number" name="price" id="price" value="{{ old('price') }}" required step="1000"
                               class="block w-full rounded-md border-slate-300 shadow-sm pl-8 focus:border-blue-500 focus:ring-blue-500 sm:text-sm" placeholder="500000">
                    </div>
                </div>
                 
                <div>
                    <label for="duration_in_days" class="block text-sm font-medium leading-6 text-slate-700 mb-1.5">Durasi (dalam hari)</label>
                    <input type="number" name="duration_in_days" id="duration_in_days" value="{{ old('duration_in_days') }}" required placeholder="Contoh: 30 untuk 1 bulan"
                           class="block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                </div>
            </div>

            <div class="space-y-6 bg-white border border-slate-200 rounded-xl p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-slate-800 border-b border-slate-200 pb-3">Aturan & Batasan Kuota</h2>
                
                <div>
                    <label for="max_letters" class="block text-sm font-medium leading-6 text-slate-700 mb-1.5">Batas Surat per Bulan</label>
                    <input type="number" name="max_letters" id="max_letters" value="{{ old('max_letters') }}"
                           class="block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    <p class="mt-2 text-xs text-slate-500">Biarkan kosong untuk kuota tak terbatas (unlimited).</p>
                </div>

                <div>
                    <label for="max_patients" class="block text-sm font-medium leading-6 text-slate-700 mb-1.5">Batas Total Pasien</label>
                    <input type="number" name="max_patients" id="max_patients" value="{{ old('max_patients') }}"
                           class="block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    <p class="mt-2 text-xs text-slate-500">Biarkan kosong untuk kuota tak terbatas (unlimited).</p>
                </div>
                
                <div class="relative flex items-start pt-4 border-t border-slate-200">
                    <div class="flex h-6 items-center">
                        <input id="is_active" name="is_active" type="checkbox" value="1" @checked(old('is_active', true))>
                    </div>
                    <div class="ml-3 text-sm leading-6">
                        <label for="is_active" class="font-medium text-slate-800">Aktifkan Paket</label>
                        <p class="text-slate-500">Paket ini akan langsung tersedia untuk dipilih oleh perusahaan klien.</p>
                    </div>
                </div>
            </div>

        </div>

        <div class="mt-8 flex justify-between items-center">
            <a href="{{ route('packages.index') }}" class="text-sm text-slate-600 hover:text-slate-900 transition hover:underline">
                &larr; Batal
            </a>
            <button type="submit"
                    class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-blue-600 text-white font-bold rounded-lg shadow-md hover:bg-blue-700 transition-all duration-200">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                <span>Simpan Paket Baru</span>
            </button>
        </div>
    </form>
</div>
@endsection