@extends('layouts.app', ['header' => 'Buat Surat Keterangan Sakit'])

@section('content')
<div class="max-w-4xl mx-auto" x-data="formWizard()">

    <div class="text-center mb-8">
        <h1 class="text-3xl font-bold text-slate-800">Buat Surat Keterangan Sakit (MC)</h1>
        <p class="mt-2 text-base text-slate-500">Ikuti langkah-langkah di bawah untuk menerbitkan surat istirahat kerja.</p>
        <p class="text-sm text-slate-500">Outlet Aktif: <span class="font-semibold text-blue-600">{{ $outlet->name ?? 'N/A' }}</span></p>
    </div>

    <div class="bg-gradient-to-r from-red-50 to-orange-50 border border-slate-200 rounded-xl shadow-sm p-6 mb-8">
        <div class="relative">
            <div class="absolute top-1/2 left-0 w-full h-1 bg-slate-200 rounded-full" aria-hidden="true">
                <div class="absolute top-0 left-0 h-1 bg-gradient-to-r from-red-600 to-orange-600 rounded-full transition-all duration-700" :style="`width: ${progress}%`"></div>
            </div>
            <div class="relative flex justify-between">
                <template x-for="(step, index) in steps" :key="index">
                    <div class="flex flex-col items-center text-center w-1/3">
                        <button @click="goToStep(index + 1)" :disabled="index + 1 > maxStep"
                                class="w-12 h-12 rounded-full flex items-center justify-center transition-all duration-300 text-sm font-bold shadow-md transform hover:scale-110"
                                :class="{
                                    'bg-gradient-to-r from-red-600 to-orange-600 text-white': currentStep === index + 1,
                                    'bg-green-500 text-white': currentStep > index + 1,
                                    'bg-slate-100 border-2 border-slate-200 text-slate-400 cursor-not-allowed': currentStep < index + 1 && maxStep <= index,
                                    'bg-white border-2 border-slate-300 text-slate-500 hover:border-red-400': currentStep < index + 1 && maxStep > index
                                }">
                            <span x-show="currentStep <= index + 1" x-text="index + 1"></span>
                            <svg x-show="currentStep > index + 1" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                        </button>
                        <div class="mt-2 w-full px-1">
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
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" /></svg>
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

    <form method="POST" action="{{ route('outlet.results.mc.store') }}" id="mc-form">
        @csrf
        <input type="hidden" name="type" value="mc">

        <div class="bg-white border border-slate-200 rounded-xl shadow-lg p-8">
            <!-- Step 1: Data Pasien & Perusahaan -->
            <div x-show="currentStep === 1" class="space-y-6 animate-fade-in">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" /></svg>
                    </div>
                    <h3 class="text-lg font-semibold text-slate-800">Langkah 1: Data Pasien & Perusahaan</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="patient_name" class="block text-sm font-medium leading-6 text-slate-700 mb-1.5">Nama Pasien <span class="text-red-600">*</span></label>
                        <div class="relative">
                            <input type="text" name="patient_name" id="patient_name" autocomplete="off"
                                   class="w-full py-2.5 px-4 rounded-lg border-slate-300 shadow-sm focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all"
                                   placeholder="Cari pasien atau ketik nama baru..."
                                   value="{{ old('patient_name') }}">
                            <input type="hidden" name="patient_id" id="patient_id" value="{{ old('patient_id') }}">
                            <div id="patient-suggestions" class="absolute z-50 bg-white border border-slate-200 mt-1 rounded-lg shadow-xl max-h-60 overflow-y-auto w-full"></div>
                        </div>
                        @error('patient_name')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror

                        <div class="mt-2 flex items-center">
                            <input type="checkbox" name="is_new_patient" id="is_new_patient" class="h-4 w-4 rounded border-slate-300 text-red-600 focus:ring-red-600" @checked(!old('patient_id'))>
                            <label for="is_new_patient" class="ml-2 text-sm text-slate-600">Ini pasien baru</label>
                        </div>
                    </div>

                    <div>
                        <label for="company_search" class="block text-sm font-medium leading-6 text-slate-700 mb-1.5">Perusahaan (Opsional)</label>
                        <div class="flex gap-2">
                            <div class="relative flex-grow">
                                <input type="text" id="company_search" name="company_name"
                                       class="w-full py-2.5 px-4 rounded-lg border-slate-300 shadow-sm focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all"
                                       placeholder="Cari perusahaan..."
                                       value="{{ old('company_name') }}">
                                <input type="hidden" name="company_id" id="company_id" value="{{ old('company_id') }}">
                                <div id="company-suggestions" class="absolute z-50 bg-white border border-slate-200 mt-1 rounded-lg shadow-xl max-h-40 overflow-y-auto w-full text-sm"></div>
                            </div>
                            <button type="button" onclick="document.getElementById('modalCompany').showModal()"
                                    class="flex-shrink-0 px-4 py-2.5 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-all shadow-md"
                                    title="Tambah Perusahaan Baru">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                            </button>
                        </div>
                        @error('company_id')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="dob" class="block text-sm font-medium leading-6 text-slate-700 mb-1.5">Tanggal Lahir <span class="text-red-600">*</span></label>
                        <input type="date" name="dob" id="dob" value="{{ old('dob') }}" class="w-full px-4 py-2.5 rounded-lg border-slate-300 shadow-sm focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all @error('dob') border-red-500 @enderror">
                        @error('dob')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="gender" class="block text-sm font-medium leading-6 text-slate-700 mb-1.5">Jenis Kelamin <span class="text-red-600">*</span></label>
                        <select name="gender" id="gender" class="w-full px-4 py-2.5 rounded-lg border-slate-300 shadow-sm focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all @error('gender') border-red-500 @enderror">
                            <option value="">~ Pilih ~</option>
                            <option value="L" @selected(old('gender') == 'L')>Laki-laki</option>
                            <option value="P" @selected(old('gender') == 'P')>Perempuan</option>
                        </select>
                        @error('gender')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="nik" class="block text-sm font-medium leading-6 text-slate-700 mb-1.5">NIK KTP (Opsional)</label>
                        <input type="text" name="nik" id="nik" value="{{ old('nik') }}" class="w-full px-4 py-2.5 rounded-lg border-slate-300 shadow-sm focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all">
                    </div>

                    <div>
                        <label for="identity" class="block text-sm font-medium leading-6 text-slate-700 mb-1.5">No. Pegawai (Opsional)</label>
                        <input type="text" name="identity" id="identity" value="{{ old('identity') }}" class="w-full px-4 py-2.5 rounded-lg border-slate-300 shadow-sm focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all">
                    </div>

                    <div>
                        <label for="phone" class="block text-sm font-medium leading-6 text-slate-700 mb-1.5">Nomor Telepon (Opsional)</label>
                        <input type="text" name="phone" id="phone" value="{{ old('phone') }}" class="w-full px-4 py-2.5 rounded-lg border-slate-300 shadow-sm focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all">
                    </div>

                    <div class="md:col-span-2">
                        <label for="address" class="block text-sm font-medium leading-6 text-slate-700 mb-1.5">Alamat</label>
                        <textarea name="address" id="address" rows="3" class="w-full px-4 py-2.5 rounded-lg border-slate-300 shadow-sm focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all resize-none">{{ old('address') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Step 2: Detail Istirahat & Diagnosa -->
            <div x-show="currentStep === 2" x-cloak class="space-y-6 animate-fade-in">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-orange-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" /></svg>
                    </div>
                    <h3 class="text-lg font-semibold text-slate-800">Langkah 2: Detail Istirahat & Diagnosa</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="duration" class="block text-sm font-medium leading-6 text-slate-700 mb-1.5">Durasi (hari) <span class="text-red-600">*</span></label>
                        <input type="number" name="duration" id="duration" min="1" value="{{ old('duration', 1) }}" class="w-full px-4 py-2.5 rounded-lg border-slate-300 shadow-sm focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all @error('duration') border-red-500 @enderror">
                        @error('duration')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="start_date" class="block text-sm font-medium leading-6 text-slate-700 mb-1.5">Tanggal Mulai <span class="text-red-600">*</span></label>
                        <input type="date" name="start_date" id="start_date" value="{{ old('start_date', $todayDate ?? date('Y-m-d')) }}" class="w-full px-4 py-2.5 rounded-lg border-slate-300 shadow-sm focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all @error('start_date') border-red-500 @enderror">
                        @error('start_date')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="end_date" class="block text-sm font-medium leading-6 text-slate-700 mb-1.5">Tanggal Selesai</label>
                        <input type="date" name="end_date" id="end_date" value="{{ old('end_date') }}" class="w-full px-4 py-2.5 rounded-lg border-slate-300 bg-slate-100 shadow-sm cursor-not-allowed" readonly>
                    </div>

                    <div class="md:col-span-3">
                        <label for="icd_search" class="block text-sm font-medium leading-6 text-slate-700 mb-1.5">Diagnosis (ICD-10) <span class="text-red-600">*</span></label>
                        <div class="relative">
                            <input type="text" name="icd_name" id="icd_search" autocomplete="off"
                                   class="w-full py-2.5 px-4 rounded-lg border-slate-300 shadow-sm focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all @error('icd_master_id') border-red-500 @enderror"
                                   placeholder="Cari kode ICD atau nama diagnosis..."
                                   value="{{ old('icd_name') }}">
                            <input type="hidden" name="icd_master_id" id="icd_master_id" value="{{ old('icd_master_id') }}">
                            <div id="icd-suggestions" class="absolute z-50 bg-white border border-slate-200 mt-1 rounded-lg shadow-xl max-h-60 overflow-y-auto w-full"></div>
                        </div>
                        @error('icd_master_id')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror

                        <div id="selected-icd-info" class="mt-3 p-4 bg-orange-50 border border-orange-200 rounded-lg @if(!old('icd_master_id')) hidden @endif">
                             <p class="font-medium text-slate-800">Kode: <span id="icd-code-display">{{ old('icd_master_id') ? (explode(' - ', old('icd_name'))[0] ?? '') : '' }}</span></p>
                             <p class="text-sm text-slate-600 mt-1">Deskripsi: <span id="icd-title-display">{{ old('icd_master_id') ? (explode(' - ', old('icd_name'))[1] ?? old('icd_name')) : '' }}</span></p>
                        </div>
                    </div>

                    <div class="md:col-span-3">
                        <label for="diagnosis_description" class="block text-sm font-medium leading-6 text-slate-700 mb-1.5">Catatan Diagnosa (Opsional)</label>
                        <textarea name="diagnosis_description" id="diagnosis_description" rows="3" class="w-full px-4 py-2.5 rounded-lg border-slate-300 shadow-sm focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all resize-none" placeholder="Catatan tambahan dari dokter terkait diagnosa ini...">{{ old('diagnosis_description') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Step 3: Dokter & Opsi Final -->
            <div x-show="currentStep === 3" x-cloak class="space-y-6 animate-fade-in">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z" /></svg>
                    </div>
                    <h3 class="text-lg font-semibold text-slate-800">Langkah 3: Dokter & Opsi Final</h3>
                </div>

                <div x-data="{
                    searchQuery: '',
                    filteredDoctors: @js($doctors->toArray()),
                    allDoctors: @js($doctors->toArray()),
                    selectedDoctorId: @js(old('doctor_id', $doctors->first()?->id ?? '')),
                    
                    filterDoctors() {
                        if (!this.searchQuery.trim()) {
                            this.filteredDoctors = this.allDoctors;
                            return;
                        }
                        
                        const query = this.searchQuery.toLowerCase();
                        this.filteredDoctors = this.allDoctors.filter(doctor => 
                            doctor.user.name.toLowerCase().includes(query) ||
                            (doctor.speciality && doctor.speciality.toLowerCase().includes(query)) ||
                            (doctor.license_number && doctor.license_number.toLowerCase().includes(query))
                        );
                    },
                    
                    clearSearch() {
                        this.searchQuery = '';
                        this.filterDoctors();
                    },
                    
                    getSelectedDoctorName() {
                        const doctor = this.allDoctors.find(d => d.id == this.selectedDoctorId);
                        return doctor ? doctor.user.name + ' - ' + (doctor.speciality || 'Dokter Umum') : '';
                    }
                }" class="space-y-6">

                    <!-- Search Bar untuk Dokter -->
                    <div class="bg-gradient-to-r from-slate-50 to-green-50 rounded-xl border border-slate-200 p-6">
                        <label class="block text-sm font-medium text-slate-700 mb-3">
                            <svg class="w-5 h-5 inline mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 15.803a7.5 7.5 0 0010.607 0z" />
                            </svg>
                            Cari Dokter Pemeriksa
                        </label>
                        <div class="relative">
                            <input type="text" 
                                   x-model="searchQuery" 
                                   @input="filterDoctors()"
                                   placeholder="Ketik nama dokter, spesialis, atau nomor lisensi..."
                                   class="w-full pl-4 pr-10 py-3 border border-slate-300 rounded-lg shadow-sm focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all text-sm">
                            <button x-show="searchQuery" 
                                    @click="clearSearch()" 
                                    type="button" 
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                        
                        <!-- Search Stats -->
                        <div x-show="searchQuery" class="mt-3 flex items-center justify-between text-sm">
                            <span class="text-slate-600" x-text="`Ditemukan ${filteredDoctors.length} dari ${allDoctors.length} dokter`"></span>
                            <button @click="clearSearch()" type="button" class="text-green-600 hover:text-green-800 font-medium">
                                Tampilkan Semua
                            </button>
                        </div>
                    </div>

                    <!-- Doctor Selection -->
                    <fieldset>
                        <legend class="block text-sm font-medium leading-6 text-slate-700 mb-4">Pilih Dokter Pemeriksa</legend>
                        
                        <!-- No Results -->
                        <div x-show="filteredDoctors.length === 0 && searchQuery" 
                             class="text-center py-12 bg-white rounded-lg border-2 border-dashed border-slate-200">
                            <svg class="mx-auto h-12 w-12 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6-4h6m2 5.291A7.962 7.962 0 0118 12a8 8 0 01-8 8 8 8 0 01-8-8 8 8 0 018-8 7.962 7.962 0 015.291 2z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-slate-900">Tidak ada dokter ditemukan</h3>
                            <p class="mt-1 text-sm text-slate-500">Coba ubah kata kunci pencarian Anda.</p>
                            <button @click="clearSearch()" 
                                    type="button" 
                                    class="mt-4 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-green-700 bg-green-100 hover:bg-green-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                Reset Pencarian
                            </button>
                        </div>

                        <!-- Doctor Grid -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                            <template x-for="doctor in filteredDoctors" :key="doctor.id">
                                <label :for="`doctor_${doctor.id}`" 
                                       class="relative group cursor-pointer rounded-xl border-2 bg-white p-5 shadow-sm transition-all duration-300 hover:shadow-lg hover:-translate-y-1 focus:outline-none"
                                       :class="selectedDoctorId == doctor.id ? 'border-green-500 bg-green-50 shadow-lg ring-2 ring-green-200' : 'border-slate-200 hover:border-green-300'">
                                    
                                    <!-- Radio Input -->
                                    <input type="radio" 
                                           name="doctor_id" 
                                           :id="`doctor_${doctor.id}`" 
                                           :value="doctor.id" 
                                           class="sr-only"
                                           x-model="selectedDoctorId">
                                    
                                    <div class="flex items-start space-x-4">
                                        <!-- Avatar -->
                                        <div class="flex-shrink-0">
                                            <div class="w-14 h-14 rounded-full bg-gradient-to-br from-green-100 via-blue-100 to-purple-100 flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                                                <svg class="w-7 h-7 text-green-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                                </svg>
                                            </div>
                                        </div>

                                        <!-- Info -->
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-start justify-between">
                                                <div class="flex-1">
                                                    <h4 class="text-sm font-bold text-slate-900 leading-tight" x-text="doctor.user.name"></h4>
                                                    <p class="text-xs text-green-600 font-medium mt-1" x-text="doctor.speciality || 'Dokter Umum'"></p>
                                                    
                                                    <!-- License Badge -->
                                                    <div x-show="doctor.license_number" class="mt-2">
                                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 border border-blue-200">
                                                            <svg class="w-3 h-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                            </svg>
                                                            <span x-text="doctor.license_number"></span>
                                                        </span>
                                                    </div>
                                                </div>
                                                
                                                <!-- Selection Check -->
                                                <div x-show="selectedDoctorId == doctor.id" 
                                                     x-transition:enter="transition ease-out duration-300"
                                                     x-transition:enter-start="opacity-0 scale-50"
                                                     x-transition:enter-end="opacity-100 scale-100"
                                                     class="flex-shrink-0 ml-2">
                                                    <div class="w-6 h-6 bg-green-600 rounded-full flex items-center justify-center shadow-lg">
                                                        <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                                        </svg>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Hover Ring Effect -->
                                    <div class="absolute inset-0 rounded-xl transition-all duration-300 pointer-events-none"
                                         :class="selectedDoctorId == doctor.id ? 'ring-2 ring-green-400 ring-opacity-60' : 'group-hover:ring-2 group-hover:ring-green-300 group-hover:ring-opacity-40'"></div>
                                </label>
                            </template>
                        </div>

                        <!-- Selected Doctor Summary -->
                        <div x-show="selectedDoctorId" 
                             x-transition:enter="transition ease-out duration-300"
                             x-transition:enter-start="opacity-0 translate-y-4"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             class="mt-6 p-6 bg-gradient-to-r from-green-50 to-blue-50 border-2 border-green-200 rounded-xl">
                            <div class="flex items-center space-x-4">
                                <div class="flex-shrink-0">
                                    <div class="w-10 h-10 bg-green-600 rounded-full flex items-center justify-center">
                                        <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </div>
                                </div>
                                <div>
                                    <h3 class="text-base font-bold text-slate-900">Dokter Terpilih</h3>
                                    <p class="text-sm text-slate-700 font-medium" x-text="getSelectedDoctorName()"></p>
                                    <p class="text-xs text-slate-500 mt-1">Pastikan pilihan Anda sudah benar sebelum melanjutkan</p>
                                </div>
                            </div>
                        </div>

                        @error('doctor_id')
                            <p class="mt-2 text-sm text-red-600 font-medium">{{ $message }}</p>
                        @enderror
                    </fieldset>

                    <!-- Notification Options -->
                    <div class="bg-gradient-to-r from-slate-50 to-green-50 rounded-lg p-6">
                        <label class="block text-sm font-medium leading-6 text-slate-700 mb-4">Opsi Notifikasi (Opsional)</label>
                        <div class="space-y-4">
                            <div x-data="{ enabled: @json(old('send_notif_wa', false)) }">
                                <label class="flex items-center cursor-pointer">
                                    <input type="checkbox" name="send_notif_wa" x-model="enabled" class="h-5 w-5 rounded border-slate-300 text-green-600 focus:ring-green-600">
                                    <span class="ml-3 text-sm text-slate-700">Kirim Notifikasi via WhatsApp</span>
                                </label>
                                <div x-show="enabled" x-transition class="mt-3 ml-8">
                                    <label for="whatsapp_number" class="block text-sm font-medium leading-6 text-slate-700 mb-1.5">Nomor WhatsApp</label>
                                    <input type="text" name="whatsapp_number" id="whatsapp_number" value="{{ old('whatsapp_number') }}" class="w-full px-4 py-2.5 rounded-lg border-slate-300 shadow-sm focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all" placeholder="Contoh: 6281234567890">
                                    @error('whatsapp_number')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                                </div>
                            </div>

                            <div x-data="{ enabled: @json(old('send_notif_email', false)) }">
                                <label class="flex items-center cursor-pointer">
                                    <input type="checkbox" name="send_notif_email" x-model="enabled" class="h-5 w-5 rounded border-slate-300 text-blue-600 focus:ring-blue-600">
                                    <span class="ml-3 text-sm text-slate-700">Kirim Notifikasi via Email</span>
                                </label>
                                <div x-show="enabled" x-transition class="mt-3 ml-8">
                                    <label for="email_address" class="block text-sm font-medium leading-6 text-slate-700 mb-1.5">Alamat Email</label>
                                    <input type="email" name="email_address" id="email_address" value="{{ old('email_address') }}" class="w-full px-4 py-2.5 rounded-lg border-slate-300 shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all" placeholder="Contoh: pasien@email.com">
                                    @error('email_address')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Navigation Buttons -->
            <div class="mt-8 pt-5 border-t border-slate-200 flex justify-between items-center">
                <button type="button" @click="prevStep()" x-show="currentStep > 1" class="inline-flex items-center gap-2 px-5 py-2.5 bg-slate-200 text-slate-700 rounded-lg text-sm font-medium hover:bg-slate-300 transition-all">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" /></svg>
                    Kembali
                </button>
                <div x-show="currentStep === 1" class="w-full text-left">
                    <a href="{{ route('outlet.healthletter.index') }}" class="text-sm text-slate-600 hover:text-slate-900 transition hover:underline">Batal</a>
                </div>

                <button type="button" @click="nextStep()" x-show="currentStep < 3" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-red-600 to-orange-600 text-white rounded-lg text-sm font-semibold hover:from-red-700 hover:to-orange-700 transition-all shadow-lg">
                    Selanjutnya
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" /></svg>
                </button>

                <button type="submit" id="submit-btn" x-show="currentStep === 3" class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-gradient-to-r from-green-600 to-green-700 text-white font-bold rounded-lg shadow-lg hover:from-green-700 hover:to-green-800 transition-all transform hover:scale-105">
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

<!-- Company Modal -->
<dialog id="modalCompany" class="rounded-xl shadow-2xl p-0 max-w-md w-full backdrop:bg-slate-900/50">
    <div class="p-6">
        <form method="POST" action="{{ route('outlet.companies.store') }}" id="company-form">
            @csrf
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008z" /></svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-slate-800">Tambah Perusahaan Baru</h3>
                    <p class="text-sm text-slate-500">Perusahaan akan langsung tersedia</p>
                </div>
            </div>
            <div class="mb-6">
                <label for="modal_company_name" class="block text-sm font-medium text-slate-700 mb-1.5">Nama Perusahaan</label>
                <input type="text" name="name" id="modal_company_name" class="w-full px-4 py-2.5 rounded-lg border-slate-300 shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all" required>
                <p id="company-error" class="text-sm text-red-600 mt-1 hidden"></p>
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('modalCompany').close()" class="px-4 py-2 bg-slate-100 text-slate-700 rounded-lg text-sm font-medium hover:bg-slate-200 transition-all">
                    Batal
                </button>
                <button type="submit" id="save-company-btn" class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg text-sm font-semibold hover:from-blue-700 hover:to-blue-800 transition-all shadow">
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
    /* Enhanced animations and styles for MC */
    @keyframes fadeIn {
        from { 
            opacity: 0; 
            transform: translateY(10px); 
        }
        to { 
            opacity: 1; 
            transform: translateY(0); 
        }
    }
    
    @keyframes slideInRight {
        from {
            opacity: 0;
            transform: translateX(20px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }
    
    @keyframes pulse {
        0%, 100% {
            opacity: 1;
        }
        50% {
            opacity: 0.8;
        }
    }
    
    .animate-fade-in {
        animation: fadeIn 0.3s ease-out;
    }
    
    .animate-slide-in-right {
        animation: slideInRight 0.3s ease-out;
    }
    
    .animate-pulse-soft {
        animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }
    
    /* Doctor card enhancements for MC */
    .doctor-card {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }
    
    .doctor-card:hover {
        transform: translateY(-4px) scale(1.02);
        box-shadow: 
            0 20px 25px -5px rgba(0, 0, 0, 0.1),
            0 10px 10px -5px rgba(0, 0, 0, 0.04),
            0 0 0 1px rgba(34, 197, 94, 0.1);
    }
    
    .doctor-card.selected {
        transform: translateY(-2px);
        box-shadow: 
            0 25px 50px -12px rgba(34, 197, 94, 0.25),
            0 0 0 1px rgba(34, 197, 94, 0.2);
        background: linear-gradient(135deg, rgba(34, 197, 94, 0.05) 0%, rgba(59, 130, 246, 0.05) 100%);
    }
    
    /* Search input styling for MC */
    .search-input:focus {
        transform: translateY(-1px);
        box-shadow: 
            0 10px 25px -5px rgba(34, 197, 94, 0.1),
            0 0 0 3px rgba(34, 197, 94, 0.1);
    }
    
    /* Date calculation enhancement */
    #end_date {
        background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
    }
    
    /* Responsive improvements */
    @media (max-width: 640px) {
        .doctor-card:hover {
            transform: translateY(-2px) scale(1.01);
        }
    }
    
    /* Focus states for accessibility */
    .doctor-card:focus-within {
        outline: 2px solid #10b981;
        outline-offset: 2px;
    }
    
    /* Reduced motion support */
    @media (prefers-reduced-motion: reduce) {
        .doctor-card {
            transition: none;
            animation: none;
        }
        
        .doctor-card:hover {
            transform: none;
        }
    }
    
    [x-cloak] { 
        display: none !important; 
    }
</style>
@endsection

@push('scripts')
@include('outlets.results._scriptmc')
@endpush