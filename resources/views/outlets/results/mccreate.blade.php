@extends('layouts.app', ['header' => 'Buat Surat Keterangan Sakit'])

@section('content')
<div class="max-w-4xl mx-auto" x-data="formWizard()">

    <div class="text-center mb-8">
        <h1 class="text-3xl font-bold text-slate-800">Buat Surat Keterangan Sakit (MC)</h1>
        <p class="mt-2 text-base text-slate-500">Ikuti langkah-langkah di bawah untuk menerbitkan surat istirahat kerja.</p>
        <p class="text-sm text-slate-500">Outlet Aktif: <span class="font-semibold text-blue-600">{{ $outlet->name ?? 'N/A' }}</span></p>
    </div>

    <div class="border border-slate-200 bg-white rounded-xl shadow-sm p-4 mb-8">
        <div class="relative">
            <div class="absolute top-1/2 left-0 w-full h-0.5 bg-slate-200" aria-hidden="true">
                <div class="absolute top-0 left-0 h-0.5 bg-blue-600 transition-all duration-500" :style="`width: ${progress}%`"></div>
            </div>
            <div class="relative flex justify-between">
                <template x-for="(step, index) in steps" :key="index">
                    <div class="flex flex-col items-center text-center w-1/3">
                        <button @click="goToStep(index + 1)" :disabled="index + 1 > maxStep" class="w-8 h-8 rounded-full flex items-center justify-center transition-colors duration-300 text-sm font-bold" :class="{ 'bg-blue-600 text-white': currentStep === index + 1, 'bg-white border-2 border-blue-600 text-blue-600': currentStep > index + 1, 'bg-slate-100 border-2 border-slate-200 text-slate-400 cursor-not-allowed': currentStep < index + 1 && maxStep <= index, 'bg-white border-2 border-slate-300 text-slate-500': currentStep < index + 1 && maxStep > index }">
                            <span x-show="currentStep <= index + 1" x-text="index + 1"></span>
                            <svg x-show="currentStep > index + 1" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                        </button>
                        <span class="text-xs mt-2 w-24 truncate" :class="currentStep >= index + 1 ? 'text-slate-700 font-semibold' : 'text-slate-500'" x-text="step"></span>
                    </div>
                </template>
            </div>
        </div>
    </div>
    
    @if ($errors->any())
        <div class="mb-6 bg-red-50 border border-red-200 text-sm text-red-700 rounded-lg p-4" role="alert">
            <div class="flex">
                <div class="flex-shrink-0"><svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" /></svg></div>
                <div class="ml-3">
                    <h3 class="font-semibold">Terdapat kesalahan pada input Anda. Silakan periksa kembali.</h3>
                </div>
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('outlet.results.store.mc') }}" id="mc-form">
        @csrf
        <input type="hidden" name="type" value="mc">
        
        <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-8">
            <div x-show="currentStep === 1" class="space-y-6 animate-fade-in">
                <h3 class="text-lg font-semibold text-slate-800 border-b border-slate-200 pb-3 mb-6">Langkah 1: Data Pasien & Perusahaan</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="patient_name" class="block text-sm font-medium leading-6 text-slate-700 mb-1.5">Nama Pasien <span class="text-red-600">*</span></label>
                        <div class="relative" x-data="{ open: false }" @click.away="open = false">
                            <input type="text" name="patient_name" id="patient_name" autocomplete="off" class="w-full rounded-md border-slate-300 shadow-sm" placeholder="Cari atau ketik baru..." value="{{ old('patient_name') }}" @focus="open = true" @input.debounce.300ms="searchPatients($event.target.value)">
                            <input type="hidden" name="patient_id" id="patient_id" value="{{ old('patient_id') }}">
                            <div id="suggestions" class="absolute z-50 bg-white border mt-1 rounded-md shadow-lg hidden max-h-60 overflow-y-auto w-full" x-show="open" x-cloak></div>
                        </div>
                        @error('patient_name')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                         <div class="mt-2">
                            <label class="inline-flex items-center">
                                <input type="checkbox" name="is_new_patient" id="is_new_patient" class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-600" @checked(!old('patient_id'))>
                                <span class="ml-2 text-sm text-slate-600">Ini pasien baru</span>
                            </label>
                        </div>
                    </div>
                    <div>
                        <label for="company_search" class="block text-sm font-medium leading-6 text-slate-700 mb-1.5">Perusahaan (Opsional)</label>
                        <div class="flex gap-2">
                            <div class="relative flex-grow" x-data="{ open: false }" @click.away="open = false">
                                <input type="text" id="company_search" name="company_name" class="w-full rounded-md border-slate-300 shadow-sm" placeholder="Cari perusahaan..." value="{{ old('company_name') }}" @focus="open = true" @input.debounce.300ms="searchCompanies($event.target.value)">
                                <input type="hidden" name="company_id" id="company_id" value="{{ old('company_id') }}">
                                <div id="company_suggestions" class="absolute z-50 bg-white border mt-1 rounded shadow-md hidden max-h-40 overflow-y-auto w-full text-sm" x-show="open" x-cloak></div>
                            </div>
                            <button type="button" onclick="document.getElementById('modalCompany').showModal()" class="px-3 py-2 bg-green-100 text-green-700 rounded-md hover:bg-green-200 transition" title="Tambah Perusahaan Baru">+</button>
                        </div>
                    </div>
                     <div class="md:col-span-2 grid grid-cols-2 gap-6">
                        <div>
                            <label for="dob" class="block text-sm font-medium leading-6 text-slate-700 mb-1.5">Tanggal Lahir <span class="text-red-600">*</span></label>
                            <input type="date" name="dob" id="dob" value="{{ old('dob') }}" class="w-full rounded-md border-slate-300 shadow-sm @error('dob') border-red-500 @enderror">
                            @error('dob')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="gender" class="block text-sm font-medium leading-6 text-slate-700 mb-1.5">Jenis Kelamin <span class="text-red-600">*</span></label>
                            <select name="gender" id="gender" class="w-full rounded-md border-slate-300 shadow-sm @error('gender') border-red-500 @enderror">
                                <option value="">~ Pilih ~</option>
                                <option value="L" @selected(old('gender') == 'L')>Laki-laki</option>
                                <option value="P" @selected(old('gender') == 'P')>Perempuan</option>
                            </select>
                            @error('gender')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>
            </div>

            <div x-show="currentStep === 2" x-cloak class="space-y-6 animate-fade-in">
                <h3 class="text-lg font-semibold text-slate-800 border-b border-slate-200 pb-3 mb-6">Langkah 2: Detail Istirahat & Diagnosa</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="md:col-span-1">
                        <label for="duration" class="block text-sm font-medium leading-6 text-slate-700 mb-1.5">Durasi (hari) <span class="text-red-600">*</span></label>
                        <input type="number" name="duration" id="duration" min="1" value="{{ old('duration', 1) }}" class="w-full rounded-md border-slate-300 shadow-sm @error('duration') border-red-500 @enderror">
                        @error('duration')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div class="md:col-span-1">
                        <label for="start_date" class="block text-sm font-medium leading-6 text-slate-700 mb-1.5">Tanggal Mulai <span class="text-red-600">*</span></label>
                        <input type="date" name="start_date" id="start_date" value="{{ old('start_date', $todayDate) }}" class="w-full rounded-md border-slate-300 shadow-sm @error('start_date') border-red-500 @enderror">
                        @error('start_date')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div class="md:col-span-1">
                        <label for="end_date" class="block text-sm font-medium leading-6 text-slate-700 mb-1.5">Tanggal Selesai</label>
                        <input type="date" name="end_date" id="end_date" value="{{ old('end_date') }}" class="w-full rounded-md border-slate-300 bg-slate-100 shadow-sm cursor-not-allowed" readonly>
                    </div>
                     <div class="md:col-span-3">
                        <label for="icd_search" class="block text-sm font-medium leading-6 text-slate-700 mb-1.5">Diagnosis (ICD-10) <span class="text-red-600">*</span></label>
                        <div class="relative" x-data="{ open: false }" @click.away="open = false">
                            <input type="text" name="icd_name" id="icd_search" autocomplete="off" class="w-full rounded-md border-slate-300 shadow-sm @error('icd_master_id') border-red-500 @enderror" placeholder="Cari kode atau nama diagnosa..." value="{{ old('icd_name') }}" @focus="open = true" @input.debounce.300ms="searchIcd($event.target.value)">
                            <input type="hidden" name="icd_master_id" id="icd_master_id" value="{{ old('icd_master_id') }}">
                            <div id="icd_suggestions" class="absolute z-50 bg-white border mt-1 rounded-md shadow-lg hidden max-h-60 overflow-y-auto w-full" x-show="open" x-cloak></div>
                        </div>
                        @error('icd_master_id')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div class="md:col-span-3">
                        <label for="diagnosis_description" class="block text-sm font-medium leading-6 text-slate-700 mb-1.5">Catatan Diagnosa (Opsional)</label>
                        <textarea name="diagnosis_description" id="diagnosis_description" rows="3" class="w-full rounded-md border-slate-300 shadow-sm" placeholder="Catatan tambahan dari dokter terkait diagnosa ini...">{{ old('diagnosis_description') }}</textarea>
                    </div>
                </div>
            </div>

           <div x-show="currentStep === 3" x-cloak class="space-y-6 animate-fade-in">
            <h3 class="text-lg font-semibold text-slate-800 border-b border-slate-200 pb-3 mb-6">Langkah 3: Dokter & Opsi Final</h3>
            
            <div>
                <label for="doctor_search" class="block text-sm font-medium leading-6 text-slate-700 mb-1.5">Pilih Dokter Pemeriksa <span class="text-red-600">*</span></label>
                
                <div class="relative">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" /></svg>
                    </div>
                    <input type="text" id="doctor_search" autocomplete="off" 
                        class="w-full rounded-md border-slate-300 shadow-sm pl-10" 
                        placeholder="Ketik nama dokter untuk mencari...">
                </div>
                <input type="hidden" name="doctor_id" id="doctor_id" value="{{ old('doctor_id') }}">
                @error('doctor_id')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>

            <div id="doctor-grid" class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                {{-- Konten dokter akan dimuat di sini oleh JavaScript --}}
                {{-- Placeholder saat loading --}}
                @foreach(range(1, 6) as $i)
                <div class="animate-pulse flex flex-col items-center space-y-2 p-4 border border-slate-200 rounded-lg">
                    <div class="w-16 h-16 bg-slate-200 rounded-full"></div>
                    <div class="h-3 bg-slate-200 rounded w-2/3"></div>
                    <div class="h-2 bg-slate-200 rounded w-1/2"></div>
                </div>
                @endforeach
            </div>
            
            <div id="doctor-pagination" class="mt-4">
                {{-- Konten pagination akan dimuat di sini oleh JavaScript --}}
            </div>

            <div class="pt-6 border-t border-slate-200">
                <label class="block text-sm font-medium leading-6 text-slate-700 mb-2">Opsi Notifikasi (Opsional)</label>
                <div class="space-y-3">
                    <label class="flex items-center"><input type="checkbox" name="send_notif_wa" value="1" @checked(old('send_notif_wa')) class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-600"><span class="ml-3 text-sm text-slate-600">Kirim Notifikasi via WhatsApp</span></label>
                    <label class="flex items-center"><input type="checkbox" name="send_notif_email" value="1" @checked(old('send_notif_email')) class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-600"><span class="ml-3 text-sm text-slate-600">Kirim Notifikasi via Email</span></label>
                </div>
            </div>
        </div>
            
            <div class="mt-8 pt-5 border-t border-slate-200 flex justify-between items-center">
                <button type="button" @click="prevStep()" x-show="currentStep > 1" x-cloak class="px-5 py-2 bg-slate-200 text-slate-700 rounded-lg text-sm font-medium hover:bg-slate-300 transition">&larr; Kembali</button>
                <div x-show="currentStep === 1" class="w-full text-left"><a href="{{ route('outlet.healthletter.index') }}" class="text-sm text-slate-600 hover:text-slate-900 transition hover:underline">Batal</a></div>
                <button type="button" @click="nextStep()" x-show="currentStep < 3" x-cloak class="px-5 py-2 bg-blue-600 text-white rounded-lg text-sm font-semibold hover:bg-blue-700 transition">Selanjutnya &rarr;</button>
                <button type="submit" id="submitBtn" x-show="currentStep === 3" x-cloak class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-green-600 text-white font-bold rounded-lg shadow-md hover:bg-green-700 transition-all">
                    <span id="btnText" class="inline-flex items-center gap-2"><svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>Simpan & Proses Surat</span>
                    <span id="btnLoading" class="hidden"><svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg> <span>Memproses...</span></span>
                </button>
            </div>
        </div>
    </form>
</div>

<dialog id="modalCompany" class="rounded-lg shadow-xl p-0 max-w-md w-full backdrop:bg-slate-900/50">
    <div class="p-6">
        <form method="POST" action="{{ route('outlet.companies.store') }}" id="companyForm">
            @csrf
            <h3 class="text-lg font-bold text-slate-800 mb-1">Tambah Perusahaan Baru</h3>
            <p class="text-sm text-slate-500 mb-4">Perusahaan yang ditambahkan akan langsung tersedia.</p>
            <div class="mb-4">
                <label for="modal_company_name" class="block text-sm font-medium text-slate-700">Nama Perusahaan</label>
                <input type="text" name="name" id="modal_company_name" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm" required>
                <p id="company_modal_error" class="text-sm text-red-600 mt-1 hidden"></p>
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('modalCompany').close()" class="px-4 py-2 bg-slate-100 text-slate-700 rounded-md text-sm font-medium hover:bg-slate-200">Batal</button>
                <button type="submit" id="saveCompanyBtn" class="px-4 py-2 bg-blue-600 text-white rounded-md text-sm font-semibold hover:bg-blue-700 w-24 text-center">
                    <span id="saveCompanyBtnText">Simpan</span>
                    <span id="saveCompanyBtnLoading" class="hidden"><svg class="animate-spin h-4 w-4 text-white mx-auto" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg></span>
                </button>
            </div>
        </form>
    </div>
</dialog>
@endsection

@push('scripts')
@include('outlets.results._scriptmc')
@endpush