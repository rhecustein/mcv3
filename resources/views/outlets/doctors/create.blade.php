@extends('layouts.app', ['header' => 'Tambah Dokter Baru'])

@section('content')
<div class="max-w-5xl mx-auto">

    <div class="mb-6 text-center">
        <h1 class="text-3xl font-bold text-slate-800">Daftarkan Dokter Baru</h1>
        <p class="mt-2 text-base text-slate-500">Lengkapi profil, akun, dan tanda tangan digital untuk dokter baru.</p>
    </div>
    
    @if ($errors->any())
        <div class="mb-6 bg-red-50 border border-red-200 text-sm text-red-700 rounded-lg p-4" role="alert">
            <div class="flex">
                <div class="flex-shrink-0"><svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" /></svg></div>
                <div class="ml-3">
                    <h3 class="font-semibold">Terdapat kesalahan pada input Anda.</h3>
                </div>
            </div>
        </div>
    @endif


    <form action="{{ route('outlet.doctors.store') }}" method="POST" enctype="multipart/form-data" onsubmit="return captureSignature()">
        @csrf
        <div class="bg-white border border-slate-200 rounded-xl shadow-sm">
            <div class="p-6 md:p-8">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    
                    <div class="lg:col-span-1 space-y-6">
                        <div x-data="imageUploader()">
                            <label class="block text-sm font-medium leading-6 text-slate-700 mb-1.5">Foto Profil</label>
                            <div class="mt-2 flex justify-center rounded-lg border border-dashed border-slate-900/25 px-6 py-10"
                                 :class="{ 'border-blue-500': isDragging }">
                                <div class="text-center">
                                    <template x-if="!imageUrl">
                                        <div>
                                            <svg class="mx-auto h-12 w-12 text-slate-300" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M1.5 6a2.25 2.25 0 012.25-2.25h16.5A2.25 2.25 0 0122.5 6v12a2.25 2.25 0 01-2.25 2.25H3.75A2.25 2.25 0 011.5 18V6zM3 16.06V18c0 .414.336.75.75.75h16.5A.75.75 0 0021 18v-1.94l-2.69-2.689a1.5 1.5 0 00-2.12 0l-.88.879.97.97a.75.75 0 11-1.06 1.06l-5.16-5.159a1.5 1.5 0 00-2.12 0L3 16.061zm10.125-7.81a1.125 1.125 0 112.25 0 1.125 1.125 0 01-2.25 0z" clip-rule="evenodd" /></svg>
                                            <div class="mt-4 flex text-sm leading-6 text-slate-600">
                                                <label for="avatar" class="relative cursor-pointer rounded-md bg-white font-semibold text-blue-600 focus-within:outline-none focus-within:ring-2 focus-within:ring-blue-600 focus-within:ring-offset-2 hover:text-blue-500">
                                                    <span>Unggah file</span>
                                                    <input id="avatar" name="avatar" type="file" class="sr-only" @change="fileChosen">
                                                </label>
                                                <p class="pl-1">atau tarik dan lepas</p>
                                            </div>
                                            <p class="text-xs leading-5 text-slate-600">PNG, JPG, GIF hingga 1MB</p>
                                        </div>
                                    </template>
                                    <template x-if="imageUrl">
                                        <div class="relative group">
                                            <img :src="imageUrl" class="w-32 h-32 rounded-full object-cover">
                                            <button @click="removeImage" type="button" class="absolute top-0 right-0 p-1 bg-red-500 text-white rounded-full opacity-0 group-hover:opacity-100 transition-opacity">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" /></svg>
                                            </button>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium leading-6 text-slate-700 mb-1.5">Tanda Tangan Digital</label>
                            <canvas id="signature-pad" class="border border-slate-300 rounded-lg w-full h-40"></canvas>
                            <div class="flex justify-between items-center mt-2">
                                <button type="button" onclick="clearSignature()" class="px-3 py-1 text-sm bg-slate-100 text-slate-700 rounded-md hover:bg-slate-200">
                                    Bersihkan
                                </button>
                                <span class="text-xs text-slate-500">Goreskan tanda tangan di atas</span>
                            </div>
                            <input type="hidden" name="signature_image" id="signature_image">
                        </div>
                    </div>

                    <div class="lg:col-span-2 space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4">
                            <div>
                                <label for="name" class="block text-sm font-medium text-slate-700">Nama Lengkap <span class="text-red-600">*</span></label>
                                <input type="text" name="name" id="name" value="{{ old('name') }}" required class="mt-1 block w-full rounded-md border-slate-300 shadow-sm">
                                @error('name') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="email" class="block text-sm font-medium text-slate-700">Email <span class="text-red-600">*</span></label>
                                <input type="email" name="email" id="email" value="{{ old('email') }}" required class="mt-1 block w-full rounded-md border-slate-300 shadow-sm">
                                @error('email') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                             <div>
                                <label for="phone" class="block text-sm font-medium text-slate-700">Telepon</label>
                                <input type="text" name="phone" id="phone" value="{{ old('phone') }}" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm">
                            </div>
                            <div>
                                <label for="password" class="block text-sm font-medium text-slate-700">Password</label>
                                <input type="password" name="password" id="password" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm" placeholder="Default: doctor123">
                                @error('password') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="gender" class="block text-sm font-medium text-slate-700">Jenis Kelamin</label>
                                <select name="gender" id="gender" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm">
                                    <option value="">-- Pilih --</option>
                                    <option value="male" @selected(old('gender') == 'male')>Laki-laki</option>
                                    <option value="female" @selected(old('gender') == 'female')>Perempuan</option>
                                </select>
                            </div>
                             <div>
                                <label for="birth_date" class="block text-sm font-medium text-slate-700">Tanggal Lahir</label>
                                <input type="date" name="birth_date" id="birth_date" value="{{ old('birth_date') }}" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm">
                            </div>
                            <div>
                                <label for="license_number" class="block text-sm font-medium text-slate-700">Nomor STR / SIP</label>
                                <input type="text" name="license_number" id="license_number" value="{{ old('license_number') }}" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm">
                            </div>
                            <div>
                                <label for="specialist" class="block text-sm font-medium text-slate-700">Spesialisasi</label>
                                <input type="text" name="specialist" id="specialist" value="{{ old('specialist') }}" placeholder="Contoh: Dokter Umum" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm">
                            </div>
                             <div>
                                <label for="education" class="block text-sm font-medium text-slate-700">Pendidikan</label>
                                <input type="text" name="education" id="education" value="{{ old('education') }}" placeholder="Contoh: S.Ked - Univ. Indonesia" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm">
                            </div>
                             <div>
                                <label for="practice_days" class="block text-sm font-medium text-slate-700">Hari Praktik</label>
                                <input type="text" name="practice_days" id="practice_days" value="{{ old('practice_days') }}" placeholder="Contoh: Senin - Jumat" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex justify-between items-center">
                <a href="{{ route('outlet.doctors.index') }}" class="text-sm text-slate-600 hover:text-slate-900 transition hover:underline">
                    &larr; Kembali ke Daftar Dokter
                </a>
                <button type="submit"
                        class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-blue-600 text-white font-bold rounded-lg shadow-md hover:bg-blue-700 transition-all duration-200">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                    <span>Simpan Data Dokter</span>
                </button>
            </div>
        </div>
    </form>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('imageUploader', () => ({
        imageUrl: '',
        isDragging: false,
        fileChosen(event) {
            this.fileToDataUrl(event.target.files[0]);
        },
        fileToDataUrl(file) {
            if (!file) return;
            let reader = new FileReader();
            reader.onload = e => this.imageUrl = e.target.result;
            reader.readAsDataURL(file);
        },
        removeImage() {
            this.imageUrl = '';
            document.getElementById('avatar').value = '';
        }
    }));
});

const canvas = document.getElementById('signature-pad');
const signaturePad = new SignaturePad(canvas, {
    backgroundColor: 'rgb(241, 245, 249)' // bg-slate-100
});

function clearSignature() {
    signaturePad.clear();
}

function captureSignature() {
    if (!signaturePad.isEmpty()) {
        document.getElementById('signature_image').value = signaturePad.toDataURL('image/png');
    }
    return true; // Lanjutkan submit form
}
</script>
@endpush