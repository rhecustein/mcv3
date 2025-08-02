<script>
    function formWizard() {
        return {
            currentStep: 1,
            maxStep: 1,
            steps: ['Data Pasien', 'Detail Istirahat', 'Dokter & Finalisasi'],
            init() {
                @if ($errors->any())
                    @if ($errors->hasAny(['doctor_id'])) this.currentStep = 3; this.maxStep = 3;
                    @elseif ($errors->hasAny(['duration', 'start_date', 'end_date', 'icd_master_id', 'icd_name'])) this.currentStep = 2; this.maxStep = 2;
                    @else this.currentStep = 1; this.maxStep = 1;
                    @endif
                @endif
            },
            get progress() { return ((this.currentStep - 1) / (this.steps.length - 1)) * 100; },
            nextStep() { if (this.currentStep < this.steps.length) { this.currentStep++; if (this.currentStep > this.maxStep) this.maxStep = this.currentStep; } },
            prevStep() { if (this.currentStep > 1) { this.currentStep--; } },
            goToStep(step) { if (step <= this.maxStep) { this.currentStep = step; } }
        }
    }

document.addEventListener('DOMContentLoaded', function () {
    function debounce(func, delay) {
        let timeout;
        return (...args) => { clearTimeout(timeout); timeout = setTimeout(() => func.apply(this, args), delay); };
    }

    const patientInput = document.getElementById('patient_name');
    const suggestionsBox = document.getElementById('suggestions');
    const patientIdInput = document.getElementById('patient_id');
    const isNewPatientCheckbox = document.getElementById('is_new_patient');
    const allPatientDetailInputs = {
        dob: document.querySelector('input[name="dob"]'),
        gender: document.querySelector('select[name="gender"]'),
    };
    
    patientInput?.addEventListener('input', debounce(function() {
        const query = this.value.trim();
        patientIdInput.value = '';
        isNewPatientCheckbox.checked = true;
        isNewPatientCheckbox.disabled = false;
        
        const alpineData = document.querySelector('[x-data]').__x.data;
        if (query.length < 2) { 
            suggestionsBox.innerHTML = ''; 
            alpineData.open = false;
            return; 
        }

        fetch(`/outlet/patients/live-search?q=${encodeURIComponent(query)}`)
            .then(res => res.ok ? res.json() : Promise.reject(res))
            .then(data => {
                alpineData.open = true;
                suggestionsBox.innerHTML = '';
                if (data.length > 0) {
                    data.forEach(item => {
                        const div = document.createElement('a');
                        div.href = '#';
                        div.className = 'block px-4 py-2 hover:bg-blue-50 border-b border-slate-100 text-sm';
                        div.innerHTML = `<div class="font-semibold text-slate-800">${item.full_name}</div><div class="text-xs text-slate-500">NIK: ${item.nik || 'N/A'}</div>`;
                        div.onclick = (e) => { e.preventDefault(); selectPatient(item); alpineData.open = false; };
                        suggestionsBox.appendChild(div);
                    });
                } else {
                    suggestionsBox.innerHTML = `<div class="px-4 py-2 text-sm text-slate-500">Pasien tidak ditemukan.</div>`;
                }
            }).catch(err => console.error("Error fetching patient:", err));
    }, 300));
    
    function selectPatient(data) {
        patientInput.value = data.full_name;
        patientIdInput.value = data.id;
        allPatientDetailInputs.dob.value = data.birth_date || '';
        allPatientDetailInputs.gender.value = data.gender || '';
        isNewPatientCheckbox.checked = false;
        isNewPatientCheckbox.disabled = true;
    }

    const companyInput = document.getElementById('company_search');
    const companyIdInput = document.getElementById('company_id');
    const companySuggestions = document.getElementById('company_suggestions');

    companyInput?.addEventListener('input', debounce(function() {
        const query = this.value.trim();
        companyIdInput.value = '';
        if (query.length < 2) { companySuggestions.innerHTML = ''; return; }

        fetch(`/outlet/companies/live-search?q=${encodeURIComponent(query)}`)
            .then(res => res.ok ? res.json() : Promise.reject(res))
            .then(data => {
                const alpineData = companyInput.parentElement.closest('[x-data]').__x.data;
                alpineData.open = true;
                companySuggestions.innerHTML = '';
                if(data.length > 0) {
                    data.forEach(item => {
                        const div = document.createElement('a');
                        div.href = '#';
                        div.className = 'block px-4 py-2 hover:bg-blue-50 border-b border-slate-100 text-sm';
                        div.textContent = item.name;
                        div.onclick = (e) => { e.preventDefault(); selectCompany(item); alpineData.open = false; };
                        companySuggestions.appendChild(div);
                    });
                } else {
                    companySuggestions.innerHTML = '<div class="px-4 py-2 text-sm text-slate-500">Perusahaan tidak ditemukan.</div>';
                }
            }).catch(err => console.error("Error fetching company:", err));
    }, 300));

    function selectCompany(company) {
        companyInput.value = company.name;
        companyIdInput.value = company.id;
    }
    
    window.handleCompanySubmit = function(event) {
        event.preventDefault();
        const form = event.target;
        const submitBtn = form.querySelector('#saveCompanyBtn');
        const btnText = submitBtn.querySelector('#saveCompanyBtnText');
        const btnLoading = submitBtn.querySelector('#saveCompanyBtnLoading');
        const errorEl = form.querySelector('#company_modal_error');

        btnText.classList.add('hidden');
        btnLoading.classList.remove('hidden');
        submitBtn.disabled = true;
        errorEl.classList.add('hidden');

        fetch(form.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ name: form.name.value })
        })
        .then(response => response.json().then(data => ({ status: response.status, body: data })))
        .then(({ status, body }) => {
            if (status >= 200 && status < 300) {
                selectCompany(body.company);
                document.getElementById('modalCompany').close();
                form.reset();
            } else {
                errorEl.textContent = body.errors?.name?.[0] || body.message || 'Terjadi kesalahan.';
                errorEl.classList.remove('hidden');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            errorEl.textContent = 'Gagal menghubungi server.';
            errorEl.classList.remove('hidden');
        })
        .finally(() => {
            btnText.classList.remove('hidden');
            btnLoading.classList.add('hidden');
            submitBtn.disabled = false;
        });
    };

    const icdInput = document.getElementById('icd_search');
    const icdSuggestions = document.getElementById('icd_suggestions');
    const icdMasterId = document.getElementById('icd_master_id');

    icdInput?.addEventListener('input', debounce(function() {
        const query = this.value.trim();
        icdMasterId.value = '';
        if (query.length < 2) { icdSuggestions.innerHTML = ''; return; }

        fetch(`/outlet/icd10/live-search?q=${encodeURIComponent(query)}`)
            .then(res => res.ok ? res.json() : Promise.reject(res))
            .then(data => {
                const alpineData = icdInput.parentElement.closest('[x-data]').__x.data;
                alpineData.open = true;
                icdSuggestions.innerHTML = '';
                if(data.length > 0) {
                    data.forEach(item => {
                        const div = document.createElement('a');
                        div.href = '#';
                        div.className = 'block px-4 py-2 hover:bg-blue-50 border-b border-slate-100 text-sm';
                        div.innerHTML = `<div class="font-semibold text-slate-700">${item.code}</div><div class="text-slate-500">${item.title}</div>`;
                        div.onclick = (e) => { e.preventDefault(); selectIcd(item); alpineData.open = false; };
                        icdSuggestions.appendChild(div);
                    });
                } else {
                    icdSuggestions.innerHTML = '<div class="px-4 py-2 text-sm text-slate-500">ICD-10 tidak ditemukan.</div>';
                }
            }).catch(err => console.error("Error fetching ICD-10:", err));
    }, 300));

    function selectIcd(item) {
        icdInput.value = `${item.code} - ${item.title}`;
        icdMasterId.value = item.id;
    }

    const startInput = document.getElementById('start_date');
    const durationInput = document.getElementById('duration');
    const endInput = document.getElementById('end_date');

    function updateEndDate() {
        if (!startInput.value || !durationInput.value) { endInput.value = ''; return; }
        const startDate = new Date(startInput.value);
        const duration = parseInt(durationInput.value, 10);
        if (!isNaN(duration) && duration > 0) {
            const endDate = new Date(startDate.getTime());
            endDate.setDate(endDate.getDate() + duration - 1);
            endInput.value = endDate.toISOString().split('T')[0];
        } else {
            endInput.value = '';
        }
    }
    durationInput?.addEventListener('input', updateEndDate);
    startInput?.addEventListener('change', updateEndDate);
    updateEndDate();
    
    document.getElementById('mc-form')?.addEventListener('submit', function() {
        const btn = document.getElementById('submitBtn');
        const btnText = document.getElementById('btnText');
        const btnLoading = document.getElementById('btnLoading');
        if(btn) {
            btn.disabled = true;
            btnText.classList.add('hidden');
            btnLoading.classList.remove('hidden');
        }
    });
});
</script>