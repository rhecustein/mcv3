@extends('layouts.app', ['header' => 'Buat Surat Keterangan Berobat'])

@section('content')
<div class="max-w-4xl mx-auto" x-data="formWizard()">

    <div class="text-center mb-8">
        <h1 class="text-3xl font-bold text-slate-800">Buat Surat Keterangan Berobat (SKB)</h1>
        <p class="mt-2 text-base text-slate-500">Ikuti langkah-langkah di bawah untuk menerbitkan surat baru.</p>
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
                        <button @click="goToStep(index + 1)" :disabled="index + 1 > maxStep"
                                class="w-8 h-8 rounded-full flex items-center justify-center transition-colors duration-300 text-sm font-bold"
                                :class="{
                                    'bg-blue-600 text-white': currentStep === index + 1,
                                    'bg-white border-2 border-blue-600 text-blue-600': currentStep > index + 1,
                                    'bg-slate-100 border-2 border-slate-200 text-slate-400 cursor-not-allowed': currentStep < index + 1 && maxStep <= index,
                                    'bg-white border-2 border-slate-300 text-slate-500': currentStep < index + 1 && maxStep > index
                                }">
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
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="font-semibold">Terdapat kesalahan pada input Anda. Silakan periksa kembali.</h3>
                    @foreach ($errors->all() as $error)
                        <p class="mt-1 text-xs">{{ $error }}</p>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('outlet.results.store.skb') }}" id="skb-form">
        @csrf
        <input type="hidden" name="type" value="skb">

        <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-8">
            <div x-show="currentStep === 1" class="space-y-6 animate-fade-in">
                <h3 class="text-lg font-semibold text-slate-800 border-b border-slate-200 pb-3 mb-6">Langkah 1: Pasien & Kunjungan</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="patient_name" class="block text-sm font-medium leading-6 text-slate-700 mb-1.5">Nama Pasien</label>
                        <div class="relative" x-data="{ patientSuggestionsOpen: false }" @click.away="patientSuggestionsOpen = false">
                            <input type="text" name="patient_name" id="patient_name" autocomplete="off" class="w-full rounded-md border-slate-300 shadow-sm" placeholder="Cari atau ketik baru..." value="{{ old('patient_name') }}" @focus="patientSuggestionsOpen = true" @input="patientSuggestionsOpen = true">
                            <input type="hidden" name="patient_id" id="patient_id" value="{{ old('patient_id') }}">
                            <div id="suggestions" class="absolute z-50 bg-white border mt-1 rounded-md shadow-lg max-h-60 overflow-y-auto w-full" x-show="patientSuggestionsOpen" x-cloak></div>
                        </div>
                        @error('patient_name')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                        <div class="mt-2">
                            <input type="checkbox" name="is_new_patient" id="is_new_patient" class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-600" @checked(old('is_new_patient'))>
                            <label for="is_new_patient" class="ml-2 text-sm text-slate-600">Ini pasien baru</label>
                        </div>
                    </div>
                    <div>
                        <label for="company_search" class="block text-sm font-medium leading-6 text-slate-700 mb-1.5">Perusahaan</label>
                        <div class="flex gap-2">
                            <div class="relative flex-grow" x-data="{ companySuggestionsOpen: false }" @click.away="companySuggestionsOpen = false">
                                <input type="text" id="company_search" name="company_name" class="w-full rounded-md border-slate-300 shadow-sm" placeholder="Cari perusahaan..." value="{{ old('company_name') }}" @focus="companySuggestionsOpen = true" @input="companySuggestionsOpen = true">
                                <input type="hidden" name="company_id" id="company_id" value="{{ old('company_id') }}">
                                <div id="company_suggestions" class="absolute z-50 bg-white border mt-1 rounded shadow-md max-h-40 overflow-y-auto w-full text-sm" x-show="companySuggestionsOpen" x-cloak></div>
                            </div>
                            <button type="button" onclick="document.getElementById('modalCompany').showModal()" class="px-3 py-2 bg-green-100 text-green-700 rounded-md hover:bg-green-200 transition" title="Tambah Perusahaan Baru">+</button>
                        </div>
                        @error('company_id')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                        @error('company_name')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="date" class="block text-sm font-medium leading-6 text-slate-700 mb-1.5">Tanggal Berobat</label>
                        <input type="date" name="date" id="date" class="w-full rounded-md border-slate-300 shadow-sm" value="{{ old('date', date('Y-m-d')) }}">
                        @error('date')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="time" class="block text-sm font-medium leading-6 text-slate-700 mb-1.5">Jam Berobat</label>
                        <input type="time" name="time" id="time" class="w-full rounded-md border-slate-300 shadow-sm" value="{{ old('time', date('H:i')) }}">
                        @error('time')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            <div x-show="currentStep === 2" x-cloak class="space-y-6 animate-fade-in">
                <h3 class="text-lg font-semibold text-slate-800 border-b border-slate-200 pb-3 mb-6">Langkah 2: Detail Pasien & Pemeriksaan</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="dob" class="block text-sm font-medium leading-6 text-slate-700 mb-1.5">Tanggal Lahir</label>
                        <input type="date" name="dob" id="dob" value="{{ old('dob') }}" class="w-full rounded-md border-slate-300 shadow-sm">
                        @error('dob')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="gender" class="block text-sm font-medium leading-6 text-slate-700 mb-1.5">Jenis Kelamin</label>
                        <select name="gender" id="gender" class="w-full rounded-md border-slate-300 shadow-sm">
                            <option value="">~ Pilih ~</option>
                            <option value="L" @selected(old('gender') == 'L')>Laki-laki</option>
                            <option value="P" @selected(old('gender') == 'P')>Perempuan</option>
                        </select>
                        @error('gender')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="nik" class="block text-sm font-medium leading-6 text-slate-700 mb-1.5">NIK KTP (Opsional)</label>
                        <input type="text" name="nik" id="nik" value="{{ old('nik') }}" class="w-full rounded-md border-slate-300 shadow-sm">
                    </div>
                    <div>
                        <label for="identity" class="block text-sm font-medium leading-6 text-slate-700 mb-1.5">No. Pegawai (Opsional)</label>
                        <input type="text" name="identity" id="identity" value="{{ old('identity') }}" class="w-full rounded-md border-slate-300 shadow-sm">
                    </div>
                    <div>
                        <label for="phone" class="block text-sm font-medium leading-6 text-slate-700 mb-1.5">Nomor Telepon (Opsional)</label>
                        <input type="text" name="phone" id="phone" value="{{ old('phone') }}" class="w-full rounded-md border-slate-300 shadow-sm">
                    </div>
                    <div class="md:col-span-2">
                        <label for="address" class="block text-sm font-medium leading-6 text-slate-700 mb-1.5">Alamat</label>
                        <textarea name="address" id="address" rows="3" class="w-full rounded-md border-slate-300 shadow-sm">{{ old('address') }}</textarea>
                    </div>
                    <div>
                        <label for="icd_search" class="block text-sm font-medium leading-6 text-slate-700 mb-1.5">Diagnosis (ICD-10)</label>
                        <div class="relative" x-data="{ icdSuggestionsOpen: false }" @click.away="icdSuggestionsOpen = false">
                            <input type="text" name="diagnosis_name" id="icd_search" autocomplete="off" class="w-full rounded-md border-slate-300 shadow-sm" placeholder="Cari diagnosis (misal: A00, demam)..." value="{{ old('diagnosis_name') }}" @focus="icdSuggestionsOpen = true" @input="icdSuggestionsOpen = true">
                            <input type="hidden" name="icd_master_id" id="icd_master_id" value="{{ old('icd_master_id') }}">
                            <div id="icd_suggestions" class="absolute z-50 bg-white border mt-1 rounded-md shadow-lg max-h-60 overflow-y-auto w-full" x-show="icdSuggestionsOpen" x-cloak></div>
                        </div>
                        <div id="selected_icd_info" class="mt-2 p-2 text-sm bg-slate-50 border border-slate-200 rounded-md @if(!old('icd_master_id')) hidden @endif">
                            @if(old('icd_master_id') && old('diagnosis_name'))
                                <p><span class="font-semibold">Kode:</span> <span id="icd_code_display">{{ explode(' - ', old('diagnosis_name'))[0] ?? '' }}</span></p>
                                <p><span class="font-semibold">Deskripsi:</span> <span id="icd_title_display">{{ explode(' - ', old('diagnosis_name'))[1] ?? old('diagnosis_name') }}</span></p>
                            @else
                                <p>Pilih ICD-10 untuk melihat detailnya.</p>
                            @endif
                        </div>
                        @error('icd_master_id')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                        @error('diagnosis_name')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div class="md:col-span-2">
                        <label for="notes" class="block text-sm font-medium leading-6 text-slate-700 mb-1.5">Catatan/Keterangan Tambahan (Opsional)</label>
                        <textarea name="notes" id="notes" rows="3" class="w-full rounded-md border-slate-300 shadow-sm">{{ old('notes') }}</textarea>
                    </div>
                </div>
            </div>

            <div x-show="currentStep === 3" x-cloak class="space-y-6 animate-fade-in">
                <h3 class="text-lg font-semibold text-slate-800 border-b border-slate-200 pb-3 mb-6">Langkah 3: Dokter & Opsi Final</h3>
                <div>
                    <label for="doctor_id" class="block text-sm font-medium leading-6 text-slate-700 mb-1.5">Dokter Pemeriksa</label>
                    <select name="doctor_id" id="doctor_id" class="w-full rounded-md border-slate-300 shadow-sm">
                        <option value="">~ Pilih Dokter ~</option>
                        @foreach ($doctors as $doctor)
                            <option value="{{ $doctor->id }}" @selected(old('doctor_id') == $doctor->id)>{{ $doctor->user->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium leading-6 text-slate-700 mb-2">Opsi Notifikasi (Opsional)</label>
                    <div class="space-y-2">
                        <label class="flex items-center">
                            <input type="checkbox" name="send_notif_wa" id="send_notif_wa" value="1" @checked(old('send_notif_wa')) class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-600">
                            <span class="ml-2 text-sm text-slate-600">Kirim Notifikasi via WhatsApp</span>
                        </label>
                        <div id="wa_input_wrapper" class="pl-6 mt-2 {{ old('send_notif_wa') ? '' : 'hidden' }}">
                            <label for="whatsapp_number" class="block text-sm font-medium leading-6 text-slate-700 mb-1.5">Nomor WhatsApp</label>
                            <input type="text" name="whatsapp_number" id="whatsapp_number" value="{{ old('whatsapp_number') }}" class="w-full rounded-md border-slate-300 shadow-sm" placeholder="Cth: 6281234567890">
                            @error('whatsapp_number')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                        </div>

                        <label class="flex items-center">
                            <input type="checkbox" name="send_notif_email" id="send_notif_email" value="1" @checked(old('send_notif_email')) class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-600">
                            <span class="ml-2 text-sm text-slate-600">Kirim Notifikasi via Email</span>
                        </label>
                        <div id="email_input_wrapper" class="pl-6 mt-2 {{ old('send_notif_email') ? '' : 'hidden' }}">
                            <label for="email_address" class="block text-sm font-medium leading-6 text-slate-700 mb-1.5">Alamat Email</label>
                            <input type="email" name="email_address" id="email_address" value="{{ old('email_address') }}" class="w-full rounded-md border-slate-300 shadow-sm" placeholder="Cth: nama@contoh.com">
                            @error('email_address')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-8 pt-5 border-t border-slate-200 flex justify-between items-center">
                <button type="button" @click="prevStep()" x-show="currentStep > 1" class="px-5 py-2 bg-slate-200 text-slate-700 rounded-lg text-sm font-medium hover:bg-slate-300 transition">
                    &larr; Kembali
                </button>
                <div x-show="currentStep === 1" class="w-full text-left">
                    <a href="{{ route('outlet.healthletter.index') }}" class="text-sm text-slate-600 hover:text-slate-900 transition hover:underline">Batal</a>
                </div>

                <button type="button" @click="nextStep()" x-show="currentStep < 3" class="px-5 py-2 bg-blue-600 text-white rounded-lg text-sm font-semibold hover:bg-blue-700 transition">
                    Selanjutnya &rarr;
                </button>

                <button type="submit" id="submitBtn" x-show="currentStep === 3" class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-green-600 text-white font-bold rounded-lg shadow-md hover:bg-green-700 transition-all">
                    <span id="btnText">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        Simpan & Proses Surat
                    </span>
                    <div id="btnLoading" class="hidden">
                        <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>
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
            <p class="text-sm text-slate-500 mb-4">Perusahaan yang ditambahkan di sini akan langsung tersedia.</p>
            <div class="mb-4">
                <label for="modal_company_name" class="block text-sm font-medium text-slate-700">Nama Perusahaan</label>
                <input type="text" name="name" id="modal_company_name" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm" required>
                <p id="company_modal_error" class="text-sm text-red-600 mt-1 hidden"></p>
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('modalCompany').close()" class="px-4 py-2 bg-slate-100 text-slate-700 rounded-md text-sm font-medium hover:bg-slate-200">Batal</button>
                <button type="submit" id="saveCompanyBtn" class="px-4 py-2 bg-blue-600 text-white rounded-md text-sm font-semibold hover:bg-blue-700">
                    <span id="saveCompanyBtnText">Simpan</span>
                    <div id="saveCompanyBtnLoading" class="hidden">
                        <svg class="animate-spin h-4 w-4 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>
                </button>
            </div>
        </form>
    </div>
</dialog>
@endsection

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('formWizard', () => ({
        currentStep: 1,
        maxStep: 1, // To keep track of the furthest step reached
        steps: ['Pasien & Kunjungan', 'Detail Pasien & Pemeriksaan', 'Dokter & Opsi Final'],

        init() {
            // Restore currentStep if there are validation errors
            @if ($errors->hasAny(['patient_name', 'dob', 'gender', 'icd_master_id', 'diagnosis_name']))
                this.currentStep = 2;
                this.maxStep = 2;
            @elseif ($errors->hasAny(['doctor_id', 'whatsapp_number', 'email_address']))
                this.currentStep = 3;
                this.maxStep = 3;
            @endif
        },

        get progress() {
            return ((this.currentStep - 1) / (this.steps.length - 1)) * 100;
        },

        nextStep() {
            // Client-side validation before moving to the next step
            if (!this.validateStep(this.currentStep)) {
                return;
            }

            if (this.currentStep < this.steps.length) {
                this.currentStep++;
                if (this.currentStep > this.maxStep) {
                    this.maxStep = this.currentStep;
                }
            }
        },

        prevStep() {
            if (this.currentStep > 1) {
                this.currentStep--;
            }
        },

        goToStep(step) {
            if (step <= this.maxStep) {
                this.currentStep = step;
            }
        },

        validateStep(step) {
            let isValid = true;
            const form = document.getElementById('skb-form');
            const inputs = form.querySelectorAll(`[x-show="currentStep === ${step}"] input, [x-show="currentStep === ${step}"] select, [x-show="currentStep === ${step}"] textarea`);

            inputs.forEach(input => {
                // For simplicity, we'll only check for 'required' here.
                // More complex validation should be handled server-side.
                // Removed general required check for inputs here to rely on specific step checks
                // and Laravel's server-side validation.
            });

            // Specific validation for Step 1
            if (step === 1) {
                const patientName = document.getElementById('patient_name');
                const companyName = document.getElementById('company_search');
                const date = document.getElementById('date');
                const time = document.getElementById('time');

                if (!patientName.value.trim()) {
                    isValid = false;
                    patientName.classList.add('border-red-500');
                } else {
                    patientName.classList.remove('border-red-500');
                }
                if (!companyName.value.trim()) { // Assuming company is required
                    isValid = false;
                    companyName.classList.add('border-red-500');
                } else {
                    companyName.classList.remove('border-red-500');
                }
                if (!date.value.trim()) { // Assuming date is required
                    isValid = false;
                    date.classList.add('border-red-500');
                } else {
                    date.classList.remove('border-red-500');
                }
                if (!time.value.trim()) { // Assuming time is required
                    isValid = false;
                    time.classList.add('border-red-500');
                } else {
                    time.classList.remove('border-red-500');
                }
            }
            // Specific validation for Step 2
            if (step === 2) {
                const dob = document.getElementById('dob');
                const gender = document.getElementById('gender');
                const icdMasterId = document.getElementById('icd_master_id');
                const icdSearch = document.getElementById('icd_search');

                if (!dob.value.trim()) {
                    isValid = false;
                    dob.classList.add('border-red-500');
                } else {
                    dob.classList.remove('border-red-500');
                }
                if (!gender.value.trim()) {
                    isValid = false;
                    gender.classList.add('border-red-500');
                } else {
                    gender.classList.remove('border-red-500');
                }
                if (!icdMasterId.value.trim() && icdSearch.value.trim()) { // ICD input has text but no ID means not selected
                    isValid = false;
                    icdSearch.classList.add('border-red-500');
                } else {
                    icdSearch.classList.remove('border-red-500');
                }
                // If icd_search is empty, it's optional
                if (icdSearch.value.trim() && !icdMasterId.value.trim()) {
                    isValid = false;
                    icdSearch.classList.add('border-red-500');
                } else {
                    icdSearch.classList.remove('border-red-500');
                }
            }
            // Specific validation for Step 3
            if (step === 3) {
                const doctorId = document.getElementById('doctor_id');
                const doctorSearch = document.getElementById('doctor_search');

                if (!doctorId.value.trim() && doctorSearch.value.trim()) { // Doctor input has text but no ID means not selected
                    isValid = false;
                    doctorSearch.classList.add('border-red-500');
                } else {
                    doctorSearch.classList.remove('border-red-500');
                }
                // If doctor_search is empty, it's optional
                if (doctorSearch.value.trim() && !doctorId.value.trim()) {
                    isValid = false;
                    doctorSearch.classList.add('border-red-500');
                } else {
                    doctorSearch.classList.remove('border-red-500');
                }
            }

            return isValid;
        }
    }));
});

document.addEventListener('DOMContentLoaded', function () {
    // Helper for debouncing input
    function debounce(func, delay) {
        let timeout;
        return function (...args) {
            const context = this;
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(context, args), delay);
        };
    }

    // ============ PASIEN LIVE SEARCH ============
    const patientInput = document.getElementById('patient_name');
    const suggestionsBox = document.getElementById('suggestions');
    const patientIdInput = document.getElementById('patient_id');
    const isNewPatientCheckbox = document.getElementById('is_new_patient');
    const patientDobInput = document.getElementById('dob');
    const patientGenderSelect = document.getElementById('gender');
    const patientPhoneInput = document.getElementById('phone');
    const patientAddressInput = document.getElementById('address');

    // Restore old patient data if validation failed
    @if(old('patient_id') && old('patient_name'))
        patientInput.value = "{{ old('patient_name') }}";
        patientIdInput.value = "{{ old('patient_id') }}";
        isNewPatientCheckbox.checked = false;
        isNewPatientCheckbox.disabled = true;
    @elseif(old('patient_name') && !old('patient_id'))
        isNewPatientCheckbox.checked = true;
        isNewPatientCheckbox.disabled = false;
    @endif

    const fetchPatientSuggestions = debounce(function (query) {
        if (query.length >= 1) {
            fetch(`/outlet/patients/live-search?q=${encodeURIComponent(query)}`)
                .then(res => res.json())
                .then(data => {
                    suggestionsBox.innerHTML = '';
                    if (data.length > 0) {
                        data.forEach(item => {
                            const div = document.createElement('div');
                            div.className = 'px-3 py-2 hover:bg-blue-100 cursor-pointer border-b text-sm';
                            div.innerHTML = `<strong>${item.name}</strong> - ${item.phone_number || 'N/A'}`;
                            div.onclick = () => selectPatient(item);
                            suggestionsBox.appendChild(div);
                        });
                    } else {
                        // No suggestions, Alpine.js x-show handles visibility
                    }
                });
        } else {
            suggestionsBox.innerHTML = '';
            // Alpine.js x-show handles visibility
        }
    }, 300); // 300ms debounce

    patientInput?.addEventListener('input', function () {
        const query = this.value.trim();
        patientIdInput.value = ''; // Clear patient_id if input changes
        isNewPatientCheckbox.checked = true;
        isNewPatientCheckbox.disabled = false;
        clearPatientDetails(); // Clear other patient details
        fetchPatientSuggestions(query);
    });

    // Clear patient details when a new patient is indicated
    isNewPatientCheckbox?.addEventListener('change', function() {
        if (this.checked) {
            patientIdInput.value = '';
            clearPatientDetails();
        } else if (patientInput.value.trim() && !patientIdInput.value) {
            // If checkbox is unchecked and no patient ID, try to re-search
            fetchPatientSuggestions(patientInput.value.trim());
        }
    });

    function selectPatient(data) {
        patientInput.value = data.name; // Keep only name in the input
        patientIdInput.value = data.id;
        patientDobInput.value = data.dob || '';
        patientGenderSelect.value = data.gender || '';
        patientPhoneInput.value = data.phone_number || '';
        patientAddressInput.value = data.address || '';
        isNewPatientCheckbox.checked = false;
        isNewPatientCheckbox.disabled = true;
        suggestionsBox.innerHTML = ''; // Clear and hide suggestions
        patientInput.focus(); // Keep focus on the input after selection
    }

    function clearPatientDetails() {
        patientDobInput.value = '';
        patientGenderSelect.value = '';
        patientPhoneInput.value = '';
        patientAddressInput.value = '';
    }

    // ============ PERUSAHAAN LIVE SEARCH ============
    const companyInput = document.getElementById('company_search');
    const companyIdInput = document.getElementById('company_id');
    const companySuggestions = document.getElementById('company_suggestions');

    // Restore old company data if validation failed
    @if(old('company_id') && old('company_name'))
        companyInput.value = "{{ old('company_name') }}";
        companyIdInput.value = "{{ old('company_id') }}";
    @endif

    const fetchCompanySuggestions = debounce(function (query) {
        if (query.length >= 1) {
            fetch(`/outlet/companies/live-search?q=${encodeURIComponent(query)}`)
                .then(res => res.json())
                .then(data => {
                    companySuggestions.innerHTML = '';
                    if (data.length > 0) {
                        data.forEach(item => {
                            const div = document.createElement('div');
                            div.className = 'px-3 py-2 hover:bg-blue-100 cursor-pointer border-b text-sm';
                            div.textContent = item.name;
                            div.onclick = () => selectCompany(item);
                            companySuggestions.appendChild(div);
                        });
                    } else {
                        // No suggestions, Alpine.js x-show handles visibility
                    }
                });
        } else {
            companySuggestions.innerHTML = '';
            // Alpine.js x-show handles visibility
        }
    }, 300);

    companyInput?.addEventListener('input', function () {
        const query = this.value.trim();
        companyIdInput.value = ''; // Clear company_id if input changes
        fetchCompanySuggestions(query);
    });

    function selectCompany(company) {
        companyInput.value = company.name;
        companyIdInput.value = company.id;
        companySuggestions.innerHTML = ''; // Clear and hide suggestions
        companyInput.focus(); // Keep focus on the input after selection
    }

    // ============ ICD-10 LIVE SEARCH ============
    const icdInput = document.getElementById('icd_search');
    const icdSuggestions = document.getElementById('icd_suggestions');
    const icdMasterId = document.getElementById('icd_master_id');
    const selectedIcdInfoDiv = document.getElementById('selected_icd_info');
    const icdCodeDisplay = document.getElementById('icd_code_display');
    const icdTitleDisplay = document.getElementById('icd_title_display');

    // Restore old ICD data if validation failed and display details
    @if(old('icd_master_id') && old('diagnosis_name'))
        icdInput.value = "{{ old('diagnosis_name') }}";
        icdMasterId.value = "{{ old('icd_master_id') }}";
        selectedIcdInfoDiv.classList.remove('hidden');
        icdCodeDisplay.textContent = "{{ explode(' - ', old('diagnosis_name'))[0] ?? '' }}";
        icdTitleDisplay.textContent = "{{ explode(' - ', old('diagnosis_name'))[1] ?? old('diagnosis_name') }}";
    @else
        selectedIcdInfoDiv.classList.add('hidden'); // Ensure it's hidden if no old value
    @endif


    const fetchIcdSuggestions = debounce(function (query) {
        if (query.length >= 2) {
            fetch(`/outlet/icd10/live-search?q=${encodeURIComponent(query)}`)
                .then(res => res.json())
                .then(data => {
                    icdSuggestions.innerHTML = '';
                    if (data.length > 0) {
                        data.forEach(item => {
                            const div = document.createElement('div');
                            div.className = 'px-3 py-2 hover:bg-blue-100 cursor-pointer border-b text-sm';
                            div.innerHTML = `<strong>${item.code}</strong> - ${item.title}`;
                            div.onclick = () => selectIcd(item);
                            icdSuggestions.appendChild(div);
                        });
                    } else {
                        // Alpine.js x-show handles visibility
                    }
                })
                .catch(error => {
                    console.error('Error fetching ICD suggestions:', error);
                    icdSuggestions.innerHTML = '<div class="px-3 py-2 text-red-500">Gagal memuat saran ICD.</div>';
                });
        } else {
            icdSuggestions.innerHTML = '';
            // Alpine.js x-show handles visibility
        }
    }, 300);

    icdInput?.addEventListener('input', function () {
        const query = this.value.trim();
        icdMasterId.value = ''; // Clear icd_master_id if input changes
        selectedIcdInfoDiv.classList.add('hidden'); // Hide details when input changes
        fetchIcdSuggestions(query);
    });

    function selectIcd(item) {
        icdInput.value = `${item.code} - ${item.title}`;
        icdMasterId.value = item.id;

        // Display full ICD info
        icdCodeDisplay.textContent = item.code;
        icdTitleDisplay.textContent = item.title;
        selectedIcdInfoDiv.classList.remove('hidden');

        icdSuggestions.innerHTML = ''; // Clear and hide suggestions
        icdInput.focus(); // Keep focus on the input after selection
    }

    // ============ DOCTOR LIVE SEARCH ============
    const doctorInput = document.getElementById('doctor_search');
    const doctorIdInput = document.getElementById('doctor_id');
    const doctorSuggestions = document.getElementById('doctor_suggestions');
    const selectedDoctorInfoDiv = document.getElementById('selected_doctor_info');
    const doctorNameDisplayInfo = document.getElementById('doctor_name_display_info');
    const doctorSpecializationDisplayInfo = document.getElementById('doctor_specialization_display_info');
    const doctorLicenseDisplayInfo = document.getElementById('doctor_license_display_info');

    // Restore old doctor data if validation failed and display details
    @if(old('doctor_id') && old('doctor_name_display'))
        doctorInput.value = "{{ old('doctor_name_display') }}";
        doctorIdInput.value = "{{ old('doctor_id') }}";
        // Fetch full doctor details to populate specialization and license
        fetch(`/outlet/doctors/live-search?id={{ old('doctor_id') }}`)
            .then(res => res.json())
            .then(data => {
                if (data.length > 0) {
                    selectDoctor(data[0]); // Use selectDoctor to populate all fields
                }
            })
            .catch(error => console.error('Error fetching old doctor details:', error));
    @else
        selectedDoctorInfoDiv.classList.add('hidden'); // Ensure it's hidden if no old value
    @endif

    const fetchDoctorSuggestions = debounce(function (query) {
        if (query.length >= 2) {
            fetch(`/outlet/doctors/live-search?q=${encodeURIComponent(query)}`)
                .then(res => res.json())
                .then(data => {
                    doctorSuggestions.innerHTML = '';
                    if (data.length > 0) {
                        data.forEach(item => {
                            const div = document.createElement('div');
                            div.className = 'px-3 py-2 hover:bg-blue-100 cursor-pointer border-b text-sm';
                            // Assuming 'user_name' is returned from your backend
                            div.innerHTML = `<strong>${item.user_name}</strong> - ${item.specialization || 'Umum'} ${item.license_number ? `(${item.license_number})` : ''}`;
                            div.onclick = () => selectDoctor(item);
                            doctorSuggestions.appendChild(div);
                        });
                    } else {
                        // Alpine.js x-show handles visibility
                    }
                })
                .catch(error => {
                    console.error('Error fetching doctor suggestions:', error);
                    doctorSuggestions.innerHTML = '<div class="px-3 py-2 text-red-500">Gagal memuat saran dokter.</div>';
                });
        } else {
            doctorSuggestions.innerHTML = '';
            // Alpine.js x-show handles visibility
        }
    }, 300);

    doctorInput?.addEventListener('input', function () {
        const query = this.value.trim();
        doctorIdInput.value = ''; // Clear doctor_id if input changes
        // Also clear the hidden name that's used for old() display
        document.querySelector('input[name="doctor_name_display"]').value = '';
        selectedDoctorInfoDiv.classList.add('hidden'); // Hide details when input changes
        fetchDoctorSuggestions(query);
    });

    function selectDoctor(data) {
        doctorInput.value = data.user_name; // Set display name
        doctorIdInput.value = data.id;

        // Set the hidden input for old() value persistence
        const doctorNameDisplayHidden = document.querySelector('input[name="doctor_name_display"]');
        if (doctorNameDisplayHidden) {
            doctorNameDisplayHidden.value = data.user_name;
        }

        // Display full doctor info
        doctorNameDisplayInfo.textContent = data.user_name;
        doctorSpecializationDisplayInfo.textContent = data.specialization || 'Umum';
        doctorLicenseDisplayInfo.textContent = data.license_number || 'Tidak Ada';
        selectedDoctorInfoDiv.classList.remove('hidden');

        doctorSuggestions.innerHTML = ''; // Clear and hide suggestions
        doctorInput.focus(); // Keep focus on the input after selection
    }


    // ============ NOTIFIKASI ============
    document.getElementById('send_notif_email')?.addEventListener('change', function () {
        document.getElementById('email_input_wrapper')?.classList.toggle('hidden', !this.checked);
    });

    document.getElementById('send_notif_wa')?.addEventListener('change', function () {
        document.getElementById('wa_input_wrapper')?.classList.toggle('hidden', !this.checked);
    });

    // ============ LOADING SUBMIT BUTTONS ============
    document.getElementById('skb-form')?.addEventListener('submit', function () {
        const btn = document.getElementById('submitBtn');
        const btnText = document.getElementById('btnText');
        const btnLoading = document.getElementById('btnLoading');

        if (btn && btnText && btnLoading) {
            btnText.classList.add('opacity-0');
            btnLoading.classList.remove('hidden');
            btn.disabled = true;
        }
    });

    // ============ COMPANY MODAL AJAX SUBMISSION ============
    const companyModal = document.getElementById('modalCompany');
    const companyForm = document.getElementById('companyForm');
    const modalCompanyNameInput = document.getElementById('modal_company_name');
    const companyModalError = document.getElementById('company_modal_error');
    const saveCompanyBtn = document.getElementById('saveCompanyBtn');
    const saveCompanyBtnText = document.getElementById('saveCompanyBtnText');
    const saveCompanyBtnLoading = document.getElementById('saveCompanyBtnLoading');

    companyForm?.addEventListener('submit', function (e) {
        e.preventDefault(); // Prevent default form submission

        // Show loading state
        saveCompanyBtnText.classList.add('opacity-0');
        saveCompanyBtnLoading.classList.remove('hidden');
        saveCompanyBtn.disabled = true;
        companyModalError.classList.add('hidden'); // Hide previous errors

        fetch(this.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ name: modalCompanyNameInput.value })
        })
        .then(response => response.json().then(data => ({ status: response.status, body: data })))
        .then(({ status, body }) => {
            if (status === 201) { // Success: Company created
                selectCompany(body.company); // Select the newly created company
                companyModal.close(); // Close the modal
                modalCompanyNameInput.value = ''; // Clear modal input
            } else if (status === 422) { // Validation error
                companyModalError.textContent = body.errors.name ? body.errors.name[0] : 'Terjadi kesalahan validasi.';
                companyModalError.classList.remove('hidden');
            } else {
                companyModalError.textContent = body.message || 'Terjadi kesalahan saat menambahkan perusahaan.';
                companyModalError.classList.remove('hidden');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            companyModalError.textContent = 'Terjadi kesalahan jaringan atau server.';
            companyModalError.classList.remove('hidden');
        })
        .finally(() => {
            // Revert loading state
            saveCompanyBtnText.classList.remove('opacity-0');
            saveCompanyBtnLoading.classList.add('hidden');
            saveCompanyBtn.disabled = false;
        });
    });
});
</script>
@endpush