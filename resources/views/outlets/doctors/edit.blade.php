@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto p-6 bg-white rounded-xl shadow space-y-6">

    <h2 class="text-2xl font-bold text-gray-800">✏️ Edit Data Dokter</h2>

    <form action="{{ route('outlet.doctors.update', $doctor) }}" method="POST" onsubmit="return captureSignature()">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- KIRI -->
            <div class="space-y-4">
                <!-- Nama -->
                <div>
                    <label for="name" class="block text-sm font-semibold text-gray-700 mb-1">Nama</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $doctor->user->name) }}"
                           class="w-full px-3 py-2 border rounded-lg text-sm shadow-sm">
                    @error('name') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Telepon -->
                <div>
                    <label for="phone" class="block text-sm font-semibold text-gray-700 mb-1">Telepon</label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone', $doctor->user->phone) }}"
                           class="w-full px-3 py-2 border rounded-lg text-sm shadow-sm">
                    @error('phone') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Gender -->
                <div>
                    <label for="gender" class="block text-sm font-semibold text-gray-700 mb-1">Jenis Kelamin</label>
                    <select name="gender" class="w-full px-3 py-2 border rounded-lg text-sm shadow-sm">
                        <option value="">-- Pilih --</option>
                        <option value="male" {{ old('gender', $doctor->gender) == 'L' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="female" {{ old('gender', $doctor->gender) == 'P' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                    @error('gender') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Tanggal Lahir -->
                <div>
                    <label for="birth_date" class="block text-sm font-semibold text-gray-700 mb-1">Tanggal Lahir</label>
                    <input type="date" name="birth_date" value="{{ old('birth_date', $doctor->birth_date) }}"
                           class="w-full px-3 py-2 border rounded-lg text-sm shadow-sm">
                    @error('birth_date') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- STR / SIP -->
                <div>
                    <label for="license_number" class="block text-sm font-semibold text-gray-700 mb-1">No STR / SIP</label>
                    <input type="text" name="license_number" value="{{ old('license_number', $doctor->license_number) }}"
                           class="w-full px-3 py-2 border rounded-lg text-sm shadow-sm">
                    @error('license_number') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- KANAN -->
            <div class="space-y-4">
                <!-- Spesialis -->
                <div>
                    <label for="specialist" class="block text-sm font-semibold text-gray-700 mb-1">Spesialis</label>
                    <input type="text" name="specialist" value="{{ old('specialist', $doctor->specialist) }}"
                           class="w-full px-3 py-2 border rounded-lg text-sm shadow-sm">
                </div>

                <!-- Pendidikan -->
                <div>
                    <label for="education" class="block text-sm font-semibold text-gray-700 mb-1">Pendidikan</label>
                    <input type="text" name="education" value="{{ old('education', $doctor->education) }}"
                           class="w-full px-3 py-2 border rounded-lg text-sm shadow-sm">
                </div>

                <!-- Hari Praktik -->
                <div>
                    <label for="practice_days" class="block text-sm font-semibold text-gray-700 mb-1">Hari Praktik</label>
                    <input type="text" name="practice_days" value="{{ old('practice_days', $doctor->practice_days) }}"
                           class="w-full px-3 py-2 border rounded-lg text-sm shadow-sm">
                </div>

                <!-- Sub-spesialisasi -->
                <div>
                    <label for="specialization" class="block text-sm font-semibold text-gray-700 mb-1">Spesialisasi Tambahan</label>
                    <input type="text" name="specialization" value="{{ old('specialization', $doctor->specialization) }}"
                           class="w-full px-3 py-2 border rounded-lg text-sm shadow-sm">
                </div>
            </div>
        </div>

        <!-- Signature Lama -->
        @if($doctor->signature_image)
            <div class="pt-6">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Tanda Tangan Saat Ini</label>
                <img src="{{ asset('storage/' . $doctor->signature_image) }}" alt="Signature"
                     class="w-64 border border-gray-300 rounded shadow">
            </div>
        @endif

        <!-- Signature Baru -->
        <div class="pt-6">
            <label class="block text-sm font-semibold text-gray-700 mb-1">Ganti Tanda Tangan (Opsional)</label>
            <canvas id="signature-pad" class="border border-gray-300 rounded-md bg-gray-50 w-full h-40"></canvas>
            <div class="flex justify-between items-center mt-2">
                <button type="button" onclick="clearSignature()"
                        class="px-3 py-1 text-sm bg-gray-100 text-gray-700 rounded hover:bg-gray-200">
                    🧹 Bersihkan
                </button>
                <span class="text-xs text-gray-500">Kosongkan jika tidak ingin mengganti</span>
            </div>
            <input type="hidden" name="signature_image" id="signature_image">
        </div>

        <!-- QR Code -->
        @if($doctor->signature_qrcode)
            <div class="pt-6">
                <label class="block text-sm font-semibold text-gray-700 mb-1">QR Code Saat Ini</label>
                <img src="{{ asset('storage/' . $doctor->signature_qrcode) }}" alt="QR Code"
                     class="w-32 border border-gray-300 rounded shadow">
            </div>
        @endif

        <!-- Tombol -->
        <div class="flex justify-between items-center pt-8">
            <a href="{{ route('outlet.doctors.index') }}" class="text-sm text-gray-600 hover:underline">← Kembali</a>
            <button type="submit"
                    class="px-5 py-2 bg-blue-600 text-white text-sm rounded-md hover:bg-blue-700 transition">
                💾 Simpan Perubahan
            </button>
        </div>
    </form>
</div>

<!-- SignaturePad JS -->
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
<script>
    const canvas = document.getElementById('signature-pad');
    const signaturePad = new SignaturePad(canvas);

    function clearSignature() {
        signaturePad.clear();
    }

    function captureSignature() {
        if (!signaturePad.isEmpty()) {
            const dataUrl = signaturePad.toDataURL('image/png');
            document.getElementById('signature_image').value = dataUrl;
        }
        return true;
    }
</script>
@endsection
