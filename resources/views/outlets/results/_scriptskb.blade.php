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