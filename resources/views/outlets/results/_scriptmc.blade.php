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

// ==================== ALPINE.JS FORM WIZARD FOR MC ====================
document.addEventListener('alpine:init', () => {
    Alpine.data('formWizard', () => ({
        currentStep: 1,
        maxStep: 1,
        steps: [
            { title: 'Data Pasien', subtitle: 'Informasi pasien & perusahaan' },
            { title: 'Detail Istirahat', subtitle: 'Durasi & diagnosis' },
            { title: 'Dokter & Finalisasi', subtitle: 'Pilih dokter & konfirmasi' }
        ],
        formElements: {},

        init() {
            // Cache DOM elements for validation
            this.formElements = {
                patient_name: document.getElementById('patient_name'),
                company_search: document.getElementById('company_search'),
                company_id: document.getElementById('company_id'),
                dob: document.getElementById('dob'),
                gender: document.getElementById('gender'),
                duration: document.getElementById('duration'),
                start_date: document.getElementById('start_date'),
                icd_search: document.getElementById('icd_search'),
                icd_master_id: document.getElementById('icd_master_id'),
                doctor_id: document.getElementById('doctor_id'),
            };

            // Restore step if validation errors exist from backend
            @if ($errors->any())
                @if ($errors->hasAny(['duration', 'start_date', 'end_date', 'icd_master_id', 'icd_name', 'diagnosis_description']))
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
                showToast('Mohon lengkapi semua kolom yang wajib diisi.', 'error');
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
            if (!element) return true;
            if (condition) {
                element.classList.remove('border-red-500', 'focus:ring-red-500');
                element.classList.add('border-slate-300', 'focus:ring-red-500');
                return true;
            } else {
                element.classList.add('border-red-500', 'focus:ring-red-500');
                element.classList.remove('border-slate-300', 'focus:ring-blue-500');
                return false;
            }
        },

        validateStep1() {
            const { patient_name, dob, gender } = this.formElements;
            let isValid = true;
            isValid = this._validateField(patient_name, patient_name.value.trim()) && isValid;
            isValid = this._validateField(dob, dob.value) && isValid;
            isValid = this._validateField(gender, gender.value) && isValid;
            return isValid;
        },

        validateStep2() {
            const { duration, start_date, icd_search, icd_master_id } = this.formElements;
            let isValid = true;
            isValid = this._validateField(duration, duration.value && parseInt(duration.value) > 0) && isValid;
            isValid = this._validateField(start_date, start_date.value) && isValid;
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
    function setupSearchComponent({ inputEl, suggestionsEl, idEl, route, renderItemFn, onSelect, noResultMsg, onNewOption }) {
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
                        itemEl.addEventListener('click', () => onSelect(item));
                        suggestionsEl.appendChild(itemEl);
                    });
                    // Add "Register New" option if provided
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
            idEl.value = ''; // Reset ID on new input
            if (window.isNewPatientCheckbox) { // Special handling for patient
                 window.isNewPatientCheckbox.checked = true;
                 window.isNewPatientCheckbox.disabled = false;
            }
            searchFn(this.value.trim());
        });
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
            route: '/outlet/api/patients/live-search',
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

    // --- COMPANY SEARCH ---
    const companyInput = document.getElementById('company_search');
    function selectCompany(company) {
        companyInput.value = company.name;
        document.getElementById('company_id').value = company.id;
        document.getElementById('company-suggestions').innerHTML = '';
    }

    if (companyInput) {
        setupSearchComponent({
            inputEl: companyInput,
            suggestionsEl: document.getElementById('company-suggestions'),
            idEl: document.getElementById('company_id'),
            route: '/outlet/api/companies/live-search',
            onSelect: selectCompany,
            noResultMsg: 'Perusahaan tidak ditemukan.',
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
    }
    
    // --- ICD-10 SEARCH ---
    const icdInput = document.getElementById('icd_search');
    const icdMasterId = document.getElementById('icd_master_id');
    const selectedIcdInfoDiv = document.getElementById('selected-icd-info');
    const icdCodeDisplay = document.getElementById('icd-code-display');
    const icdTitleDisplay = document.getElementById('icd-title-display');
    
    function selectICD(icd) {
        icdInput.value = `${icd.code} - ${icd.title}`;
        icdMasterId.value = icd.id;

        // Display full ICD info
        if (icdCodeDisplay) icdCodeDisplay.textContent = icd.code;
        if (icdTitleDisplay) icdTitleDisplay.textContent = icd.title;
        if (selectedIcdInfoDiv) selectedIcdInfoDiv.classList.remove('hidden');

        document.getElementById('icd-suggestions').innerHTML = '';
    }
    
    if (icdInput) {
        setupSearchComponent({
            inputEl: icdInput,
            suggestionsEl: document.getElementById('icd-suggestions'),
            idEl: icdMasterId,
            route: '/outlet/api/icd10/live-search',
            onSelect: selectICD,
            noResultMsg: 'Diagnosis tidak ditemukan.',
            renderItemFn: (icd) => {
                const el = createDOMElement('div', ['p-4', 'hover:bg-orange-50', 'cursor-pointer', 'border-b']);
                const content = createDOMElement('div', ['flex', 'items-start', 'gap-3']);
                const codeBadge = createDOMElement('span', ['inline-flex', 'items-center', 'px-2', 'py-1', 'text-xs', 'font-bold', 'text-orange-700', 'bg-orange-100', 'rounded'], {}, icd.code);
                const title = createDOMElement('p', ['font-medium', 'text-gray-800', 'flex-1'], {}, icd.title);
                content.append(codeBadge, title);
                el.appendChild(content);
                return el;
            }
        });

        // Custom input handler for ICD to handle info display
        icdInput.addEventListener('input', function() {
            const query = this.value.trim();
            icdMasterId.value = '';
            if (selectedIcdInfoDiv) selectedIcdInfoDiv.classList.add('hidden');
        });
    }

    // --- DOCTOR SEARCH ---
    const doctorInput = document.getElementById('doctor_search');
    const doctorIdInput = document.getElementById('doctor_id');
    const doctorSuggestions = document.getElementById('doctor-suggestions');
    const selectedDoctorInfoDiv = document.getElementById('selected-doctor-info');
    const doctorNameDisplayInfo = document.getElementById('doctor-name-display-info');
    const doctorSpecializationDisplayInfo = document.getElementById('doctor-specialization-display-info');
    const doctorLicenseDisplayInfo = document.getElementById('doctor-license-display-info');

    // Restore old doctor data if validation failed and display details
    @if(old('doctor_id') && old('doctor_name_display'))
        if (doctorInput) doctorInput.value = "{{ old('doctor_name_display') }}";
        if (doctorIdInput) doctorIdInput.value = "{{ old('doctor_id') }}";
        // Fetch full doctor details to populate specialization and license
        fetch(`/outlet/api/doctors/live-search?id={{ old('doctor_id') }}`)
            .then(res => res.json())
            .then(data => {
                if (data.length > 0) {
                    selectDoctor(data[0]); // Use selectDoctor to populate all fields
                }
            })
            .catch(error => console.error('Error fetching old doctor details:', error));
    @else
        if (selectedDoctorInfoDiv) selectedDoctorInfoDiv.classList.add('hidden'); // Ensure it's hidden if no old value
    @endif

    const fetchDoctorSuggestions = debounce(function (query) {
        if (query.length >= 2) {
            fetch(`/outlet/api/doctors/live-search?q=${encodeURIComponent(query)}`)
                .then(res => res.json())
                .then(data => {
                    if (doctorSuggestions) doctorSuggestions.innerHTML = '';
                    if (data.length > 0) {
                        data.forEach(item => {
                            const div = document.createElement('div');
                            div.className = 'px-3 py-2 hover:bg-blue-100 cursor-pointer border-b text-sm';
                            div.innerHTML = `<strong>${item.user_name || item.name}</strong> - ${item.specialization || item.specialist || 'Umum'} ${item.license_number ? `(${item.license_number})` : ''}`;
                            div.onclick = () => selectDoctor(item);
                            doctorSuggestions.appendChild(div);
                        });
                    }
                })
                .catch(error => {
                    console.error('Error fetching doctor suggestions:', error);
                    if (doctorSuggestions) doctorSuggestions.innerHTML = '<div class="px-3 py-2 text-red-500">Gagal memuat saran dokter.</div>';
                });
        } else {
            if (doctorSuggestions) doctorSuggestions.innerHTML = '';
        }
    }, 300);

    if (doctorInput) {
        doctorInput.addEventListener('input', function () {
            const query = this.value.trim();
            if (doctorIdInput) doctorIdInput.value = ''; // Clear doctor_id if input changes
            // Also clear the hidden name that's used for old() display
            const doctorNameDisplayHidden = document.querySelector('input[name="doctor_name_display"]');
            if (doctorNameDisplayHidden) doctorNameDisplayHidden.value = '';
            if (selectedDoctorInfoDiv) selectedDoctorInfoDiv.classList.add('hidden'); // Hide details when input changes
            fetchDoctorSuggestions(query);
        });
    }

    function selectDoctor(data) {
        if (doctorInput) doctorInput.value = data.user_name || data.name; // Set display name
        if (doctorIdInput) doctorIdInput.value = data.id;

        // Set the hidden input for old() value persistence
        const doctorNameDisplayHidden = document.querySelector('input[name="doctor_name_display"]');
        if (doctorNameDisplayHidden) {
            doctorNameDisplayHidden.value = data.user_name || data.name;
        }

        // Display full doctor info
        if (doctorNameDisplayInfo) doctorNameDisplayInfo.textContent = data.user_name || data.name;
        if (doctorSpecializationDisplayInfo) doctorSpecializationDisplayInfo.textContent = data.specialization || data.specialist || 'Umum';
        if (doctorLicenseDisplayInfo) doctorLicenseDisplayInfo.textContent = data.license_number || 'Tidak Ada';
        if (selectedDoctorInfoDiv) selectedDoctorInfoDiv.classList.remove('hidden');

        if (doctorSuggestions) doctorSuggestions.innerHTML = ''; // Clear and hide suggestions
        if (doctorInput) doctorInput.focus(); // Keep focus on the input after selection
    }

    // --- DATE CALCULATION FOR MC ---
    const startDateInput = document.getElementById('start_date');
    const durationInput = document.getElementById('duration');
    const endDateInput = document.getElementById('end_date');

    function updateEndDate() {
        if (!startDateInput?.value || !durationInput?.value) {
            if (endDateInput) endDateInput.value = '';
            return;
        }
        
        const startDate = new Date(startDateInput.value);
        const duration = parseInt(durationInput.value, 10);
        
        if (!isNaN(duration) && duration > 0) {
            const endDate = new Date(startDate.getTime());
            endDate.setDate(endDate.getDate() + duration - 1);
            if (endDateInput) endDateInput.value = endDate.toISOString().split('T')[0];
        } else {
            if (endDateInput) endDateInput.value = '';
        }
    }

    durationInput?.addEventListener('input', updateEndDate);
    startDateInput?.addEventListener('change', updateEndDate);
    
    // Initialize end date calculation
    updateEndDate();

    // --- FORM SUBMISSION ---
    document.getElementById('mc-form')?.addEventListener('submit', function(e) {
        const submitBtn = document.getElementById('submit-btn');
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.querySelector('#submit-text')?.classList.add('hidden');
            submitBtn.querySelector('#submit-loading')?.classList.remove('hidden');
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
        
        saveBtn.disabled = true;
        if (saveBtnText) saveBtnText.classList.add('hidden');
        if (saveBtnLoading) saveBtnLoading.classList.remove('hidden');
        errorEl?.classList.add('hidden');

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ name: document.getElementById('modal_company_name').value })
            });

            const data = await response.json();

            if (response.ok) {
                selectCompany(data.company);
                document.getElementById('modalCompany').close();
                form.reset();
                showToast('Perusahaan berhasil ditambahkan.', 'success');
            } else {
                if (errorEl) {
                    errorEl.textContent = data.errors?.name?.[0] || data.message || 'Terjadi kesalahan.';
                    errorEl.classList.remove('hidden');
                }
            }
        } catch (error) {
            console.error('Error saving company:', error);
            if (errorEl) {
                errorEl.textContent = 'Terjadi kesalahan jaringan.';
                errorEl.classList.remove('hidden');
            }
        } finally {
            saveBtn.disabled = false;
            if (saveBtnText) saveBtnText.classList.remove('hidden');
            if (saveBtnLoading) saveBtnLoading.classList.add('hidden');
        }
    });

    // --- Close dropdowns when clicking outside ---
    document.addEventListener('click', function(e) {
        const patientSuggestions = document.getElementById('patient-suggestions');
        const companySuggestions = document.getElementById('company-suggestions');
        const icdSuggestions = document.getElementById('icd-suggestions');
        const doctorSuggestions = document.getElementById('doctor-suggestions');

        if (patientSuggestions && !patientInput?.contains(e.target) && !patientSuggestions.contains(e.target)) {
            patientSuggestions.innerHTML = '';
        }
        if (companySuggestions && !companyInput?.contains(e.target) && !companySuggestions.contains(e.target)) {
            companySuggestions.innerHTML = '';
        }
        if (icdSuggestions && !icdInput?.contains(e.target) && !icdSuggestions.contains(e.target)) {
            icdSuggestions.innerHTML = '';
        }
        if (doctorSuggestions && !doctorInput?.contains(e.target) && !doctorSuggestions.contains(e.target)) {
            doctorSuggestions.innerHTML = '';
        }
    });

    // --- Restore Old Values for MC ---
    // Restore old ICD data if validation failed
    @if(old('icd_master_id') && old('icd_name'))
        if (icdInput) icdInput.value = "{{ old('icd_name') }}";
        if (icdMasterId) icdMasterId.value = "{{ old('icd_master_id') }}";
        if (selectedIcdInfoDiv) {
            selectedIcdInfoDiv.classList.remove('hidden');
            const codePart = "{{ old('icd_name') }}".split(' - ')[0] || '';
            const titlePart = "{{ old('icd_name') }}".split(' - ')[1] || "{{ old('icd_name') }}";
            if (icdCodeDisplay) icdCodeDisplay.textContent = codePart;
            if (icdTitleDisplay) icdTitleDisplay.textContent = titlePart;
        }
    @endif

    // Restore old company data if validation failed
    @if(old('company_id') && old('company_name'))
        if (companyInput) companyInput.value = "{{ old('company_name') }}";
        if (document.getElementById('company_id')) document.getElementById('company_id').value = "{{ old('company_id') }}";
    @endif

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
        @keyframes slide-in-right {
            from { opacity: 0; transform: translateX(20px); }
            to { opacity: 1; transform: translateX(0); }
        }
        .animate-fade-in { animation: fade-in 0.3s ease-out forwards; }
        .animate-fade-out { animation: fade-out 0.3s ease-out forwards; }
        .animate-slide-in-right { animation: slide-in-right 0.3s ease-out forwards; }
        
        /* Custom scrollbar for suggestions */
        #patient-suggestions, #company-suggestions, #icd-suggestions, #doctor-suggestions {
            scrollbar-width: thin;
            scrollbar-color: #cbd5e1 #f1f5f9;
        }
        
        #patient-suggestions::-webkit-scrollbar,
        #company-suggestions::-webkit-scrollbar,
        #icd-suggestions::-webkit-scrollbar,
        #doctor-suggestions::-webkit-scrollbar {
            width: 6px;
        }
        
        #patient-suggestions::-webkit-scrollbar-track,
        #company-suggestions::-webkit-scrollbar-track,
        #icd-suggestions::-webkit-scrollbar-track,
        #doctor-suggestions::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 3px;
        }
        
        #patient-suggestions::-webkit-scrollbar-thumb,
        #company-suggestions::-webkit-scrollbar-thumb,
        #icd-suggestions::-webkit-scrollbar-thumb,
        #doctor-suggestions::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
        }
        
        #patient-suggestions::-webkit-scrollbar-thumb:hover,
        #company-suggestions::-webkit-scrollbar-thumb:hover,
        #icd-suggestions::-webkit-scrollbar-thumb:hover,
        #doctor-suggestions::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    `;
    document.head.appendChild(style);
});
</script>