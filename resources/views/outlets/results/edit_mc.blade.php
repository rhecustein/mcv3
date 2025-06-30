@extends('layouts.app')

@section('content')
<div class="bg-blue-50 border border-blue-200 p-6 rounded-xl shadow-md mb-6 relative">
    <div class="text-center mb-8 animate-fade-in-down">
        <h2 class="text-3xl font-extrabold text-gray-800 tracking-tight mb-2">✏️ Edit Surat Istirahat</h2>
        <p class="text-base text-gray-600">Outlet: <span class="text-blue-700 font-semibold">{{ $outlet->name }}</span></p>
    </div>

    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
            <strong>Terjadi Kesalahan:</strong>
            <ul class="mt-2 list-disc list-inside text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('outlet.results.mc.update', $result->id) }}" class="space-y-6" id="skb-form">
        @csrf
        @method('PUT')

        <h4 class="text-lg font-semibold text-gray-700">Perusahaan</h4>
        <div class="grid grid-cols-12 gap-4">
            <div class="col-span-12 md:col-span-6 relative">
                <label class="text-sm font-medium text-gray-700">Cari Perusahaan</label>
                <input type="text" id="company_search" name="company_name"
                       class="mt-1 block w-full border rounded px-3 py-2"
                       value="{{ $result->company->name ?? '' }}"
                       placeholder="Ketik nama perusahaan...">
                <input type="hidden" name="company_id" id="company_id" value="{{ $result->company_id }}">
                <div id="company_suggestions" class="absolute z-50 bg-white border mt-1 rounded shadow-md hidden max-h-40 overflow-y-auto w-full text-sm"></div>
            </div>
        </div>

        <h4 class="text-lg font-semibold text-gray-700">Data Pasien</h4>
        <div class="grid grid-cols-12 gap-4 items-end">
            <div class="col-span-12 md:col-span-6 relative">
                <label>Nama Pasien</label>
                <input type="text" name="patient_name" id="patient_name"
                       value="{{ $result->patient->full_name }}"
                       class="border rounded px-3 py-2 w-full">
                <input type="hidden" name="patient_id" id="patient_id" value="{{ $result->patient_id }}">
            </div>
            <div class="col-span-12 md:col-span-3">
                <label>Tanggal Lahir</label>
                <input type="date" name="dob" value="{{ $result->patient->birth_date }}" class="border rounded px-3 py-2 w-full">
            </div>
            <div class="col-span-12 md:col-span-3">
                <label>Jenis Kelamin</label>
                <select name="gender" class="border rounded px-3 py-2 w-full">
                    <option value="L" @selected($result->patient->gender == 'L')>Laki-laki</option>
                    <option value="P" @selected($result->patient->gender == 'P')>Perempuan</option>
                </select>
            </div>
        </div>

        <h4 class="text-lg font-semibold text-gray-700">Keterangan Surat</h4>
        <div class="grid grid-cols-12 gap-4">
            <div class="col-span-12 md:col-span-2">
                <label>Durasi (hari)</label>
                <input type="number" name="duration" id="duration"
                       class="border rounded px-3 py-2 w-full"
                       value="{{ $result->duration }}">
            </div>
            <div class="col-span-12 md:col-span-3">
                <label>Tanggal Mulai</label>
                <input type="date" name="start_date" id="start_date"
                       class="border rounded px-3 py-2 w-full"
                       value="{{ $result->start_date }}">
            </div>
            <div class="col-span-12 md:col-span-3">
                <label>Tanggal Selesai</label>
                <input type="date" name="end_date" id="end_date"
                       class="border rounded px-3 py-2 w-full"
                       value="{{ $result->end_date }}">
            </div>
            <div class="col-span-12 md:col-span-4">
                <label>Dokter Pemeriksa</label>
                <select name="doctor_id" class="select2 mt-1 w-full border rounded px-3 py-2">
                    @foreach ($doctors as $doctor)
                        <option value="{{ $doctor->id }}" @selected($doctor->id == $result->doctor_id)>
                            {{ $doctor->user->name }} - {{ $doctor->spesialist }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <h4 class="text-lg font-semibold text-gray-700">Kontak & Identitas</h4>
        <div class="grid grid-cols-12 gap-4">
            <div class="col-span-12 md:col-span-4">
                <label>NIK</label>
                <input type="text" name="nik" class="border rounded px-3 py-2 w-full" value="{{ $result->patient->nik }}">
            </div>
            <div class="col-span-12 md:col-span-4">
                <label>Nomor Telepon</label>
                <input type="text" name="phone" class="border rounded px-3 py-2 w-full" value="{{ $result->patient->phone }}">
            </div>
            <div class="col-span-12 md:col-span-4">
                <label>No Identitas</label>
                <input type="text" name="identity" class="border rounded px-3 py-2 w-full" value="{{ $result->patient->identity }}">
            </div>
        </div>

        <div class="grid grid-cols-12 gap-4">
            <div class="col-span-12 md:col-span-6">
                <label>Alamat</label>
                <input type="text" name="address" class="border rounded px-3 py-2 w-full" value="{{ $result->patient->address }}">
            </div>
            <div class="col-span-12 md:col-span-6 relative">
                <label>Kode Diagnosa (ICD-10)</label>
                <input type="text" id="icd_search" name="icd_name" autocomplete="off"
                       class="border rounded px-3 py-2 w-full"
                       value="{{ $result->diagnosis?->icd->code . ' - ' . $result->diagnosis?->icd->title }}">
                <input type="hidden" name="icd_master_id" id="icd_master_id" value="{{ $result->diagnosis?->icd_master_id }}">
                <div id="icd_suggestions" class="absolute z-50 bg-white border mt-1 rounded shadow-md hidden max-h-40 overflow-y-auto w-full text-sm"></div>
                <label class="block mt-4">Deskripsi Tambahan</label>
                <textarea name="description" id="description" rows="2" class="border rounded px-3 py-2 w-full">{{ $result->diagnosis?->description }}</textarea>
            </div>
        </div>

        <div class="text-right">
            <button type="submit" id="submitBtn" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-2 rounded relative">
                <span id="btnText">Update</span>
                <span id="btnLoading" class="hidden absolute left-1/2 top-1/2 transform -translate-x-1/2 -translate-y-1/2">
                    <svg class="animate-spin h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                </span>
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const debounce = (fn, delay = 300) => {
        let timeout;
        return (...args) => {
            clearTimeout(timeout);
            timeout = setTimeout(() => fn(...args), delay);
        };
    };

    function setupLiveSearch({ inputId, suggestionsId, url, onSelect, minLength = 1 }) {
        const input = document.getElementById(inputId);
        const suggestionsBox = document.getElementById(suggestionsId);

        if (!input || !suggestionsBox) return;

        input.addEventListener('input', debounce(() => {
            const query = input.value.trim();
            if (query.length < minLength) {
                suggestionsBox.classList.add('hidden');
                return;
            }

            fetch(`${url}?q=${encodeURIComponent(query)}`)
                .then(res => res.json())
                .then(data => {
                    suggestionsBox.innerHTML = '';
                    if (data.length === 0) {
                        suggestionsBox.classList.add('hidden');
                        return;
                    }

                    suggestionsBox.classList.remove('hidden');
                    data.forEach(item => {
                        const div = document.createElement('div');
                        div.className = 'px-3 py-2 hover:bg-blue-100 cursor-pointer border-b text-sm';
                        div.innerHTML = onSelect.display(item);
                        div.onclick = () => {
                            onSelect.select(item);
                            suggestionsBox.classList.add('hidden');
                        };
                        suggestionsBox.appendChild(div);
                    });
                });
        }));
    }

    // Pasien
    setupLiveSearch({
        inputId: 'patient_name',
        suggestionsId: 'suggestions',
        url: '/outlet/patients/live-search',
        onSelect: {
            display: item => `<strong>${item.name}</strong>`,
            select: item => {
                document.getElementById('patient_name').value = item.name.split(' - ')[0];
                document.getElementById('patient_id').value = item.id;
                document.querySelector('input[name="dob"]').value = item.dob || '';
                document.querySelector('select[name="gender"]').value = item.gender || '';
                document.querySelector('input[name="phone"]').value = item.phone_number || '';
                document.querySelector('input[name="address"]').value = item.address || '';
                const isNew = document.getElementById('is_new_patient');
                if (isNew) {
                    isNew.checked = false;
                    isNew.disabled = true;
                }
            }
        }
    });

    // Perusahaan
    setupLiveSearch({
        inputId: 'company_search',
        suggestionsId: 'company_suggestions',
        url: '/outlet/companies/live-search',
        onSelect: {
            display: item => `${item.name}`,
            select: item => {
                document.getElementById('company_search').value = item.name;
                document.getElementById('company_id').value = item.id;
            }
        }
    });

    // Diagnosa ICD
    setupLiveSearch({
        inputId: 'icd_search',
        suggestionsId: 'icd_suggestions',
        url: '/outlet/icd10/live-search',
        minLength: 2,
        onSelect: {
            display: item => `<strong>${item.code}</strong> - ${item.title}`,
            select: item => {
                document.getElementById('icd_search').value = `${item.code} - ${item.title}`;
                document.getElementById('icd_master_id').value = item.id;
            }
        }
    });

    // Auto End Date
    const startInput = document.getElementById('start_date');
    const durationInput = document.getElementById('duration');
    const endInput = document.getElementById('end_date');

    function updateEndDate() {
        const start = new Date(startInput.value);
        const duration = parseInt(durationInput.value);
        if (!isNaN(duration) && startInput.value) {
            const endDate = new Date(start);
            endDate.setDate(endDate.getDate() + duration - 1);
            endInput.value = endDate.toISOString().split('T')[0];
        }
    }

    startInput?.addEventListener('change', updateEndDate);
    durationInput?.addEventListener('input', updateEndDate);

    // Loader on Submit
    const form = document.getElementById('skb-form');
    form?.addEventListener('submit', () => {
        const btn = document.getElementById('submitBtn');
        document.getElementById('btnText')?.classList.add('opacity-0');
        document.getElementById('btnLoading')?.classList.remove('hidden');
        btn.disabled = true;
    });
});
</script>
@endpush

