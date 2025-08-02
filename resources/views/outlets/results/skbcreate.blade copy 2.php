@extends('layouts.app', ['header' => 'Buat Surat Keterangan Berobat'])

@section('content')
<div class="max-w-4xl mx-auto" x-data="formWizard()">

    <div class="text-center mb-8">
        <h1 class="text-3xl font-bold text-slate-800">Buat Surat Keterangan Berobat (SKB)</h1>
        <p class="mt-2 text-base text-slate-500">Ikuti langkah-langkah di bawah untuk menerbitkan surat baru.</p>
        <p class="text-sm text-slate-500">Outlet Aktif: <span class="font-semibold text-blue-600">{{ $outlet->name ?? 'N/A' }}</span></p>
    </div>

    <div class="bg-gradient-to-r from-blue-50 to-purple-50 border border-slate-200 rounded-xl shadow-sm p-6 mb-8">
        <div class="relative">
            <div class="absolute top-1/2 left-0 w-full h-1 bg-slate-200 rounded-full" aria-hidden="true">
                <div class="absolute top-0 left-0 h-1 bg-gradient-to-r from-blue-600 to-purple-600 rounded-full transition-all duration-700" :style="`width: ${progress}%`"></div>
            </div>
            <div class="relative flex justify-between">
                <template x-for="(step, index) in steps" :key="index">
                    <div class="flex flex-col items-center text-center w-1/3">
                        <button @click="goToStep(index + 1)" :disabled="index + 1 > maxStep"
                                class="w-12 h-12 rounded-full flex items-center justify-center transition-all duration-300 text-sm font-bold shadow-md transform hover:scale-110"
                                :class="{
                                    'bg-gradient-to-r from-blue-600 to-purple-600 text-white': currentStep === index + 1,
                                    'bg-green-500 text-white': currentStep > index + 1,
                                    'bg-slate-100 border-2 border-slate-200 text-slate-400 cursor-not-allowed': currentStep < index + 1 && maxStep <= index,
                                    'bg-white border-2 border-slate-300 text-slate-500 hover:border-blue-400': currentStep < index + 1 && maxStep > index
                                }">
                            <span x-show="currentStep <= index + 1" x-text="index + 1"></span>
                            <svg x-show="currentStep > index + 1" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                        </button>
                        {{-- FIXED: Menampilkan title dan subtitle dari objek step --}}
                        <div class="mt-2 w-full">
                           <p class="text-xs font-semibold" :class="currentStep >= index + 1 ? 'text-slate-700' : 'text-slate-500'" x-text="step.title"></p>
                           <p class="text-xs text-slate-400" x-text="step.subtitle"></p>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    @if ($errors->any())
        <div class="mb-6 bg-red-50 border border-red-200 text-sm text-red-700 rounded-lg p-4" role="alert">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="font-semibold">Terdapat kesalahan pada input Anda</h3>
                    @foreach ($errors->all() as $error)
                        <p class="mt-1 text-xs">{{ $error }}</p>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('outlet.results.store') }}" id="skb-form">
        @csrf
        <input type="hidden" name="type" value="skb">

        <div class="bg-white border border-slate-200 rounded-xl shadow-lg p-8">
            <div x-show="currentStep === 1" class="space-y-6 animate-fade-in">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                        {{-- Icon Step 1 --}}
                        <svg class="w-6 h-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" /></svg>
                    </div>
                    <h3 class="text-lg font-semibold text-slate-800">Langkah 1: Pasien & Kunjungan</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="patient_name" class="block text-sm font-medium leading-6 text-slate-700 mb-1.5">Nama Pasien</label>
                        {{-- FIXED: Mengganti id="suggestions" menjadi "patient-suggestions" untuk konsistensi --}}
                        <div class="relative" @click.away="document.getElementById('patient-suggestions').innerHTML=''">
                            <input type="text" name="patient_name" id="patient_name" autocomplete="off"
                                   class="w-full pl-4 pr-4 py-2.5 rounded-lg border-slate-300 shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                                   placeholder="Cari pasien atau ketik nama baru..."
                                   value="{{ old('patient_name') }}">
                            <input type="hidden" name="patient_id" id="patient_id" value="{{ old('patient_id') }}">

                            <div id="patient-suggestions" class="absolute z-50 bg-white border border-slate-200 mt-1 rounded-lg shadow-xl max-h-60 overflow-y-auto w-full">
                                </div>
                        </div>
                        @error('patient_name')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror

                        <div class="mt-2 flex items-center">
                            <input type="checkbox" name="is_new_patient" id="is_new_patient" class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-600" @checked(old('is_new_patient', true))>
                            <label for="is_new_patient" class="ml-2 text-sm text-slate-600">Daftarkan sebagai pasien baru</label>
                        </div>
                    </div>

                    <div>
                        <label for="company_search" class="block text-sm font-medium leading-6 text-slate-700 mb-1.5">Perusahaan</label>
                        <div class="flex gap-2">
                            <div class="relative flex-grow" @click.away="document.getElementById('company-suggestions').innerHTML=''">
                                <input type="text" id="company_search" name="company_name"
                                       class="w-full pl-4 pr-4 py-2.5 rounded-lg border-slate-300 shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                                       placeholder="Cari perusahaan..."
                                       value="{{ old('company_name') }}">
                                <input type="hidden" name="company_id" id="company_id" value="{{ old('company_id') }}">
                                <div id="company-suggestions" class="absolute z-50 bg-white border border-slate-200 mt-1 rounded-lg shadow-xl max-h-40 overflow-y-auto w-full text-sm"></div>
                            </div>
                            <button type="button" onclick="document.getElementById('modalCompany').showModal()"
                                    class="px-4 py-2.5 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-all shadow-md"
                                    title="Tambah Perusahaan Baru">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                            </button>
                        </div>
                        @error('company_id')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                        @error('company_name')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>

                    {{-- Fields for Date and Time --}}
                    <div>
                        <label for="date" class="block text-sm font-medium leading-6 text-slate-700 mb-1.5">Tanggal Berobat</label>
                        <input type="date" name="date" id="date" class="w-full px-4 py-2.5 rounded-lg border-slate-300 shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all" value="{{ old('date', date('Y-m-d')) }}">
                        @error('date')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="time" class="block text-sm font-medium leading-6 text-slate-700 mb-1.5">Jam Berobat</label>
                        <input type="time" name="time" id="time" class="w-full px-4 py-2.5 rounded-lg border-slate-300 shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all" value="{{ old('time', date('H:i')) }}">
                        @error('time')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            <div x-show="currentStep === 2" x-cloak class="space-y-6 animate-fade-in">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                        {{-- Icon Step 2 --}}
                        <svg class="w-6 h-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" /></svg>
                    </div>
                    <h3 class="text-lg font-semibold text-slate-800">Langkah 2: Detail Pasien & Pemeriksaan</h3>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- All patient detail fields --}}
                    <div>
                        <label for="dob" class="block text-sm font-medium leading-6 text-slate-700 mb-1.5">Tanggal Lahir</label>
                        <input type="date" name="dob" id="dob" value="{{ old('dob') }}" class="w-full px-4 py-2.5 rounded-lg border-slate-300 shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                        @error('dob')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="gender" class="block text-sm font-medium leading-6 text-slate-700 mb-1.5">Jenis Kelamin</label>
                        <select name="gender" id="gender" class="w-full px-4 py-2.5 rounded-lg border-slate-300 shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                            <option value="">~ Pilih ~</option>
                            <option value="L" @selected(old('gender') == 'L')>Laki-laki</option>
                            <option value="P" @selected(old('gender') == 'P')>Perempuan</option>
                        </select>
                        @error('gender')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                    
                    {{-- ... other patient fields (NIK, Identity, Phone, Address) ... --}}

                    <div class="md:col-span-2">
                        <label for="icd_search" class="block text-sm font-medium leading-6 text-slate-700 mb-1.5">Diagnosis (ICD-10)</label>
                        <div class="relative" @click.away="document.getElementById('icd-suggestions').innerHTML=''">
                            <input type="text" name="diagnosis_name" id="icd_search" autocomplete="off"
                                   class="w-full pl-4 pr-4 py-2.5 rounded-lg border-slate-300 shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                                   placeholder="Cari kode ICD atau nama diagnosis..."
                                   value="{{ old('diagnosis_name') }}">
                            <input type="hidden" name="icd_master_id" id="icd_master_id" value="{{ old('icd_master_id') }}">
                            <div id="icd-suggestions" class="absolute z-50 bg-white border border-slate-200 mt-1 rounded-lg shadow-xl max-h-60 overflow-y-auto w-full"></div>
                        </div>

                        {{-- Display for selected ICD --}}
                        <div id="selected-icd-info" class="mt-3 p-4 bg-blue-50 border border-blue-200 rounded-lg @if(!old('icd_master_id')) hidden @endif">
                            {{-- Content will be filled by JavaScript. This part is for re-population on validation error --}}
                             <p><strong>Kode:</strong> <span id="icd-code-display">{{ old('icd_master_id') ? explode(' - ', old('diagnosis_name'))[0] : '' }}</span></p>
                             <p><strong>Deskripsi:</strong> <span id="icd-title-display">{{ old('icd_master_id') ? (explode(' - ', old('diagnosis_name'))[1] ?? '') : '' }}</span></p>
                        </div>
                        @error('icd_master_id')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>

                    {{-- ... Notes Field ... --}}
                </div>
            </div>

            <div x-show="currentStep === 3" x-cloak class="space-y-6 animate-fade-in">
                 <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                        {{-- Icon Step 3 --}}
                        <svg class="w-6 h-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z" /></svg>
                    </div>
                    <h3 class="text-lg font-semibold text-slate-800">Langkah 3: Dokter & Opsi Final</h3>
                </div>
                
                {{-- ... Doctor and Notification fields ... --}}
            </div>

            <div class="mt-8 pt-5 border-t border-slate-200 flex justify-between items-center">
                <button type="button" @click="prevStep()" x-show="currentStep > 1"
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-slate-200 text-slate-700 rounded-lg text-sm font-medium hover:bg-slate-300 transition-all">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" /></svg>
                    Kembali
                </button>
                <div x-show="currentStep === 1" class="w-full text-left">
                    <a href="{{ route('outlet.healthletter.index') }}" class="text-sm text-slate-600 hover:text-slate-900 transition hover:underline">Batal</a>
                </div>

                <button type="button" @click="nextStep()" x-show="currentStep < 3"
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg text-sm font-semibold hover:from-blue-700 hover:to-blue-800 transition-all shadow-lg">
                    Selanjutnya
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" /></svg>
                </button>

                <button type="submit" id="submit-btn" x-show="currentStep === 3"
                        class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-gradient-to-r from-green-600 to-green-700 text-white font-bold rounded-lg shadow-lg hover:from-green-700 hover:to-green-800 transition-all transform hover:scale-105">
                    <span id="submit-text" class="inline-flex items-center gap-2">
                         <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        Simpan & Proses Surat
                    </span>
                    <div id="submit-loading" class="hidden inline-flex items-center gap-2">
                        <svg class="animate-spin h-5 w-5 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        Processing...
                    </div>
                </button>
            </div>
        </div>
    </form>
</div>

<dialog id="modalCompany" class="rounded-xl shadow-2xl p-0 max-w-md w-full backdrop:bg-slate-900/50">
    <div class="p-6">
        <form method="POST" action="{{ route('outlet.companies.store') }}" id="company-form">
            @csrf
            {{-- ... Modal content ... --}}
            <div class="mb-6">
                <label for="modal_company_name" class="block text-sm font-medium text-slate-700 mb-1.5">Nama Perusahaan</label>
                <input type="text" name="name" id="modal_company_name" class="w-full px-4 py-2.5 rounded-lg border-slate-300 shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all" required>
                {{-- FIXED: Mengganti id="company_modal_error" menjadi "company-error" --}}
                <p id="company-error" class="text-sm text-red-600 mt-1 hidden"></p>
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('modalCompany').close()"
                        class="px-4 py-2 bg-slate-100 text-slate-700 rounded-lg text-sm font-medium hover:bg-slate-200 transition-all">
                    Batal
                </button>
                <button type="submit" id="save-company-btn"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg text-sm font-semibold hover:from-blue-700 hover:to-blue-800 transition-all shadow">
                    <span id="save-company-text">Simpan</span>
                    <div id="save-company-loading" class="hidden">
                        <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    </div>
                </button>
            </div>
        </form>
    </div>
</dialog>

<style>
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in {
        animation: fadeIn 0.3s ease-out;
    }
    [x-cloak] { display: none !important; }
</style>
@endsection

@push('scripts')
{{-- Pastikan file _scriptskb.blade.php sudah menggunakan ID yang telah diperbaiki --}}
@include('outlets.results._scriptskb')
@endpush