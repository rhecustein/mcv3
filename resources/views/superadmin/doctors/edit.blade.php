@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto">

    <h1 class="text-2xl font-bold text-gray-800 mb-6">✏️ Edit Data Dokter</h1>

    <form action="{{ route('doctors.update', $doctor) }}" method="POST" class="bg-white p-6 rounded-lg shadow space-y-6">
        @csrf
        @method('PUT')

        {{-- Nama --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
            <input type="text" name="name" value="{{ old('name', $doctor->user->name) }}" required
                   class="w-full px-4 py-2 border rounded-md text-sm @error('name') border-red-500 @enderror">
            @error('name') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Email --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
            <input type="email" name="email" value="{{ old('email', $doctor->user->email) }}" required
                   class="w-full px-4 py-2 border rounded-md text-sm @error('email') border-red-500 @enderror">
            @error('email') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Nomor HP --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nomor HP</label>
            <input type="text" name="phone" value="{{ old('phone', $doctor->user->phone) }}"
                   class="w-full px-4 py-2 border rounded-md text-sm">
        </div>

        {{-- Spesialisasi & STR --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Spesialisasi</label>
                <input type="text" name="specialist" value="{{ old('specialist', $doctor->specialist) }}"
                       class="w-full px-4 py-2 border rounded-md text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nomor STR/SIP</label>
                <input type="text" name="license_number" value="{{ old('license_number', $doctor->license_number) }}"
                       class="w-full px-4 py-2 border rounded-md text-sm">
            </div>
        </div>

        {{-- Jenis Kelamin & Tanggal Lahir --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Kelamin</label>
                <select name="gender"
                        class="w-full px-4 py-2 border rounded-md text-sm @error('gender') border-red-500 @enderror">
                    <option value="">-- Pilih --</option>
                    <option value="male" {{ old('gender', $doctor->gender) === 'male' ? 'selected' : '' }}>Laki-laki</option>
                    <option value="female" {{ old('gender', $doctor->gender) === 'female' ? 'selected' : '' }}>Perempuan</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Lahir</label>
                <input type="date" name="birth_date" value="{{ old('birth_date', $doctor->birth_date?->format('Y-m-d')) }}"
                       class="w-full px-4 py-2 border rounded-md text-sm">
            </div>
        </div>

        {{-- Outlet --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Outlet</label>
            <select name="outlet_id"
                    class="w-full px-4 py-2 border rounded-md text-sm @error('outlet_id') border-red-500 @enderror">
                <option value="">-- Pilih Outlet --</option>
                @foreach ($outlets as $outlet)
                    <option value="{{ $outlet->id }}" {{ old('outlet_id', $doctor->outlet_id) == $outlet->id ? 'selected' : '' }}>
                        {{ $outlet->name }} ({{ $outlet->city ?? '-' }})
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Tombol --}}
        <div class="flex justify-between pt-4">
            <a href="{{ route('doctors.index') }}"
               class="text-sm text-gray-600 hover:underline">← Batal</a>

            <button type="submit"
                    class="px-6 py-2 bg-blue-600 text-white text-sm rounded-md hover:bg-blue-700 transition">
                💾 Update Dokter
            </button>
        </div>
    </form>

</div>
@endsection
