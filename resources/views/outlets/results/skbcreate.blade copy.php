@extends('layouts.app', ['header' => 'Buat Surat Keterangan Berobat'])

@section('content')
{{-- 
    PENTING: Halaman ini menggunakan Alpine.js untuk mengelola state wizard (langkah). 
    Pastikan Alpine.js sudah terinstal di proyek Anda.
--}}
<div class="max-w-4xl mx-auto" x-data="formWizard()">

    <div class="text-center mb-8">
        <h1 class="text-3xl font-bold text-slate-800">Buat Surat Keterangan Berobat (SKB)</h1>
        <p class="mt-2 text-base text-slate-500">Ikuti langkah-langkah di bawah ini untuk menerbitkan surat baru.</p>
    </div>

    <div class="border border-slate-200 bg-white rounded-xl shadow-sm p-4 mb-6">
        <div class="relative">
            <div class="absolute top-1/2 left-0 w-full h-0.5 bg-slate-200">
                <div class="absolute top-0 left-0 h-0.5 bg-blue-600 transition-all duration-500" :style="`width: ${progress}%`"></div>
            </div>
            <div class="relative flex justify-between">
                <template x-for="(step, index) in steps" :key="index">
                    <div class="flex flex-col items-center">
                        <button @click="currentStep = index + 1" :disabled="index + 1 > maxStep"
                                class="w-8 h-8 rounded-full flex items-center justify-center transition-colors duration-300"
                                :class="{
                                    'bg-blue-600 text-white': currentStep === index + 1,
                                    'bg-white border-2 border-blue-600 text-blue-600': currentStep > index + 1,
                                    'bg-slate-200 text-slate-400': currentStep < index + 1 && maxStep <= index + 1,
                                    'bg-white border-2 border-slate-300 text-slate-500': currentStep < index + 1 && maxStep > index + 1
                                }">
                            <span x-show="currentStep <= index + 1" x-text="index + 1"></span>
                            <svg x-show="currentStep > index + 1" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                        </button>
                        <span class="text-xs mt-2" :class="currentStep >= index + 1 ? 'text-slate-700 font-semibold' : 'text-slate-500'" x-text="step"></span>
                    </div>
                </template>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('outlet.results.store.skb') }}" id="skb-form">
        @csrf
        
        <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-8">
            <div x-show="currentStep === 1" class="space-y-6 animate-fade-in">
                <h3 class="text-lg font-semibold text-slate-800 border-b border-slate-200 pb-3">Langkah 1: Informasi Pasien & Kunjungan</h3>
                
                <div>
                    <label for="patient_name" class="block text-sm font-medium leading-6 text-slate-700 mb-1.5">Nama Pasien</label>
                    <input type="text" name="patient_name" id="patient_name" autocomplete="off" class="w-full rounded-md border-slate-300 shadow-sm" placeholder="Cari atau ketik baru...">
                    <input type="hidden" name="patient_id" id="patient_id">
                    <div id="suggestions" class="absolute z-50 bg-white border mt-1 rounded-md shadow-lg hidden max-h-60 overflow-y-auto w-full"></div>
                </div>
                 </div>

            <div x-show="currentStep === 2" class="space-y-6 animate-fade-in">
                <h3 class="text-lg font-semibold text-slate-800 border-b border-slate-200 pb-3">Langkah 2: Detail Pemeriksaan & Diagnosa</h3>
                
                 <div>
                    <label for="icd_search" class="text-sm font-medium text-slate-700">Kode Diagnosa (ICD-10) <span class="text-xs text-slate-500">(Opsional)</span></label>
                    <input type="text" id="icd_search" name="icd_name" autocomplete="off" class="mt-1 w-full rounded-md border-slate-300 shadow-sm" placeholder="Cari kode atau nama diagnosa...">
                    <input type="hidden" name="icd_master_id" id="icd_master_id">
                    <div id="icd_suggestions" class="absolute z-50 bg-white border mt-1 rounded-md shadow-lg hidden max-h-40 overflow-y-auto w-full text-sm"></div>
                </div>
                </div>

            <div x-show="currentStep === 3" class="space-y-6 animate-fade-in">
                 <h3 class="text-lg font-semibold text-slate-800 border-b border-slate-200 pb-3">Langkah 3: Opsi Tambahan & Finalisasi</h3>

                </div>
            
            <div class="mt-8 pt-5 border-t border-slate-200 flex justify-between">
                <button type="button" @click="prevStep()" x-show="currentStep > 1" class="px-5 py-2 bg-slate-200 text-slate-700 rounded-lg text-sm font-medium hover:bg-slate-300 transition">
                    &larr; Kembali
                </button>
                <div x-show="currentStep === 1" class="w-full text-right"></div> <button type="button" @click="nextStep()" x-show="currentStep < 3" class="px-5 py-2 bg-blue-600 text-white rounded-lg text-sm font-semibold hover:bg-blue-700 transition">
                    Selanjutnya &rarr;
                </button>
                
                <button type="submit" id="submitBtn" x-show="currentStep === 3" class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-green-600 text-white font-bold rounded-lg shadow-md hover:bg-green-700 transition-all">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <span>Simpan & Proses Surat</span>
                </button>
            </div>
        </div>
    </form>
</div>

@endsection

@push('scripts')
{{-- Script untuk mengontrol wizard multi-langkah --}}
<script>
    function formWizard() {
        return {
            currentStep: 1,
            maxStep: 1, // Melacak langkah terjauh yang pernah diakses
            steps: ['Pasien & Kunjungan', 'Detail Pemeriksaan', 'Opsi & Finalisasi'],
            
            // Menghitung persentase progress bar
            get progress() {
                // Progress adalah 0% di langkah 1, 50% di langkah 2, 100% di langkah 3
                return ((this.currentStep - 1) / (this.steps.length - 1)) * 100;
            },

            // Pindah ke langkah berikutnya
            nextStep() {
                if (this.currentStep < this.steps.length) {
                    this.currentStep++;
                    // Update langkah terjauh jika kita maju ke langkah baru
                    if (this.currentStep > this.maxStep) {
                        this.maxStep = this.currentStep;
                    }
                }
            },

            // Kembali ke langkah sebelumnya
            prevStep() {
                if (this.currentStep > 1) {
                    this.currentStep--;
                }
            },
            
            // Pindah ke langkah spesifik (hanya jika sudah pernah diakses)
            goToStep(step) {
                if (step <= this.maxStep) {
                    this.currentStep = step;
                }
            }
        }
    }
</script>

{{-- Script fungsionalitas yang sudah ada (Live Search, Submit, dll) --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    // ============ PASIEN LIVE SEARCH ============
    const patientInput = document.getElementById('patient_name');
    const suggestionsBox = document.getElementById('suggestions');
    const patientIdInput = document.getElementById('patient_id');
    const isNewPatientCheckbox = document.getElementById('is_new_patient');

    patientInput?.addEventListener('input', function () {
        const query = this.value.trim();

        // Selalu reset ID dan status pasien baru setiap kali ada input baru
        patientIdInput.value = '';
        if (isNewPatientCheckbox) {
            isNewPatientCheckbox.checked = true;
            isNewPatientCheckbox.disabled = false;
        }
        
        if (query.length < 1) {
            suggestionsBox.classList.add('hidden');
            return;
        }
        
        fetch(`/outlet/patients/live-search?q=${encodeURIComponent(query)}`)
            .then(res => res.json())
            .then(data => {
                suggestionsBox.innerHTML = '';
                if (data.length > 0) {
                    suggestionsBox.classList.remove('hidden');
                    data.forEach(item => {
                        const div = document.createElement('div');
                        div.className = 'px-4 py-2 hover:bg-blue-50 cursor-pointer border-b border-slate-100 text-sm';
                        div.innerHTML = `<div class="font-semibold text-slate-800">${item.name}</div><div class="text-xs text-slate-500">NIK: ${item.nik || '-'}</div>`;
                        div.onclick = () => selectPatient(item);
                        suggestionsBox.appendChild(div);
                    });
                } else {
                    suggestionsBox.classList.add('hidden');
                }
            });
    });

    function selectPatient(data) {
        patientInput.value = data.name; // Ambil nama saja
        patientIdInput.value = data.id;
        // Gunakan querySelector untuk mengisi semua field terkait
        document.querySelector('input[name="dob"]').value = data.birth_date || '';
        document.querySelector('select[name="gender"]').value = data.gender || '';
        document.querySelector('input[name="phone"]').value = data.phone || '';
        document.querySelector('input[name="address"]').value = data.address || '';
        document.querySelector('input[name="nik"]').value = data.nik || '';
        document.querySelector('input[name="identity"]').value = data.identity || '';

        // Update status checkbox "pasien baru"
        if (isNewPatientCheckbox) {
            isNewPatientCheckbox.checked = false;
            isNewPatientCheckbox.disabled = true;
        }

        suggestionsBox.classList.add('hidden');
    }
    
    // Sembunyikan suggestion box jika klik di luar area
    document.addEventListener('click', function(e) {
        if (suggestionsBox && !patientInput.contains(e.target) && !suggestionsBox.contains(e.target)) {
            suggestionsBox.classList.add('hidden');
        }
    });

    // ============ PERUSAHAAN LIVE SEARCH ============
    const companyInput = document.getElementById('company_search');
    const companyIdInput = document.getElementById('company_id');
    const companySuggestions = document.getElementById('company_suggestions');

    companyInput?.addEventListener('input', function () {
        const query = this.value.trim();
        companyIdInput.value = ''; // Reset ID saat mengetik
        if (query.length < 1) {
            companySuggestions.classList.add('hidden');
            return;
        }

        fetch(`/outlet/companies/live-search?q=${encodeURIComponent(query)}`)
            .then(res => res.json())
            .then(data => {
                companySuggestions.innerHTML = '';
                if (data.length > 0) {
                    companySuggestions.classList.remove('hidden');
                    data.forEach(item => {
                        const div = document.createElement('div');
                        div.className = 'px-4 py-2 hover:bg-blue-50 cursor-pointer border-b border-slate-100 text-sm';
                        div.textContent = item.name;
                        div.onclick = () => selectCompany(item);
                        companySuggestions.appendChild(div);
                    });
                } else {
                    companySuggestions.classList.add('hidden');
                }
            });
    });

    function selectCompany(company) {
        companyInput.value = company.name;
        companyIdInput.value = company.id;
        companySuggestions.classList.add('hidden');
    }
     document.addEventListener('click', function(e) {
        if (companySuggestions && !companyInput.contains(e.target) && !companySuggestions.contains(e.target)) {
            companySuggestions.classList.add('hidden');
        }
    });

    // ============ ICD-10 LIVE SEARCH ============
    const icdInput = document.getElementById('icd_search');
    const icdSuggestions = document.getElementById('icd_suggestions');
    const icdMasterId = document.getElementById('icd_master_id');

    icdInput?.addEventListener('input', function () {
        const query = this.value.trim();
        icdMasterId.value = ''; // Reset ID saat mengetik
        if (query.length < 2) {
            icdSuggestions.classList.add('hidden');
            return;
        }
        fetch(`/outlet/icd10/live-search?q=${encodeURIComponent(query)}`)
            .then(res => res.json())
            .then(data => {
                icdSuggestions.innerHTML = '';
                if (data.length > 0) {
                    icdSuggestions.classList.remove('hidden');
                    data.forEach(item => {
                        const div = document.createElement('div');
                        div.className = 'px-4 py-2 hover:bg-blue-50 cursor-pointer border-b border-slate-100 text-sm';
                        div.innerHTML = `<div class="font-semibold text-slate-700">${item.code}</div><div class="text-slate-500">${item.title}</div>`;
                        div.onclick = () => selectIcd(item);
                        icdSuggestions.appendChild(div);
                    });
                } else {
                    icdSuggestions.classList.add('hidden');
                }
            });
    });

    function selectIcd(item) {
        icdInput.value = `${item.code} - ${item.title}`;
        icdMasterId.value = item.id;
        icdSuggestions.classList.add('hidden');
    }
    document.addEventListener('click', function(e) {
        if (icdSuggestions && !icdInput.contains(e.target) && !icdSuggestions.contains(e.target)) {
            icdSuggestions.classList.add('hidden');
        }
    });

    // ============ NOTIFIKASI CHECKBOX ============
    document.getElementById('send_notif_email')?.addEventListener('change', function () {
        document.getElementById('email_input_wrapper')?.classList.toggle('hidden', !this.checked);
    });
    document.getElementById('send_notif_wa')?.addEventListener('change', function () {
        document.getElementById('wa_input_wrapper')?.classList.toggle('hidden', !this.checked);
    });

    // ============ LOADING ON SUBMIT ============
    document.getElementById('skb-form')?.addEventListener('submit', function () {
        const btn = document.getElementById('submitBtn');
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = `<svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> <span>Memproses...</span>`;
        }
    });
});
</script>
@endpush