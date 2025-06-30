@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto">

    <h1 class="text-2xl font-bold text-gray-800 mb-6">✏️ Edit Outlet</h1>

    <form action="{{ route('outlets.update', $outlet) }}" method="POST"
          class="bg-white p-6 rounded-lg shadow space-y-6">
        @csrf
        @method('PUT')

        {{-- Nama --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Outlet</label>
            <input type="text" name="name" value="{{ old('name', $outlet->name) }}" required
                   class="w-full px-4 py-2 border rounded-md text-sm @error('name') border-red-500 @enderror">
            @error('name') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Email --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
            <input type="email" name="email" value="{{ old('email', $outlet->email) }}"
                   class="w-full px-4 py-2 border rounded-md text-sm @error('email') border-red-500 @enderror">
            @error('email') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Nomor HP --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nomor HP</label>
            <input type="text" name="phone" value="{{ old('phone', $outlet->phone) }}"
                   class="w-full px-4 py-2 border rounded-md text-sm @error('phone') border-red-500 @enderror">
            @error('phone') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Alamat --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
            <input type="text" name="address" value="{{ old('address', $outlet->address) }}"
                   class="w-full px-4 py-2 border rounded-md text-sm">
        </div>

        {{-- Kota & Provinsi --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kota</label>
                <input type="text" name="city" value="{{ old('city', $outlet->city) }}"
                       class="w-full px-4 py-2 border rounded-md text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Provinsi</label>
                <input type="text" name="province" value="{{ old('province', $outlet->province) }}"
                       class="w-full px-4 py-2 border rounded-md text-sm">
            </div>
        </div>

        {{-- Kode Outlet --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Kode Outlet (opsional)</label>
            <input type="text" name="code" value="{{ old('code', $outlet->code) }}"
                   class="w-full px-4 py-2 border rounded-md text-sm">
        </div>

        {{-- Admin Pengelola --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Admin Pengelola</label>
            <select name="admin_id"
                    class="w-full px-4 py-2 border rounded-md text-sm @error('admin_id') border-red-500 @enderror">
                <option value="">-- Pilih Admin --</option>
                @foreach ($admins as $admin)
                    <option value="{{ $admin->id }}" {{ old('admin_id', $outlet->admin_id) == $admin->id ? 'selected' : '' }}>
                        {{ $admin->user->name ?? '-' }}
                    </option>
                @endforeach
            </select>
            @error('admin_id') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Ubah Password (Admin Outlet) --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Ubah Password Admin</label>
            <input type="password" name="password"
                placeholder="Kosongkan jika tidak diubah"
                class="w-full px-4 py-2 border rounded-md text-sm @error('password') border-red-500 @enderror">
            @error('password') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
            <p class="text-xs text-gray-400 mt-1">Kosongkan jika tidak ingin mengubah password admin.</p>
        </div>

        {{-- Tombol --}}
        <div class="flex justify-between pt-4">
            <a href="{{ route('outlets.index') }}"
               class="text-sm text-gray-600 hover:underline">← Batal</a>

            <button type="submit"
                    class="px-6 py-2 bg-blue-600 text-white text-sm rounded-md hover:bg-blue-700 transition">
                💾 Update Outlet
            </button>
        </div>
    </form>

</div>
@endsection
