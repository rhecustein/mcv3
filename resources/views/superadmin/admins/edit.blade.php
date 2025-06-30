@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto">

    <h1 class="text-2xl font-bold text-gray-800 mb-6">✏️ Edit Admin</h1>

    <form action="{{ route('admins.update', $admin) }}" method="POST" class="bg-white p-6 rounded-lg shadow space-y-6">
        @csrf
        @method('PUT')

        {{-- Nama --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
            <input type="text" name="name" value="{{ old('name', $admin->user->name) }}" required
                   class="w-full px-4 py-2 border rounded-md text-sm @error('name') border-red-500 @enderror">
            @error('name') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Email --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
            <input type="email" name="email" value="{{ old('email', $admin->user->email) }}" required
                   class="w-full px-4 py-2 border rounded-md text-sm @error('email') border-red-500 @enderror">
            @error('email') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- No. HP --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nomor HP</label>
            <input type="text" name="phone" value="{{ old('phone', $admin->user->phone) }}"
                   class="w-full px-4 py-2 border rounded-md text-sm @error('phone') border-red-500 @enderror">
            @error('phone') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <hr class="my-4">

        {{-- Wilayah --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Wilayah</label>
            <input type="text" name="region_name" value="{{ old('region_name', $admin->region_name) }}" required
                   class="w-full px-4 py-2 border rounded-md text-sm @error('region_name') border-red-500 @enderror">
            @error('region_name') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Provinsi --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Provinsi</label>
            <input type="text" name="province" value="{{ old('province', $admin->province) }}"
                   class="w-full px-4 py-2 border rounded-md text-sm @error('province') border-red-500 @enderror">
            @error('province') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Jabatan --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Jabatan</label>
            <input type="text" name="position_title" value="{{ old('position_title', $admin->position_title) }}"
                   class="w-full px-4 py-2 border rounded-md text-sm">
        </div>

        {{-- Kontak Langsung --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Kontak Langsung</label>
            <input type="text" name="contact_number" value="{{ old('contact_number', $admin->contact_number) }}"
                   class="w-full px-4 py-2 border rounded-md text-sm">
        </div>

        {{-- Tombol --}}
        <div class="flex justify-between items-center pt-4">
            <a href="{{ route('admins.index') }}"
               class="text-sm text-gray-600 hover:underline">← Batal</a>
            <button type="submit"
                    class="px-6 py-2 bg-blue-600 text-white text-sm rounded-md hover:bg-blue-700 transition">
                💾 Simpan Perubahan
            </button>
        </div>

    </form>
</div>
@endsection
