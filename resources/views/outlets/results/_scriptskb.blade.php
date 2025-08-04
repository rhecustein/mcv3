<script>
// ==================== UTILITY FUNCTIONS (HELPERS) ====================

/**
 * Membuat elemen DOM dengan aman.
 * @param {string} tag - Tag HTML (e.g., 'div', 'p', 'svg').
 * @param {string[]} classNames - Array dari nama kelas CSS.
 * @param {Object} attributes - Objek dari atribut (e.g., { 'data-id': 1 }).
 * @param {string} textContent - Teks untuk elemen.
 * @returns {HTMLElement} Elemen yang dibuat.
 */
function createDOMElement(tag, classNames = [], attributes = {}, textContent = '') {
    const el = document.createElement(tag);
    el.classList.add(...classNames);
    if (textContent) el.textContent = textContent;
    for (const key in attributes) {
        el.setAttribute(key, attributes[key]);
    }
    return el;
}

/**
 * Membuat elemen ikon FontAwesome.
 * @param {string[]} iconClasses - Array kelas untuk ikon (e.g., ['fas', 'fa-phone']).
 * @returns {HTMLElement} Elemen <i> untuk ikon.
 */
function createIcon(iconClasses) {
    const icon = document.createElement('i');
    icon.classList.add(...iconClasses);
    return icon;
}

/**
 * Menampilkan notifikasi toast dengan aman.
 * @param {string} message - Pesan yang akan ditampilkan.
 * @param {'success' | 'error' | 'info'} type - Jenis toast.
 */
function showToast(message, type = 'info') {
    const colors = {
        success: 'bg-green-500',
        error: 'bg-red-500',
        info: 'bg-blue-500'
    };
    const icons = {
        success: ['fas', 'fa-check-circle'],
        error: ['fas', 'fa-times-circle'],
        info: ['fas', 'fa-info-circle']
    };

    const toast = createDOMElement('div', ['fixed', 'top-4', 'right-4', colors[type], 'text-white', 'px-6', 'py-3', 'rounded-lg', 'shadow-lg', 'z-50', 'animate-fade-in', 'flex', 'items-center']);
    const icon = createIcon([...icons[type], 'mr-3']);
    const text = createDOMElement('span', [], {}, message);

    toast.appendChild(icon);
    toast.appendChild(text);
    document.body.appendChild(toast);

    setTimeout(() => {
        toast.classList.add('animate-fade-out');
        toast.addEventListener('animationend', () => toast.remove());
    }, 3000);
}


// ==================== ALPINE.JS FORM WIZARD ====================
document.addEventListener('alpine:init', () => {
    Alpine.data('formWizard', () => ({
        currentStep: 1,
        maxStep: 1,
        steps: [
            { title: 'Pasien & Kunjungan', subtitle: 'Data dasar pasien' },
            { title: 'Detail & Pemeriksaan', subtitle: 'Informasi medis' },
            { title: 'Dokter & Notifikasi', subtitle: 'Finalisasi surat' }
        ],
        formElements: {},

        init() {
            // Cache DOM elements for validation
            this.formElements = {
                patient_name: document.getElementById('patient_name'),
                company_search: document.getElementById('company_search'),
                company_id: document.getElementById('company_id'),
                date: document.getElementById('date'),
                time: document.getElementById('time'),
                dob: document.getElementById('dob'),
                gender: document.getElementById('gender'),
                icd_search: document.getElementById('icd_search'),
                icd_master_id: document.getElementById('icd_master_id'),
                doctor_id: document.getElementById('doctor_id'),
            };

            // Restore step if validation errors exist from backend
            @if ($errors->any())
                @if ($errors->hasAny(['dob', 'gender', 'icd_master_id', 'diagnosis_name']))
                    this.currentStep = 2;
                    this.maxStep = 2;
                @elseif ($errors->hasAny(['doctor_id', 'whatsapp_number', 'email_address']))
                    this.currentStep = 3;
                    this.maxStep = 3;
                @endif
            @endif
        },

        get progress() {
            return ((this.currentStep - 1) / (this.steps.length - 1)) * 100;
        },

        nextStep() {
            if (!this.validateCurrentStep()) {
                showToast('Mohon lengkapi semua kolom yang wajib diisi, pilih data yang sesuai', 'error');
                return;
            }
            if (this.currentStep < this.steps.length) {
                this.currentStep++;
                if (this.currentStep > this.maxStep) {
                    this.maxStep = this.currentStep;
                }
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        },

        prevStep() {
            if (this.currentStep > 1) {
                this.currentStep--;
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        },

        goToStep(step) {
            if (step <= this.maxStep && step !== this.currentStep) {
                this.currentStep = step;
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        },

        // --- Validation Logic ---
        validateCurrentStep() {
            const validations = {
                1: () => this.validateStep1(),
                2: () => this.validateStep2(),
                3: () => this.validateStep3()
            };
            return validations[this.currentStep] ? validations[this.currentStep]() : true;
        },

        _validateField(element, condition) {
            if (condition) {
                element.classList.remove('border-red-500', 'focus:ring-red-500');
                element.classList.add('border-gray-300', 'focus:ring-blue-500');
                return true;
            } else {
                element.classList.add('border-red-500', 'focus:ring-red-500');
                element.classList.remove('border-gray-300', 'focus:ring-blue-500');
                return false;
            }
        },

        validateStep1() {
            const { patient_name, company_search, company_id, date, time } = this.formElements;
            let isValid = true;
            
            // Validasi nama pasien
            isValid = this._validateField(patient_name, patient_name.value.trim()) && isValid;
            
            // Validasi perusahaan - HARUS memilih dari dropdown, tidak boleh free text
            const companyValid = company_id.value && company_search.value.trim();
            isValid = this._validateField(company_search, companyValid) && isValid;
            
            // Jika ada text di company_search tapi tidak ada company_id, berarti belum memilih dari dropdown
            if (company_search.value.trim() && !company_id.value) {
                this._validateField(company_search, false);
                showToast('Silakan pilih perusahaan dari daftar yang tersedia.', 'error');
                isValid = false;
            }
            
            isValid = this._validateField(date, date.value) && isValid;
            isValid = this._validateField(time, time.value) && isValid;
            return isValid;
        },

        validateStep2() {
            const { dob, gender, icd_search, icd_master_id } = this.formElements;
            let isValid = true;
            isValid = this._validateField(dob, dob.value) && isValid;
            isValid = this._validateField(gender, gender.value) && isValid;
            isValid = this._validateField(icd_search, !icd_search.value.trim() || icd_master_id.value) && isValid;
            return isValid;
        },

        validateStep3() {
            const { doctor_id } = this.formElements;
            return this._validateField(doctor_id, doctor_id.value);
        }
    }));
});


// ==================== MAIN SCRIPT LOGIC ====================
document.addEventListener('DOMContentLoaded', function() {
    
    // --- Utility: Debounce Function ---
    function debounce(func, delay) {
        let timeout;
        return function(...args) {
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(this, args), delay);
        };
    }

    // --- Generic Search Component Setup ---
    function setupSearchComponent({ inputEl, suggestionsEl, idEl, route, renderItemFn, onSelect, noResultMsg, onNewOption, restrictToSelection = false }) {
        let selectedFromDropdown = false;
        
        const searchFn = debounce(async (query) => {
            if (query.length < 2) {
                suggestionsEl.innerHTML = '';
                return;
            }

            suggestionsEl.innerHTML = '<div class="p-4 text-center text-gray-500">Mencari...</div>';

            try {
                const response = await fetch(`${route}?q=${encodeURIComponent(query)}`);
                if (!response.ok) throw new Error('Network response was not ok');
                const data = await response.json();

                suggestionsEl.innerHTML = '';

                if (data.length > 0) {
                    data.forEach(item => {
                        const itemEl = renderItemFn(item);
                        itemEl.addEventListener('click', () => {
                            selectedFromDropdown = true;
                            onSelect(item);
                        });
                        suggestionsEl.appendChild(itemEl);
                    });
                    if(onNewOption) {
                        const newOptionEl = onNewOption(query);
                        suggestionsEl.appendChild(newOptionEl);
                    }
                } else {
                    suggestionsEl.appendChild(createDOMElement('div', ['p-4', 'text-center', 'text-gray-500'], {}, noResultMsg));
                }
            } catch (error) {
                console.error('Search error:', error);
                suggestionsEl.innerHTML = '';
                suggestionsEl.appendChild(createDOMElement('div', ['p-4', 'text-center', 'text-red-500'], {}, 'Terjadi kesalahan'));
            }
        }, 300);

        inputEl.addEventListener('input', function() {
            const currentValue = this.value.trim();
            
            // Jika restrictToSelection true (untuk company), reset ID dan flag saat mengetik
            if (restrictToSelection) {
                selectedFromDropdown = false;
                idEl.value = '';
            } else {
                idEl.value = '';
            }
            
            if (window.isNewPatientCheckbox) {
                 window.isNewPatientCheckbox.checked = true;
                 window.isNewPatientCheckbox.disabled = false;
            }
            searchFn(currentValue);
        });

        // Validasi khusus untuk restricted input (company)
        if (restrictToSelection) {
            inputEl.addEventListener('blur', function() {
                const currentValue = this.value.trim();
                // Jika ada text tapi tidak dipilih dari dropdown, beri warning visual
                if (currentValue && !selectedFromDropdown && !idEl.value) {
                    this.classList.add('border-yellow-400', 'bg-yellow-50');
                    setTimeout(() => {
                        this.classList.remove('border-yellow-400', 'bg-yellow-50');
                    }, 2000);
                }
            });
        }
    }

    // --- PATIENT SEARCH ---
    const patientInput = document.getElementById('patient_name');
    const patientIdInput = document.getElementById('patient_id');
    window.isNewPatientCheckbox = document.getElementById('is_new_patient');
    const patientFields = {
        dob: document.getElementById('dob'),
        gender: document.getElementById('gender'),
        phone: document.getElementById('phone'),
        address: document.getElementById('address'),
        nik: document.getElementById('nik'),
        identity: document.getElementById('identity')
    };

    function clearPatientFields() {
        Object.values(patientFields).forEach(field => {
            if (field) field.value = '';
        });
    }
    
    function formatDate(dateString) {
        if (!dateString) return '';
        const options = { year: 'numeric', month: 'long', day: 'numeric' };
        return new Date(dateString).toLocaleDateString('id-ID', options);
    }

    function selectPatient(patient) {
        patientInput.value = patient.name;
        patientIdInput.value = patient.id;
        
        if (patientFields.dob && patient.dob) patientFields.dob.value = patient.dob;
        if (patientFields.gender && patient.gender) patientFields.gender.value = patient.gender;
        if (patientFields.phone && patient.phone_number) patientFields.phone.value = patient.phone_number;
        if (patientFields.address && patient.address) patientFields.address.value = patient.address;
        if (patientFields.nik && patient.nik) patientFields.nik.value = patient.nik;
        if (patientFields.identity && patient.identity) patientFields.identity.value = patient.identity;
        
        if (isNewPatientCheckbox) {
            isNewPatientCheckbox.checked = false;
            isNewPatientCheckbox.disabled = true;
        }
        document.getElementById('patient-suggestions').innerHTML = '';
    }

    if (patientInput) {
        setupSearchComponent({
            inputEl: patientInput,
            suggestionsEl: document.getElementById('patient-suggestions'),
            idEl: patientIdInput,
            route: `{{ route('outlet.api.patients.search') }}`,
            onSelect: selectPatient,
            noResultMsg: 'Pasien tidak ditemukan.',
            renderItemFn: (patient) => {
                const el = createDOMElement('div', ['p-4', 'hover:bg-blue-50', 'cursor-pointer', 'border-b']);
                
                const mainDiv = createDOMElement('div', ['flex', 'items-start', 'justify-between']);
                const infoDiv = createDOMElement('div', ['flex-1']);
                const nameP = createDOMElement('p', ['font-semibold', 'text-gray-800'], {}, patient.name);
                const detailsDiv = createDOMElement('div', ['flex', 'items-center', 'gap-3', 'mt-1', 'text-sm', 'text-gray-600']);

                if (patient.phone_number) detailsDiv.append(createIcon(['fas', 'fa-phone', 'text-xs', 'mr-1']), patient.phone_number);
                if (patient.gender) detailsDiv.append(createIcon(['fas', 'fa-venus-mars', 'text-xs', 'mr-1']), patient.gender === 'L' ? 'Laki-laki' : 'Perempuan');
                if (patient.dob) detailsDiv.append(createIcon(['fas', 'fa-birthday-cake', 'text-xs', 'mr-1']), formatDate(patient.dob));

                const idBadge = createDOMElement('span', ['inline-flex', 'items-center', 'px-2.5', 'py-0.5', 'rounded-full', 'text-xs', 'font-medium', 'bg-blue-100', 'text-blue-800'], {}, `ID: ${patient.id}`);
                
                infoDiv.append(nameP, detailsDiv);
                mainDiv.append(infoDiv, idBadge);
                el.appendChild(mainDiv);
                return el;
            },
            onNewOption: (query) => {
                const el = createDOMElement('div', ['p-4', 'bg-blue-50', 'border-t', 'border-blue-200', 'cursor-pointer', 'hover:bg-blue-100']);
                const content = createDOMElement('div', ['flex', 'items-center', 'text-blue-700']);
                content.append(createIcon(['fas', 'fa-user-plus', 'mr-3']), `Daftarkan "${query}" sebagai pasien baru`);
                el.appendChild(content);

                el.addEventListener('click', () => {
                    patientIdInput.value = '';
                    if (isNewPatientCheckbox) {
                        isNewPatientCheckbox.checked = true;
                        isNewPatientCheckbox.disabled = false;
                    }
                    clearPatientFields();
                    document.getElementById('patient-suggestions').innerHTML = '';
                });
                return el;
            }
        });
    }

    isNewPatientCheckbox?.addEventListener('change', function() {
        if (this.checked) {
            patientIdInput.value = '';
            clearPatientFields();
        }
    });

    // --- COMPANY SEARCH (RESTRICTED TO SELECTION ONLY) ---
    const companyInput = document.getElementById('company_search');
    function selectCompany(company) {
        companyInput.value = company.name;
        document.getElementById('company_id').value = company.id;
        document.getElementById('company-suggestions').innerHTML = '';
        
        // Reset visual validation styling
        companyInput.classList.remove('border-yellow-400', 'bg-yellow-50', 'border-red-500', 'focus:ring-red-500');
        companyInput.classList.add('border-gray-300', 'focus:ring-blue-500');
    }

    if (companyInput) {
        setupSearchComponent({
            inputEl: companyInput,
            suggestionsEl: document.getElementById('company-suggestions'),
            idEl: document.getElementById('company_id'),
            route: `{{ route('outlet.api.companies.search') }}`,
            onSelect: selectCompany,
            noResultMsg: 'Perusahaan tidak ditemukan.',
            restrictToSelection: true, // IMPORTANT: This restricts input to dropdown selection only
            renderItemFn: (company) => {
                const el = createDOMElement('div', ['p-3', 'hover:bg-blue-50', 'cursor-pointer', 'border-b']);
                const content = createDOMElement('div', ['flex', 'items-center', 'justify-between']);
                content.append(
                    createDOMElement('span', ['font-medium', 'text-gray-800'], {}, company.name),
                    createDOMElement('span', ['text-xs', 'text-gray-500'], {}, `ID: ${company.id}`)
                );
                el.appendChild(content);
                return el;
            }
        });

        // Tambahan validasi khusus untuk company - clear text jika tidak valid
        companyInput.addEventListener('keydown', function(e) {
            // Jika user menekan Enter dan tidak ada ID yang dipilih, clear input
            if (e.key === 'Enter' && this.value.trim() && !document.getElementById('company_id').value) {
                e.preventDefault();
                showToast('Silakan pilih perusahaan dari daftar yang tersedia.', 'error');
                return false;
            }
        });
    }
    
    // --- ICD-10 SEARCH ---
    const icdInput = document.getElementById('icd_search');
    function selectICD(icd) {
        icdInput.value = `${icd.code} - ${icd.title}`;
        document.getElementById('icd_master_id').value = icd.id;
        document.getElementById('icd-suggestions').innerHTML = '';
    }
    
    if (icdInput) {
        setupSearchComponent({
            inputEl: icdInput,
            suggestionsEl: document.getElementById('icd-suggestions'),
            idEl: document.getElementById('icd_master_id'),
            route: `{{ route('outlet.api.icd10.search') }}`,
            onSelect: selectICD,
            noResultMsg: 'Diagnosis tidak ditemukan.',
            renderItemFn: (icd) => {
                const el = createDOMElement('div', ['p-4', 'hover:bg-purple-50', 'cursor-pointer', 'border-b']);
                const content = createDOMElement('div', ['flex', 'items-start', 'gap-3']);
                const codeBadge = createDOMElement('span', ['inline-flex', 'items-center', 'px-2', 'py-1', 'text-xs', 'font-bold', 'text-purple-700', 'bg-purple-100', 'rounded'], {}, icd.code);
                const title = createDOMElement('p', ['font-medium', 'text-gray-800', 'flex-1'], {}, icd.title);
                content.append(codeBadge, title);
                el.appendChild(content);
                return el;
            }
        });
    }

    // --- FORM SUBMISSION ---
    document.getElementById('skb-form')?.addEventListener('submit', function(e) {
        // Validasi final sebelum submit - khusus untuk company
        const companyId = document.getElementById('company_id').value;
        const companySearch = document.getElementById('company_search').value.trim();
        
        if (companySearch && !companyId) {
            e.preventDefault();
            showToast('Silakan pilih perusahaan dari daftar yang tersedia.', 'error');
            companyInput.focus();
            companyInput.classList.add('border-red-500', 'focus:ring-red-500');
            return false;
        }

        // Simple loading state on final submit button
        const submitBtn = document.getElementById('submit-btn');
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.querySelector('#submit-text').classList.add('hidden');
            submitBtn.querySelector('#submit-loading').classList.remove('hidden');
        }
    });

    // --- COMPANY MODAL LOGIC ---
    document.getElementById('company-form')?.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const form = e.target;
        const saveBtn = document.getElementById('save-company-btn');
        const saveBtnText = document.getElementById('save-company-text');
        const saveBtnLoading = document.getElementById('save-company-loading');
        const errorEl = document.getElementById('company-error');
        const companyNameInput = document.getElementById('modal_company_name');
        const modal = document.getElementById('modalCompany');
        
        // Validasi input kosong
        const companyName = companyNameInput.value.trim();
        if (!companyName) {
            errorEl.textContent = 'Nama perusahaan tidak boleh kosong.';
            errorEl.classList.remove('hidden');
            companyNameInput.focus();
            return;
        }

        // Set loading state
        saveBtn.disabled = true;
        saveBtnText.classList.add('hidden');
        saveBtnLoading.classList.remove('hidden');
        errorEl.classList.add('hidden');

        try {
            // Cek apakah nama perusahaan sudah ada
            const checkResponse = await fetch(`{{ route('outlet.api.companies.search') }}?q=${encodeURIComponent(companyName)}`);
            if (checkResponse.ok) {
                const existingCompanies = await checkResponse.json();
                const duplicateCompany = existingCompanies.find(company => 
                    company.name.toLowerCase() === companyName.toLowerCase()
                );
                
                if (duplicateCompany) {
                    errorEl.textContent = `Perusahaan "${companyName}" sudah terdaftar. Silakan gunakan nama yang berbeda.`;
                    errorEl.classList.remove('hidden');
                    companyNameInput.focus();
                    companyNameInput.select();
                    return;
                }
            }

            // Jika tidak ada duplikat, lanjutkan dengan menyimpan
            const response = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ name: companyName })
            });

            const data = await response.json();

            if (response.ok || response.status === 201) {
                // Berhasil dibuat
                if (data.company) {
                    selectCompany(data.company);
                    modal.close();
                    form.reset();
                    showToast('Perusahaan berhasil ditambahkan dan dipilih.', 'success');
                } else {
                    // Respons sukses tapi tidak ada data company
                    errorEl.textContent = 'Terjadi kesalahan: Data perusahaan tidak ditemukan dalam respons.';
                    errorEl.classList.remove('hidden');
                }
            } else if (response.status === 422) {
                // Validation error dari server
                const errorMessage = data.errors?.name?.[0] || 
                                    data.message || 
                                    'Terjadi kesalahan validasi.';
                
                // Cek jika error tentang duplikat
                if (errorMessage.toLowerCase().includes('sudah ada') || 
                    errorMessage.toLowerCase().includes('already exists') ||
                    errorMessage.toLowerCase().includes('duplicate')) {
                    errorEl.textContent = `Perusahaan "${companyName}" sudah terdaftar. Silakan gunakan nama yang berbeda.`;
                } else {
                    errorEl.textContent = errorMessage;
                }
                errorEl.classList.remove('hidden');
            } else if (response.status === 409) {
                // Conflict - duplicate entry
                errorEl.textContent = `Perusahaan "${companyName}" sudah terdaftar. Silakan gunakan nama yang berbeda.`;
                errorEl.classList.remove('hidden');
            } else {
                // Error lainnya
                errorEl.textContent = data.message || `Terjadi kesalahan server (${response.status}). Silakan coba lagi.`;
                errorEl.classList.remove('hidden');
            }

        } catch (error) {
            console.error('Error saving company:', error);
            
            // Cek apakah ini network error atau parsing error
            if (error.name === 'TypeError' && error.message.includes('fetch')) {
                errorEl.textContent = 'Tidak dapat terhubung ke server. Periksa koneksi internet Anda.';
            } else if (error.name === 'SyntaxError') {
                errorEl.textContent = 'Terjadi kesalahan dalam memproses respons server.';
            } else {
                errorEl.textContent = 'Terjadi kesalahan yang tidak terduga. Silakan coba lagi.';
            }
            errorEl.classList.remove('hidden');
        } finally {
            // Reset loading state
            saveBtn.disabled = false;
            saveBtnText.classList.remove('hidden');
            saveBtnLoading.classList.add('hidden');
        }
    });

    // Clear error ketika user mulai mengetik lagi
    document.getElementById('modal_company_name')?.addEventListener('input', function() {
        const errorEl = document.getElementById('company-error');
        if (!errorEl.classList.contains('hidden')) {
            errorEl.classList.add('hidden');
        }
    });

    // Clear form dan error ketika modal ditutup
    document.getElementById('modalCompany')?.addEventListener('close', function() {
        const form = document.getElementById('company-form');
        const errorEl = document.getElementById('company-error');
        
        if (form) form.reset();
        if (errorEl) errorEl.classList.add('hidden');
    });

    // Clear error ketika user mulai mengetik lagi
    document.getElementById('modal_company_name')?.addEventListener('input', function() {
        const errorEl = document.getElementById('company-error');
        if (!errorEl.classList.contains('hidden')) {
            errorEl.classList.add('hidden');
        }
    });

    // Clear form dan error ketika modal ditutup
    document.getElementById('modalCompany')?.addEventListener('close', function() {
        const form = document.getElementById('company-form');
        const errorEl = document.getElementById('company-error');
        
        if (form) form.reset();
        if (errorEl) errorEl.classList.add('hidden');
    });

    // --- Close dropdowns when clicking outside ---
    document.addEventListener('click', function(e) {
        const patientSuggestions = document.getElementById('patient-suggestions');
        const companySuggestions = document.getElementById('company-suggestions');
        const icdSuggestions = document.getElementById('icd-suggestions');

        if (patientSuggestions && !patientInput.contains(e.target) && !patientSuggestions.contains(e.target)) {
            patientSuggestions.innerHTML = '';
        }
        if (companySuggestions && !companyInput.contains(e.target) && !companySuggestions.contains(e.target)) {
            companySuggestions.innerHTML = '';
        }
        if (icdSuggestions && !icdInput.contains(e.target) && !icdSuggestions.contains(e.target)) {
            icdSuggestions.innerHTML = '';
        }
    });

    // --- Inject Animation CSS ---
    const style = document.createElement('style');
    style.textContent = `
        @keyframes fade-in {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes fade-out {
            from { opacity: 1; transform: translateY(0); }
            to { opacity: 0; transform: translateY(-10px); }
        }
        .animate-fade-in { animation: fade-in 0.3s ease-out forwards; }
        .animate-fade-out { animation: fade-out 0.3s ease-out forwards; }
    `;
    document.head.appendChild(style);
});
</script>