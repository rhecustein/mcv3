@extends('layouts.app', ['header' => 'Buat Surat Keterangan Berobat'])

@section('content')
<div class="max-w-5xl mx-auto" x-data="formWizard()">
    <!-- Header Section -->
    <div class="bg-gradient-to-br from-blue-600 via-blue-700 to-purple-700 rounded-2xl p-8 mb-8 text-white shadow-2xl relative overflow-hidden">
        <div class="absolute top-0 right-0 w-64 h-64 bg-white opacity-5 rounded-full -mr-32 -mt-32"></div>
        <div class="absolute bottom-0 left-0 w-48 h-48 bg-white opacity-5 rounded-full -ml-24 -mb-24"></div>
        
        <div class="relative z-10">
            <div class="flex items-center gap-4 mb-4">
                <div class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center">
                    <svg class="w-10 h-10" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        <path d="M13 2v6h6"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold mb-1">Buat Surat Keterangan Berobat (SKB)</h1>
                    <p class="text-blue-100">Isi formulir dengan lengkap untuk membuat surat kesehatan</p>
                </div>
            </div>
            <div class="flex items-center gap-2 text-sm">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                <span>Outlet: <strong>{{ $outlet->name ?? 'Klinik Utama' }}</strong></span>
            </div>
        </div>
    </div>

    <!-- Progress Steps -->
    <div class="bg-white rounded-2xl shadow-lg p-6 mb-6 border border-gray-100">
        <div class="relative">
            <!-- Progress Bar Background -->
            <div class="absolute top-1/2 left-0 w-full h-1 bg-gray-200 rounded-full -translate-y-1/2"></div>
            <!-- Progress Bar Fill -->
            <div class="absolute top-1/2 left-0 h-1 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full transition-all duration-700 ease-out -translate-y-1/2" 
                 :style="`width: ${progress}%`"></div>
            
            <!-- Steps -->
            <div class="relative flex justify-between">
                <template x-for="(step, index) in steps" :key="index">
                    <div class="flex flex-col items-center group cursor-pointer" @click="goToStep(index + 1)">
                        <!-- Step Circle -->
                        <div class="relative">
                            <button :disabled="index + 1 > maxStep"
                                    class="w-14 h-14 rounded-2xl flex items-center justify-center transition-all duration-300 transform group-hover:scale-110"
                                    :class="{
                                        'bg-gradient-to-br from-blue-500 to-purple-600 text-white shadow-lg shadow-blue-500/30': currentStep === index + 1,
                                        'bg-green-500 text-white shadow-lg shadow-green-500/30': currentStep > index + 1,
                                        'bg-gray-100 text-gray-400 cursor-not-allowed': currentStep < index + 1 && maxStep <= index,
                                        'bg-white border-2 border-gray-300 text-gray-600 hover:border-blue-400': currentStep < index + 1 && maxStep > index
                                    }">
                                <span x-show="currentStep <= index + 1" class="font-bold text-lg" x-text="index + 1"></span>
                                <svg x-show="currentStep > index + 1" class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                </svg>
                            </button>
                            <!-- Pulse Animation for Active Step -->
                            <div x-show="currentStep === index + 1" 
                                 class="absolute inset-0 rounded-2xl bg-blue-500/20 animate-ping"></div>
                        </div>
                        
                        <!-- Step Label -->
                        <div class="mt-3 text-center">
                            <span class="text-sm font-semibold block" 
                                  :class="currentStep >= index + 1 ? 'text-gray-800' : 'text-gray-500'" 
                                  x-text="step.title"></span>
                            <span class="text-xs text-gray-500 mt-1 block" x-text="step.subtitle"></span>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    <!-- Error Alert -->
    @if ($errors->any())
        <div class="mb-6 bg-red-50 border-l-4 border-red-500 rounded-lg p-4 shadow-md" role="alert" x-data="{ show: true }" x-show="show">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3 flex-1">
                    <h3 class="text-sm font-bold text-red-800">Terdapat kesalahan pada formulir</h3>
                    <div class="mt-2 text-sm text-red-700">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <button @click="show = false" class="ml-3 text-red-400 hover:text-red-600">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                    </svg>
                </button>
            </div>
        </div>
    @endif

    <!-- Main Form -->
    <form method="POST" action="{{ route('outlet.results.store') }}" id="skb-form" class="relative">
        @csrf
        <input type="hidden" name="type" value="skb">

        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
            <!-- Step 1: Patient & Visit Information -->
            <div x-show="currentStep === 1" class="p-8 space-y-6" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-x-10" x-transition:enter-end="opacity-100 transform translate-x-0">
                <!-- Section Header -->
                <div class="flex items-center gap-4 mb-8 pb-6 border-b border-gray-100">
                    <div class="w-12 h-12 bg-gradient-to-br from-blue-100 to-blue-200 rounded-xl flex items-center justify-center">
                        <svg class="w-7 h-7 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-800">Data Pasien & Kunjungan</h3>
                        <p class="text-sm text-gray-500 mt-1">Masukkan informasi dasar pasien dan detail kunjungan</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Patient Name with Live Search -->
                    <div class="lg:col-span-2">
                        <label for="patient_name" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-user-injured mr-2 text-gray-400"></i>Nama Pasien
                        </label>
                        <div class="relative" x-data="{ patientOpen: false, searching: false }" @click.away="patientOpen = false">
                            <div class="relative">
                                <input type="text" 
                                       name="patient_name" 
                                       id="patient_name" 
                                       autocomplete="off"
                                       @focus="patientOpen = true"
                                       @input="patientOpen = true"
                                       class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                       placeholder="Ketik untuk mencari pasien atau masukkan nama baru..."
                                       value="{{ old('patient_name') }}">
                                <svg class="absolute left-4 top-3.5 w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                                <input type="hidden" name="patient_id" id="patient_id" value="{{ old('patient_id') }}">
                            </div>
                            
                            <!-- Patient Search Results -->
                            <div id="patient-suggestions" 
                                 x-show="patientOpen" 
                                 x-transition
                                 class="absolute z-50 w-full mt-2 bg-white rounded-xl shadow-2xl border border-gray-100 max-h-64 overflow-y-auto">
                                <!-- Dynamic content will be loaded here -->
                            </div>
                        </div>
                        @error('patient_name')
                            <p class="mt-2 text-sm text-red-600 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                        
                        <!-- New Patient Checkbox -->
                        <div class="mt-3 flex items-center p-3 bg-blue-50 rounded-lg">
                            <input type="checkbox" 
                                   name="is_new_patient" 
                                   id="is_new_patient" 
                                   class="w-5 h-5 text-blue-600 rounded border-gray-300 focus:ring-blue-500"
                                   @checked(old('is_new_patient'))>
                            <label for="is_new_patient" class="ml-3 text-sm font-medium text-gray-700">
                                <i class="fas fa-user-plus mr-2 text-blue-600"></i>
                                Daftarkan sebagai pasien baru
                            </label>
                        </div>
                    </div>

                    <!-- Company Selection -->
                    <div>
                        <label for="company_search" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-building mr-2 text-gray-400"></i>Perusahaan
                        </label>
                        <div class="flex gap-2">
                            <div class="relative flex-1" x-data="{ companyOpen: false }" @click.away="companyOpen = false">
                                <input type="text" 
                                       id="company_search" 
                                       name="company_name"
                                       @focus="companyOpen = true"
                                       @input="companyOpen = true"
                                       class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                       placeholder="Cari nama perusahaan..."
                                       value="{{ old('company_name') }}">
                                <svg class="absolute left-4 top-3.5 w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                                <input type="hidden" name="company_id" id="company_id" value="{{ old('company_id') }}">
                                
                                <!-- Company Search Results -->
                                <div id="company-suggestions" 
                                     x-show="companyOpen" 
                                     x-transition
                                     class="absolute z-50 w-full mt-2 bg-white rounded-xl shadow-2xl border border-gray-100 max-h-48 overflow-y-auto">
                                    <!-- Dynamic content will be loaded here -->
                                </div>
                            </div>
                            
                            <!-- Add Company Button -->
                            <button type="button" 
                                    onclick="document.getElementById('modalCompany').showModal()"
                                    class="px-4 py-3 bg-gradient-to-r from-green-500 to-green-600 text-white rounded-xl hover:from-green-600 hover:to-green-700 transition-all duration-200 shadow-md hover:shadow-lg transform hover:scale-105"
                                    title="Tambah Perusahaan Baru">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                        @error('company_id')
                            <p class="mt-2 text-sm text-red-600 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Visit Date -->
                    <div>
                        <label for="date" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-calendar-day mr-2 text-gray-400"></i>Tanggal Berobat
                        </label>
                        <input type="date" 
                               name="date" 
                               id="date"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                               value="{{ old('date', date('Y-m-d')) }}">
                        @error('date')
                            <p class="mt-2 text-sm text-red-600 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Visit Time -->
                    <div>
                        <label for="time" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-clock mr-2 text-gray-400"></i>Jam Berobat
                        </label>
                        <input type="time" 
                               name="time" 
                               id="time"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                               value="{{ old('time', date('H:i')) }}">
                        @error('time')
                            <p class="mt-2 text-sm text-red-600 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Step 2: Patient Details & Examination -->
            <div x-show="currentStep === 2" x-cloak class="p-8 space-y-6" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-x-10" x-transition:enter-end="opacity-100 transform translate-x-0">
                <!-- Section Header -->
                <div class="flex items-center gap-4 mb-8 pb-6 border-b border-gray-100">
                    <div class="w-12 h-12 bg-gradient-to-br from-purple-100 to-purple-200 rounded-xl flex items-center justify-center">
                        <svg class="w-7 h-7 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-800">Detail Pasien & Pemeriksaan</h3>
                        <p class="text-sm text-gray-500 mt-1">Lengkapi informasi medis dan diagnosis pasien</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Personal Information -->
                    <div>
                        <label for="dob" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-birthday-cake mr-2 text-gray-400"></i>Tanggal Lahir
                        </label>
                        <input type="date" 
                               name="dob" 
                               id="dob"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200"
                               value="{{ old('dob') }}">
                        @error('dob')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="gender" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-venus-mars mr-2 text-gray-400"></i>Jenis Kelamin
                        </label>
                        <select name="gender" 
                                id="gender"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200">
                            <option value="">Pilih jenis kelamin</option>
                            <option value="L" @selected(old('gender') == 'L')>Laki-laki</option>
                            <option value="P" @selected(old('gender') == 'P')>Perempuan</option>
                        </select>
                        @error('gender')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="nik" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-id-card mr-2 text-gray-400"></i>NIK KTP
                            <span class="text-xs text-gray-500 font-normal">(Opsional)</span>
                        </label>
                        <input type="text" 
                               name="nik" 
                               id="nik"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200"
                               placeholder="Masukkan 16 digit NIK"
                               value="{{ old('nik') }}">
                    </div>

                    <div>
                        <label for="identity" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-id-badge mr-2 text-gray-400"></i>No. Pegawai
                            <span class="text-xs text-gray-500 font-normal">(Opsional)</span>
                        </label>
                        <input type="text" 
                               name="identity" 
                               id="identity"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200"
                               placeholder="Nomor pegawai/karyawan"
                               value="{{ old('identity') }}">
                    </div>

                    <div>
                        <label for="phone" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-phone mr-2 text-gray-400"></i>Nomor Telepon
                            <span class="text-xs text-gray-500 font-normal">(Opsional)</span>
                        </label>
                        <input type="text" 
                               name="phone" 
                               id="phone"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200"
                               placeholder="08xx-xxxx-xxxx"
                               value="{{ old('phone') }}">
                    </div>

                    <div class="lg:col-span-2">
                        <label for="address" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-map-marker-alt mr-2 text-gray-400"></i>Alamat Lengkap
                        </label>
                        <textarea name="address" 
                                  id="address"
                                  rows="3"
                                  class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200 resize-none"
                                  placeholder="Masukkan alamat lengkap pasien">{{ old('address') }}</textarea>
                    </div>

                    <!-- ICD-10 Diagnosis -->
                    <div class="lg:col-span-2">
                        <label for="icd_search" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-stethoscope mr-2 text-gray-400"></i>Diagnosis (ICD-10)
                        </label>
                        <div class="relative" x-data="{ icdOpen: false }" @click.away="icdOpen = false">
                            <input type="text" 
                                   name="diagnosis_name" 
                                   id="icd_search"
                                   @focus="icdOpen = true"
                                   @input="icdOpen = true"
                                   autocomplete="off"
                                   class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200"
                                   placeholder="Cari kode ICD atau nama diagnosis (contoh: J00, demam, flu)..."
                                   value="{{ old('diagnosis_name') }}">
                            <svg class="absolute left-4 top-3.5 w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            <input type="hidden" name="icd_master_id" id="icd_master_id" value="{{ old('icd_master_id') }}">
                            
                            <!-- ICD Search Results -->
                            <div id="icd-suggestions" 
                                 x-show="icdOpen" 
                                 x-transition
                                 class="absolute z-50 w-full mt-2 bg-white rounded-xl shadow-2xl border border-gray-100 max-h-64 overflow-y-auto">
                                <!-- Dynamic content will be loaded here -->
                            </div>
                        </div>
                        
                        <!-- Selected ICD Display -->
                        <div id="selected-icd-info" class="mt-3 p-4 bg-purple-50 border border-purple-200 rounded-xl @if(!old('icd_master_id')) hidden @endif">
                            <div class="flex items-start gap-3">
                                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-check-circle text-purple-600"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="font-semibold text-gray-800">
                                        Kode: <span id="icd-code-display" class="text-purple-600">{{ explode(' - ', old('diagnosis_name'))[0] ?? '' }}</span>
                                    </p>
                                    <p class="text-sm text-gray-600 mt-1">
                                        <span id="icd-title-display">{{ explode(' - ', old('diagnosis_name'))[1] ?? old('diagnosis_name') }}</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                        @error('icd_master_id')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="lg:col-span-2">
                        <label for="notes" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-notes-medical mr-2 text-gray-400"></i>Catatan/Keterangan Tambahan
                            <span class="text-xs text-gray-500 font-normal">(Opsional)</span>
                        </label>
                        <textarea name="notes" 
                                  id="notes"
                                  rows="4"
                                  class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200 resize-none"
                                  placeholder="Tambahkan catatan atau keterangan khusus jika diperlukan">{{ old('notes') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Step 3: Doctor & Final Options -->
            <div x-show="currentStep === 3" x-cloak class="p-8 space-y-6" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-x-10" x-transition:enter-end="opacity-100 transform translate-x-0">
                <!-- Section Header -->
                <div class="flex items-center gap-4 mb-8 pb-6 border-b border-gray-100">
                    <div class="w-12 h-12 bg-gradient-to-br from-green-100 to-green-200 rounded-xl flex items-center justify-center">
                        <svg class="w-7 h-7 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-800">Dokter & Opsi Pengiriman</h3>
                        <p class="text-sm text-gray-500 mt-1">Pilih dokter pemeriksa dan atur notifikasi</p>
                    </div>
                </div>

                <!-- Doctor Selection -->
                <div>
                    <label for="doctor_id" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-user-md mr-2 text-gray-400"></i>Dokter Pemeriksa
                    </label>
                    <select name="doctor_id" 
                            id="doctor_id"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200">
                        <option value="">Pilih dokter pemeriksa</option>
                        @foreach ($doctors as $doctor)
                            <option value="{{ $doctor->id }}" @selected(old('doctor_id') == $doctor->id)>
                                {{ $doctor->user->name }}
                                @if($doctor->specialization)
                                    - {{ $doctor->specialization }}
                                @endif
                            </option>
                        @endforeach
                    </select>
                    @error('doctor_id')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Notification Options -->
                <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl p-6 space-y-4">
                    <h4 class="text-lg font-semibold text-gray-800 mb-4">
                        <i class="fas fa-bell mr-2 text-gray-500"></i>Opsi Notifikasi
                    </h4>

                    <!-- WhatsApp Notification -->
                    <div class="bg-white rounded-lg p-4" x-data="{ waEnabled: @json(old('send_notif_wa', false)) }">
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" 
                                   name="send_notif_wa" 
                                   id="send_notif_wa"
                                   value="1"
                                   x-model="waEnabled"
                                   @checked(old('send_notif_wa'))
                                   class="w-5 h-5 text-green-600 rounded border-gray-300 focus:ring-green-500">
                            <span class="ml-3 flex items-center text-gray-700">
                                <i class="fab fa-whatsapp text-green-500 text-xl mr-2"></i>
                                Kirim notifikasi via WhatsApp
                            </span>
                        </label>
                        
                        <div x-show="waEnabled" x-transition class="mt-4 ml-8">
                            <label for="whatsapp_number" class="block text-sm font-medium text-gray-700 mb-2">
                                Nomor WhatsApp
                            </label>
                            <div class="relative">
                                <span class="absolute left-4 top-3 text-gray-500">+62</span>
                                <input type="text" 
                                       name="whatsapp_number" 
                                       id="whatsapp_number"
                                       class="w-full pl-14 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200"
                                       placeholder="812-3456-7890"
                                       value="{{ old('whatsapp_number') }}">
                            </div>
                            @error('whatsapp_number')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Email Notification -->
                    <div class="bg-white rounded-lg p-4" x-data="{ emailEnabled: @json(old('send_notif_email', false)) }">
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" 
                                   name="send_notif_email" 
                                   id="send_notif_email"
                                   value="1"
                                   x-model="emailEnabled"
                                   @checked(old('send_notif_email'))
                                   class="w-5 h-5 text-blue-600 rounded border-gray-300 focus:ring-blue-500">
                            <span class="ml-3 flex items-center text-gray-700">
                                <i class="fas fa-envelope text-blue-500 text-xl mr-2"></i>
                                Kirim notifikasi via Email
                            </span>
                        </label>
                        
                        <div x-show="emailEnabled" x-transition class="mt-4 ml-8">
                            <label for="email_address" class="block text-sm font-medium text-gray-700 mb-2">
                                Alamat Email
                            </label>
                            <input type="email" 
                                   name="email_address" 
                                   id="email_address"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                   placeholder="pasien@email.com"
                                   value="{{ old('email_address') }}">
                            @error('email_address')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Navigation Bar -->
            <div class="px-8 py-6 bg-gray-50 border-t border-gray-200 flex justify-between items-center">
                <!-- Back Button -->
                <button type="button" 
                        @click="prevStep()" 
                        x-show="currentStep > 1"
                        class="inline-flex items-center px-6 py-3 bg-white border border-gray-300 text-gray-700 rounded-xl font-medium hover:bg-gray-50 transition-all duration-200 shadow-sm">
                    <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Kembali
                </button>
                
                <!-- Cancel Link -->
                <div x-show="currentStep === 1">
                    <a href="{{ route('outlet.healthletter.index') }}" 
                       class="text-gray-600 hover:text-gray-900 font-medium transition-colors">
                        Batalkan
                    </a>
                </div>

                <!-- Next Button -->
                <button type="button" 
                        @click="nextStep()" 
                        x-show="currentStep < 3"
                        class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-xl font-medium hover:from-blue-700 hover:to-blue-800 transition-all duration-200 shadow-lg transform hover:scale-105">
                    Selanjutnya
                    <svg class="w-5 h-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>

                <!-- Submit Button -->
                <button type="submit" 
                        id="submit-btn"
                        x-show="currentStep === 3"
                        class="inline-flex items-center px-8 py-3 bg-gradient-to-r from-green-600 to-green-700 text-white rounded-xl font-bold hover:from-green-700 hover:to-green-800 transition-all duration-200 shadow-lg transform hover:scale-105">
                    <span id="submit-text" class="inline-flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Simpan & Proses Surat
                    </span>
                    <span id="submit-loading" class="hidden inline-flex items-center">
                        <svg class="animate-spin h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Memproses...
                    </span>
                </button>
            </div>
        </div>
    </form>
</div>

<!-- Company Modal -->
<dialog id="modalCompany" class="rounded-2xl shadow-2xl p-0 max-w-md w-full backdrop:bg-gray-900/50">
    <div class="bg-white rounded-2xl overflow-hidden">
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 p-6 text-white">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                    <i class="fas fa-building text-2xl"></i>
                </div>
                <div>
                    <h3 class="text-xl font-bold">Tambah Perusahaan Baru</h3>
                    <p class="text-sm text-blue-100 mt-1">Perusahaan akan langsung tersedia untuk dipilih</p>
                </div>
            </div>
        </div>
        
        <form method="POST" action="{{ route('outlet.companies.store') }}" id="company-form" class="p-6">
            @csrf
            <div class="mb-6">
                <label for="modal_company_name" class="block text-sm font-semibold text-gray-700 mb-2">
                    Nama Perusahaan
                </label>
                <input type="text" 
                       name="name" 
                       id="modal_company_name"
                       class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                       placeholder="Contoh: PT. Maju Bersama"
                       required>
                <p id="company-error" class="mt-2 text-sm text-red-600 hidden"></p>
            </div>
            
            <div class="flex justify-end gap-3">
                <button type="button" 
                        onclick="document.getElementById('modalCompany').close()"
                        class="px-6 py-3 bg-gray-200 text-gray-700 rounded-xl font-medium hover:bg-gray-300 transition-all duration-200">
                    Batal
                </button>
                <button type="submit" 
                        id="save-company-btn"
                        class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-xl font-medium hover:from-blue-700 hover:to-blue-800 transition-all duration-200 shadow-lg">
                    <span id="save-company-text">
                        <i class="fas fa-save mr-2"></i>Simpan
                    </span>
                    <span id="save-company-loading" class="hidden">
                        <svg class="animate-spin h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        Menyimpan...
                    </span>
                </button>
            </div>
        </form>
    </div>
</dialog>

<style>
    [x-cloak] { display: none !important; }
    
    /* Custom scrollbar for search results */
    #patient-suggestions::-webkit-scrollbar,
    #company-suggestions::-webkit-scrollbar,
    #icd-suggestions::-webkit-scrollbar {
        width: 8px;
    }
    
    #patient-suggestions::-webkit-scrollbar-track,
    #company-suggestions::-webkit-scrollbar-track,
    #icd-suggestions::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    
    #patient-suggestions::-webkit-scrollbar-thumb,
    #company-suggestions::-webkit-scrollbar-thumb,
    #icd-suggestions::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 10px;
    }
    
    #patient-suggestions::-webkit-scrollbar-thumb:hover,
    #company-suggestions::-webkit-scrollbar-thumb:hover,
    #icd-suggestions::-webkit-scrollbar-thumb:hover {
        background: #555;
    }
</style>
@endsection

@push('scripts')
@include('outlets.results._scriptskb')
@endpush