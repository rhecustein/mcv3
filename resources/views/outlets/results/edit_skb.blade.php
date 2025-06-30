@extends('layouts.app')

@section('content')
<div class="bg-blue-50 border border-blue-200 p-6 rounded-xl shadow-md mb-6 relative">
    <div class="text-center mb-8 animate-fade-in-down">
        <h2 class="text-3xl font-extrabold text-gray-800 tracking-tight mb-2">✏️ Edit Surat Keterangan</h2>
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

    <form method="POST" action="{{ route('outlet.results.skb.update', $result->id) }}" class="space-y-6" id="skb-form">
        @csrf
        @method('PUT')

        <h4 class="text-lg font-semibold text-gray-700">Data Pasien</h4>
        <div class="grid grid-cols-12 gap-4 items-end">
            <div class="col-span-12 md:col-span-6">
                <label>Nama Pasien</label>
                <input type="text" name="patient_name" value="{{ old('patient_name', $result->patient->full_name) }}" class="border rounded px-3 py-2 w-full">
            </div>
            <div class="col-span-12 md:col-span-3">
                <label>Tanggal Lahir</label>
                <input type="date" name="dob" value="{{ old('dob', $result->patient->birth_date) }}" class="border rounded px-3 py-2 w-full">
            </div>
            <div class="col-span-12 md:col-span-3">
                <label>Jenis Kelamin</label>
                <select name="gender" class="border rounded px-3 py-2 w-full">
                    <option value="">~ Pilih ~</option>
                    <option value="L" @selected(old('gender', $result->patient->gender) === 'L')>Laki-laki</option>
                    <option value="P" @selected(old('gender', $result->patient->gender) === 'P')>Perempuan</option>
                </select>
            </div>
        </div>

        <h4 class="text-lg font-semibold text-gray-700">Keterangan Surat</h4>
        <div class="grid grid-cols-12 gap-4">
            <div class="col-span-12 md:col-span-4">
                <label>Tanggal Berobat</label>
                <input type="date" name="date" value="{{ old('date', $result->date) }}" class="border rounded px-3 py-2 w-full">
            </div>
            <div class="col-span-12 md:col-span-2">
                <label>Jam Berobat</label>
                <input type="time" name="time" value="{{ old('time', $result->time) }}" class="border rounded px-3 py-2 w-full">
            </div>
            <div class="col-span-12 md:col-span-6">
                <label>Dokter Pemeriksa</label>
                <select name="doctor_id" class="select2 mt-1 w-full border rounded px-3 py-2">
                    <option value="">~ Pilih Dokter ~</option>
                    @foreach ($doctors as $doctor)
                        <option value="{{ $doctor->id }}" @selected(old('doctor_id', $result->doctor_id) == $doctor->id)>
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
                <input type="text" name="nik" value="{{ old('nik', $result->patient->nik) }}" class="border rounded px-3 py-2 w-full">
            </div>
            <div class="col-span-12 md:col-span-4">
                <label>Nomor Telepon</label>
                <input type="text" name="phone" value="{{ old('phone', $result->patient->phone) }}" class="border rounded px-3 py-2 w-full">
            </div>
            <div class="col-span-12 md:col-span-4">
                <label>No Identitas</label>
                <input type="text" name="identity" value="{{ old('identity', $result->patient->identity) }}" class="border rounded px-3 py-2 w-full">
            </div>
        </div>

        <div class="col-span-12">
            <label>Alamat</label>
            <input type="text" name="address" value="{{ old('address', $result->patient->address) }}" class="border rounded px-3 py-2 w-full">
        </div>

        <!-- <h4 class="text-lg font-semibold text-gray-700">Notifikasi</h4>
        <div class="grid grid-cols-12 gap-4">
            <div class="col-span-6">
                <label class="inline-flex items-center">
                    <input type="checkbox" name="send_notif_email" class="rounded border-gray-300" @checked(old('send_notif_email', $result->send_notif_email))>
                    <span class="ml-2 text-sm">Kirim Email ke Pasien</span>
                </label>
            </div>
            <div class="col-span-6">
                <label class="inline-flex items-center">
                    <input type="checkbox" name="send_notif_wa" class="rounded border-gray-300" @checked(old('send_notif_wa', $result->send_notif_wa))>
                    <span class="ml-2 text-sm">Kirim WhatsApp ke Pasien</span>
                </label>
            </div>
        </div> -->

        <div class="text-right mt-6">
            <button type="submit" id="submitBtn" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-2 rounded relative">
                <span id="btnText">Simpan Perubahan</span>
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
document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('send_notif_email')?.addEventListener('change', function () {
        document.getElementById('email_input_wrapper')?.classList.toggle('hidden', !this.checked);
    });

    document.getElementById('send_notif_wa')?.addEventListener('change', function () {
        document.getElementById('wa_input_wrapper')?.classList.toggle('hidden', !this.checked);
    });

    document.getElementById('skb-form')?.addEventListener('submit', function () {
        const btn = document.getElementById('submitBtn');
        document.getElementById('btnText')?.classList.add('opacity-0');
        document.getElementById('btnLoading')?.classList.remove('hidden');
        btn.disabled = true;
    });
});
</script>
@endpush
