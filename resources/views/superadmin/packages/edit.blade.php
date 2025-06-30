@extends('layouts.app')

@section('content')
<div class="max-w-screen-md mx-auto">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">✏️ Edit Paket: {{ $package->name }}</h1>

    @if ($errors->any())
        <div class="mb-4 p-4 bg-red-100 border border-red-300 text-red-700 rounded-md text-sm">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('packages.update', $package) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Nama Paket</label>
            <input type="text" name="name" value="{{ old('name', $package->name) }}"
                   class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Durasi (dalam hari)</label>
            <input type="number" name="duration_in_days" value="{{ old('duration_in_days', $package->duration_in_days) }}"
                   class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Batas Surat per Bulan</label>
            <input type="number" name="max_letters" value="{{ old('max_letters', $package->max_letters) }}"
                   placeholder="Kosongkan jika unlimited"
                   class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Batas Total Pasien</label>
            <input type="number" name="max_patients" value="{{ old('max_patients', $package->max_patients) }}"
                   placeholder="Kosongkan jika unlimited"
                   class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Harga Paket (Rp)</label>
            <input type="number" name="price" step="0.01" value="{{ old('price', $package->price) }}"
                   class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
        </div>

        <div class="mb-4">
            <label class="inline-flex items-center">
                <input type="checkbox" name="is_active" class="rounded border-gray-300"
                       {{ old('is_active', $package->is_active) ? 'checked' : '' }}>
                <span class="ml-2 text-sm text-gray-700">Aktifkan Paket</span>
            </label>
        </div>

        <div class="mt-6 flex justify-between">
            <a href="{{ route('packages.index') }}"
               class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md text-sm hover:bg-gray-200">← Kembali</a>

            <button type="submit"
                    class="px-4 py-2 bg-blue-600 text-white rounded-md text-sm hover:bg-blue-700">
                💾 Update Paket
            </button>
        </div>
    </form>
</div>
@endsection
