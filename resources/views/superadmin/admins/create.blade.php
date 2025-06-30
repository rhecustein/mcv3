@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto">

    <h1 class="text-2xl font-bold text-gray-800 mb-6">➕ Tambah Admin Baru</h1>

    <form action="{{ route('admins.store') }}" method="POST" class="bg-white p-6 rounded-lg shadow space-y-6">
        @csrf

        {{-- Nama --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
            <input type="text" name="name" value="{{ old('name') }}" required
                   class="w-full px-4 py-2 border rounded-md text-sm @error('name') border-red-500 @enderror">
            @error('name')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Email --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" required
                   class="w-full px-4 py-2 border rounded-md text-sm @error('email') border-red-500 @enderror">
            @error('email')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Nomor HP --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nomor HP</label>
            <input type="text" name="phone" value="{{ old('phone') }}"
                   class="w-full px-4 py-2 border rounded-md text-sm @error('phone') border-red-500 @enderror">
            @error('phone')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Password --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Password (opsional)</label>
            <input type="password" name="password"
                   placeholder="Kosongkan untuk default: admin123"
                   class="w-full px-4 py-2 border rounded-md text-sm @error('password') border-red-500 @enderror">
            @error('password')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <hr class="my-4">

        {{-- Wilayah --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Wilayah (Contoh: Jakarta Selatan)</label>
            <input type="text" name="region_name" value="{{ old('region_name') }}" required
                   class="w-full px-4 py-2 border rounded-md text-sm @error('region_name') border-red-500 @enderror">
            @error('region_name')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Provinsi --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Provinsi</label>
            <input type="text" name="province" value="{{ old('province') }}"
                   class="w-full px-4 py-2 border rounded-md text-sm @error('province') border-red-500 @enderror">
            @error('province')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Jabatan --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Jabatan</label>
            <input type="text" name="position_title" value="{{ old('position_title') }}"
                   class="w-full px-4 py-2 border rounded-md text-sm">
        </div>

        {{-- Kontak Langsung --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Kontak Langsung</label>
            <input type="text" name="contact_number" value="{{ old('contact_number') }}"
                   class="w-full px-4 py-2 border rounded-md text-sm">
        </div>

        {{-- Tombol --}}
        <div class="flex justify-between items-center pt-4">
            <a href="{{ route('admins.index') }}"
               class="text-sm text-gray-600 hover:underline">← Kembali</a>
            <button type="submit"
                    class="px-6 py-2 bg-blue-600 text-white text-sm rounded-md hover:bg-blue-700 transition">
                💾 Simpan Admin
            </button>
        </div>
    </form>

</div>
@endsection
