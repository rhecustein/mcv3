@extends('layouts.app')

@section('content')
<div class="bg-blue-50 border border-blue-200 p-6 rounded-xl shadow-md mb-6 relative">
    <div class="text-center mb-8 animate-fade-in-down">
        <h2 class="text-3xl font-extrabold text-gray-800 tracking-tight mb-2">📝 Buat Surat Keterangan</h2>
        <h4 class="text-1xl font-extrabold text-gray-800 tracking-tight mb-2">Surat Keterangan Berobat (SKB)</h4>
        <p class="text-base text-gray-600">Outlet Aktif: <span class="text-blue-700 font-semibold">{{ $outlet->name }}</span></p>
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

    <form method="POST" action="{{ route('outlet.results.store.skb') }}" class="space-y-6" id="skb-form">
        @csrf

      <!-- Company -->
        <h4 class="text-lg font-semibold text-gray-700">Perusahaan</h4>
         @if(session('success'))
                    <div class="bg-green-100 text-green-800 px-4 py-2 rounded mb-4">
                        {{ session('success') }}
                    </div>
                @endif
        <div class="grid grid-cols-12 gap-4">
            
            <div class="col-span-12 md:col-span-6 relative">
                <label class="text-sm font-medium text-gray-700">Cari Perusahaan</label>
                <input type="text" id="company_search" name="company_name"
                    class="mt-1 block w-full border rounded px-3 py-2"
                    placeholder="Ketik nama perusahaan...">
                <input type="hidden" name="company_id" id="company_id">
                <div id="company_suggestions" class="absolute z-50 bg-white border mt-1 rounded shadow-md hidden max-h-40 overflow-y-auto w-full text-sm"></div>
               
            </div>
            <div class="col-span-12 md:col-span-3 flex items-end">
                <button type="button" onclick="document.getElementById('modalCompany').showModal()"
                        class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded shadow">
                    + Tambah Perusahaan
                </button>
            </div>
        </div>

        <!-- Patient Info -->
        <h4 class="text-lg font-semibold text-gray-700">Data Pasien</h4>
        <div class="grid grid-cols-12 gap-4 items-end">
            <div class="col-span-12 md:col-span-6 relative">
                <label>Nama Pasien</label>
                <input type="text" name="patient_name" id="patient_name" autocomplete="off" class="border rounded px-3 py-2 w-full" placeholder="Cari atau ketik baru...">
                <input type="hidden" name="patient_id" id="patient_id">
                <div id="suggestions" class="absolute z-50 bg-white border mt-1 rounded shadow-md hidden max-h-40 overflow-y-auto w-full"></div>
            </div>

            <div class="col-span-12 md:col-span-3">
                <label>Tanggal Lahir</label>
                <input type="date" name="dob" class="border rounded px-3 py-2 w-full">
            </div>

            <div class="col-span-12 md:col-span-3">
                <label>Jenis Kelamin</label>
                <select name="gender" class="border rounded px-3 py-2 w-full">
                    <option value="">~ Pilih ~</option>
                    <option value="L">Laki-laki</option>
                    <option value="P">Perempuan</option>
                </select>
            </div>

            <div class="col-span-12">
                <label class="inline-flex items-center mt-2">
                    <input type="checkbox" name="is_new_patient" id="is_new_patient" class="rounded border-gray-300 mr-2 text-blue-600">
                    <span class="text-sm text-gray-700">Centang jika ini adalah pasien baru</span>
                </label>
            </div>
        </div>

        <!-- Visit Info -->
        <h4 class="text-lg font-semibold text-gray-700">Keterangan Surat</h4>
        <div class="grid grid-cols-12 gap-4">
            <div class="col-span-12 md:col-span-4">
                <label>Tanggal Berobat</label>
                <input type="date" name="date" class="border rounded px-3 py-2 w-full" value="{{ date('Y-m-d') }}">
            </div>
            <div class="col-span-12 md:col-span-2">
                <label>Jam Berobat</label>
                <input type="time" name="time" class="border rounded px-3 py-2 w-full" value="{{ date('H:i') }}">
            </div>
            <div class="col-span-12 md:col-span-6">
                <label>Dokter Pemeriksa</label>
                <select name="doctor_id" class="select2 mt-1 w-full border rounded px-3 py-2">
                    <option value="">~ Pilih Dokter ~</option>
                    @foreach ($doctors as $doctor)
                        <option value="{{ $doctor->id }}">{{ $doctor->user->name }} - {{ $doctor->spesialist }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Contact Info -->
        <h4 class="text-lg font-semibold text-gray-700">Kontak & Identitas</h4>
        <div class="grid grid-cols-12 gap-4">
            <div class="col-span-12 md:col-span-4">
                <label>NIK KTP  <span class="text-xs text-gray-500">(Opsional)</span></label>
                <input type="text" name="nik" class="border rounded px-3 py-2 w-full">
            </div>
            <div class="col-span-12 md:col-span-4">
                <label>Nomor Telepon</label>
                <input type="text" name="phone" class="border rounded px-3 py-2 w-full">
            </div>
            <div class="col-span-12 md:col-span-4">
                <label>No Identitas / No.Pegawai  <span class="text-xs text-gray-500">(Opsional)</span></label>
                <input type="text" name="identity" class="border rounded px-3 py-2 w-full">
            </div>
        </div>
      <div class="grid grid-cols-12 gap-4">
    <!-- Alamat -->
    <div class="col-span-12 md:col-span-6">
        <label for="address" class="text-sm font-medium text-gray-700">Alamat</label>
        <input type="text" name="address" id="address" class="mt-1 border rounded px-3 py-2 w-full" placeholder="Alamat lengkap pasien">
    </div>

    <!-- ICD-10 -->
    <div class="col-span-12 md:col-span-6 relative">
        <label for="icd_search" class="text-sm font-medium text-gray-700">
            Kode Diagnosa (ICD-10) <span class="text-xs text-gray-500">(Opsional)</span>
        </label>
        <input type="text" id="icd_search" name="icd_name" autocomplete="off"
               class="mt-1 border rounded px-3 py-2 w-full"
               placeholder="Cari kode atau nama diagnosa...">
        <input type="hidden" name="icd_master_id" id="icd_master_id">
        <div id="icd_suggestions"
             class="absolute z-50 bg-white border mt-1 rounded shadow-md hidden max-h-40 overflow-y-auto w-full text-sm"></div>

        <!-- Deskripsi Tambahan -->
        <label for="description" class="text-sm font-medium text-gray-700 mt-4 block">
            Deskripsi Tambahan <span class="text-xs text-gray-500">(Opsional)</span>
        </label>
        <textarea name="description" id="description"
                  rows="2"
                  class="mt-1 border rounded px-3 py-2 w-full"
                  placeholder="Catatan tambahan dari dokter terkait diagnosa ini..."></textarea>
            </div>
        </div>


        <!-- Notifikasi -->
        <!-- <h4 class="text-lg font-semibold text-gray-700">Notifikasi</h4>
        <div class="grid grid-cols-12 gap-4">
            <div class="col-span-6">
                <label class="inline-flex items-center">
                    <input type="checkbox" name="send_notif_email" id="send_notif_email" class="rounded border-gray-300">
                    <span class="ml-2 text-sm">Kirim Email ke Pasien</span>
                </label>
                <div id="email_input_wrapper" class="mt-2 hidden">
                    <input type="email" name="email_to" placeholder="Email pasien..." class="border rounded px-3 py-2 w-full">
                </div>
            </div>
            <div class="col-span-6">
                <label class="inline-flex items-center">
                    <input type="checkbox" name="send_notif_wa" id="send_notif_wa" class="rounded border-gray-300">
                    <span class="ml-2 text-sm">Kirim WhatsApp ke Pasien</span>
                </label>
                <div id="wa_input_wrapper" class="mt-2 hidden">
                    <input type="text" name="wa_to" placeholder="Nomor WA pasien..." class="border rounded px-3 py-2 w-full">
                </div>
            </div>
        </div> -->

        <!-- Submit -->
        <div class="text-right">
            <button type="submit" id="submitBtn" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-2 rounded relative">
                <span id="btnText">Proses</span>
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

<!-- Modal Company -->
<dialog id="modalCompany" class="rounded-md shadow-md w-full max-w-md p-4">
    <form method="POST" action="{{ route('outlet.companies.store') }}">
        @csrf
        <h3 class="text-lg font-bold text-gray-800 mb-4">Tambah Perusahaan Baru</h3>
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Nama Perusahaan</label>
            <input type="text" name="name" class="mt-1 block w-full border rounded px-3 py-2" required>
        </div>
        <div class="flex justify-end gap-2">
            <button type="button" onclick="document.getElementById('modalCompany').close()" class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">Batal</button>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Simpan</button>
        </div>
    </form>
</dialog>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // ============ PASIEN LIVE SEARCH ============
    const patientInput = document.getElementById('patient_name');
    const suggestionsBox = document.getElementById('suggestions');
    const patientIdInput = document.getElementById('patient_id');
    const isNewPatient = document.getElementById('is_new_patient');

    patientInput?.addEventListener('input', function () {
        const query = this.value.trim();
        if (query.length >= 1) {
            fetch(`/outlet/patients/live-search?q=${encodeURIComponent(query)}`)
                .then(res => res.json())
                .then(data => {
                    suggestionsBox.innerHTML = '';
                    if (data.length > 0) {
                        suggestionsBox.classList.remove('hidden');
                        data.forEach(item => {
                            const div = document.createElement('div');
                            div.className = 'px-3 py-2 hover:bg-blue-100 cursor-pointer border-b text-sm';
                            div.innerHTML = `<strong>${item.name}</strong>`;
                            div.onclick = () => selectPatient(item);
                            suggestionsBox.appendChild(div);
                        });
                    } else {
                        suggestionsBox.classList.add('hidden');
                    }
                });
        } else {
            suggestionsBox.classList.add('hidden');
        }

        patientIdInput.value = '';
        isNewPatient.checked = true;
        isNewPatient.disabled = false;
    });

    function selectPatient(data) {
        patientInput.value = data.name.split(' - ')[0];
        patientIdInput.value = data.id;
        document.querySelector('input[name="dob"]').value = data.dob || '';
        document.querySelector('select[name="gender"]').value = data.gender || '';
        document.querySelector('input[name="phone"]').value = data.phone_number || '';
        document.querySelector('input[name="address"]').value = data.address || '';
        suggestionsBox.classList.add('hidden');
        isNewPatient.checked = false;
        isNewPatient.disabled = true;
    }

    // ============ PERUSAHAAN LIVE SEARCH ============
    const companyInput = document.getElementById('company_search');
    const companyIdInput = document.getElementById('company_id');
    const companySuggestions = document.getElementById('company_suggestions');

    companyInput?.addEventListener('input', function () {
        const query = this.value.trim();
        if (query.length >= 1) {
            fetch(`/outlet/companies/live-search?q=${encodeURIComponent(query)}`)
                .then(res => res.json())
                .then(data => {
                    companySuggestions.innerHTML = '';
                    if (data.length > 0) {
                        companySuggestions.classList.remove('hidden');
                        data.forEach(item => {
                            const div = document.createElement('div');
                            div.className = 'px-3 py-2 hover:bg-blue-100 cursor-pointer border-b text-sm';
                            div.textContent = item.name;
                            div.onclick = () => selectCompany(item);
                            companySuggestions.appendChild(div);
                        });
                    } else {
                        companySuggestions.classList.add('hidden');
                    }
                });
        } else {
            companySuggestions.classList.add('hidden');
        }
    });

    function selectCompany(company) {
        companyInput.value = company.name;
        companyIdInput.value = company.id;
        companySuggestions.classList.add('hidden');
    }

    // ============ ICD-10 LIVE SEARCH ============
    const icdInput = document.getElementById('icd_search');
    const icdSuggestions = document.getElementById('icd_suggestions');
    const icdMasterId = document.getElementById('icd_master_id');

    icdInput?.addEventListener('input', function () {
        const query = this.value.trim();
        if (query.length >= 2) {
            fetch(`/outlet/icd10/live-search?q=${encodeURIComponent(query)}`)
                .then(res => res.json())
                .then(data => {
                    icdSuggestions.innerHTML = '';
                    if (data.length > 0) {
                        icdSuggestions.classList.remove('hidden');
                        data.forEach(item => {
                            const div = document.createElement('div');
                            div.className = 'px-3 py-2 hover:bg-blue-100 cursor-pointer border-b text-sm';
                            div.innerHTML = `<strong>${item.code}</strong> - ${item.title}`;
                            div.onclick = () => selectIcd(item);
                            icdSuggestions.appendChild(div);
                        });
                    } else {
                        icdSuggestions.classList.add('hidden');
                    }
                });
        } else {
            icdSuggestions.classList.add('hidden');
        }
    });

    function selectIcd(item) {
        icdInput.value = `${item.code} - ${item.title}`;
        icdMasterId.value = item.id;
        icdSuggestions.classList.add('hidden');
    }

    // ============ NOTIFIKASI ============
    document.getElementById('send_notif_email')?.addEventListener('change', function () {
        document.getElementById('email_input_wrapper')?.classList.toggle('hidden', !this.checked);
    });

    document.getElementById('send_notif_wa')?.addEventListener('change', function () {
        document.getElementById('wa_input_wrapper')?.classList.toggle('hidden', !this.checked);
    });

    // ============ LOADING SUBMIT ============
    document.getElementById('skb-form')?.addEventListener('submit', function () {
        const btn = document.getElementById('submitBtn');
        document.getElementById('btnText')?.classList.add('opacity-0');
        document.getElementById('btnLoading')?.classList.remove('hidden');
        btn.disabled = true;
    });
});
</script>
@endpush
