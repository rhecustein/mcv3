@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-2xl shadow-xl border border-slate-200 mb-8 overflow-hidden">
            <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-8 py-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-white flex items-center">
                            <svg class="w-8 h-8 mr-3" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                            </svg>
                            Edit Surat Istirahat
                        </h1>
                        <p class="text-blue-100 mt-1">Outlet: {{ $outlet->name }}</p>
                    </div>
                    <div class="bg-white/20 px-4 py-2 rounded-lg">
                        <span class="text-white font-semibold">{{ $result->unique_code }}</span>
                    </div>
                </div>
            </div>
        </div>

        @if ($errors->any())
            <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-6 rounded-r-lg" role="alert">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">Terjadi Kesalahan:</h3>
                        <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <form method="POST" action="{{ route('outlet.results.mc.update', $result->id) }}" id="edit-mc-form">
            @csrf
            @method('PUT')

            <div class="bg-white rounded-2xl shadow-lg border border-slate-200 mb-6 overflow-hidden">
                <div class="bg-slate-50 px-6 py-4 border-b border-slate-200">
                    <h2 class="text-lg font-semibold text-slate-800 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-slate-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        Data Pasien
                        <span class="ml-2 text-xs bg-red-100 text-red-600 px-2 py-1 rounded-full">Tidak dapat diubah</span>
                    </h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div class="space-y-1">
                            <label class="text-sm font-medium text-slate-600">Nama Pasien</label>
                            <div class="bg-slate-50 border border-slate-200 rounded-lg px-4 py-3 text-slate-800 font-medium">
                                {{ $result->patient->full_name }}
                            </div>
                            <input type="hidden" name="patient_id" value="{{ $result->patient_id }}">
                        </div>
                        <div class="space-y-1">
                            <label class="text-sm font-medium text-slate-600">Tanggal Lahir</label>
                            <div class="bg-slate-50 border border-slate-200 rounded-lg px-4 py-3 text-slate-800">
                                {{ \Carbon\Carbon::parse($result->patient->birth_date)->format('d F Y') }}
                            </div>
                        </div>
                        <div class="space-y-1">
                            <label class="text-sm font-medium text-slate-600">Jenis Kelamin</label>
                            <div class="bg-slate-50 border border-slate-200 rounded-lg px-4 py-3 text-slate-800">
                                {{ $result->patient->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-lg border border-slate-200 mb-6 overflow-hidden">
                <div class="bg-gradient-to-r from-green-50 to-emerald-50 px-6 py-4 border-b border-green-200">
                    <h2 class="text-lg font-semibold text-slate-800 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-green-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                        </svg>
                        Data yang Dapat Diubah
                        <span class="ml-2 text-xs bg-green-100 text-green-600 px-2 py-1 rounded-full">Editable</span>
                    </h2>
                </div>
                <div class="p-6 space-y-6">
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-slate-700">
                            Dokter Pemeriksa <span class="text-red-500">*</span>
                        </label>
                        <select name="doctor_id" class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all @error('doctor_id') border-red-500 @enderror">
                            <option value="">Pilih dokter...</option>
                            @foreach ($doctors as $doctor)
                                <option value="{{ $doctor->id }}" @selected($doctor->id == old('doctor_id', $result->doctor_id))>
                                    {{ $doctor->user->name }} - {{ $doctor->specialist ?? 'Umum' }}
                                </option>
                            @endforeach
                        </select>
                        @error('doctor_id')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-slate-700">Nama Perusahaan</label>
                        <input type="text" name="company_name" value="{{ old('company_name', $result->company->name ?? '') }}"
                               class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all @error('company_name') border-red-500 @enderror"
                               placeholder="Masukkan nama perusahaan">
                        @error('company_name')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-slate-700">Diagnosis</label>
                        <div class="flex gap-4">
                            <div class="flex-none w-1/4">
                                <input type="text" name="icd_code" value="{{ old('icd_code', $result->diagnosis->icd->code ?? '') }}"
                                       class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all @error('icd_code') border-red-500 @enderror"
                                       placeholder="Kode ICD">
                                @error('icd_code')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="flex-grow">
                                <input type="text" name="icd_title" value="{{ old('icd_title', $result->diagnosis->icd->title ?? '') }}"
                                       class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all @error('icd_title') border-red-500 @enderror"
                                       placeholder="Judul Diagnosis">
                                @error('icd_title')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <textarea name="description" placeholder="Deskripsi tambahan (opsional)" class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all @error('description') border-red-500 @enderror">{{ old('description', $result->diagnosis->description ?? '') }}</textarea>
                        @error('description')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-slate-700">Durasi (hari) <span class="text-red-500">*</span></label>
                            <input type="number" name="duration" value="{{ old('duration', $result->duration) }}"
                                   class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all @error('duration') border-red-500 @enderror"
                                   placeholder="Jumlah hari">
                            @error('duration')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-slate-700">Tanggal Mulai <span class="text-red-500">*</span></label>
                            <input type="date" name="start_date" value="{{ old('start_date', \Carbon\Carbon::parse($result->start_date)->format('Y-m-d')) }}"
                                   class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all @error('start_date') border-red-500 @enderror">
                            @error('start_date')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-slate-700">Tanggal Selesai <span class="text-red-500">*</span></label>
                            <input type="date" name="end_date" value="{{ old('end_date', \Carbon\Carbon::parse($result->end_date)->format('Y-m-d')) }}"
                                   class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all @error('end_date') border-red-500 @enderror">
                            @error('end_date')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-lg border border-slate-200 mb-6 overflow-hidden">
                <div class="bg-gradient-to-r from-green-50 to-emerald-50 px-6 py-4 border-b border-green-200">
                    <h2 class="text-lg font-semibold text-slate-800 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-green-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                        </svg>
                        Kontak & Identitas
                        <span class="ml-2 text-xs bg-green-100 text-green-600 px-2 py-1 rounded-full">Editable</span>
                    </h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-slate-700">NIK</label>
                            <input type="text" name="nik" value="{{ old('nik', $result->patient->nik) }}"
                                   class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all @error('nik') border-red-500 @enderror"
                                   placeholder="Masukkan NIK pasien">
                            @error('nik')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-slate-700">Nomor Telepon</label>
                            <input type="text" name="phone" value="{{ old('phone', $result->patient->phone) }}"
                                   class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all @error('phone') border-red-500 @enderror"
                                   placeholder="Masukkan nomor telepon">
                            @error('phone')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-slate-700">No Identitas</label>
                            <input type="text" name="identity" value="{{ old('identity', $result->patient->identity) }}"
                                   class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all @error('identity') border-red-500 @enderror"
                                   placeholder="Masukkan nomor identitas">
                            @error('identity')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-slate-700">Alamat</label>
                            <input type="text" name="address" value="{{ old('address', $result->patient->address) }}"
                                   class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all @error('address') border-red-500 @enderror"
                                   placeholder="Masukkan alamat lengkap">
                            @error('address')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-lg border border-slate-200 p-6">
                <div class="flex flex-col sm:flex-row gap-4 justify-between items-center">
                    <div class="text-sm text-slate-600">
                        <svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                        Hanya data yang ditandai "Editable" yang dapat diubah untuk menjaga integritas surat
                    </div>
                    <div class="flex gap-3">
                        <a href="{{ route('outlet.healthletter.index') }}"
                           class="px-6 py-3 bg-slate-100 text-slate-700 rounded-lg hover:bg-slate-200 transition-all font-medium flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                            </svg>
                            Kembali
                        </a>
                        <button type="submit" id="submit-btn"
                                class="relative px-8 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg hover:from-blue-700 hover:to-blue-800 transition-all font-semibold shadow-lg flex items-center">
                            <span id="submit-text">
                                <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                </svg>
                                Simpan Perubahan
                            </span>
                            <span id="submit-loading" class="hidden">
                                <svg class="animate-spin h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Menyimpan...
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Form submission with loading state
    const form = document.getElementById('edit-mc-form');
    const submitBtn = document.getElementById('submit-btn');
    const submitText = document.getElementById('submit-text');
    const submitLoading = document.getElementById('submit-loading');

    form?.addEventListener('submit', function() {
        submitBtn.disabled = true;
        submitText.classList.add('hidden');
        submitLoading.classList.remove('hidden');
    });

    // Auto-hide alerts after 5 seconds
    const alerts = document.querySelectorAll('[role="alert"]');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.transition = 'opacity 0.5s ease-out';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        }, 5000);
    });
});
</script>
@endpush
@endsection