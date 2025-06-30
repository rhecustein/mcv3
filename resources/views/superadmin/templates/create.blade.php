@extends('layouts.app')

@section('content')
<div class="max-w-screen-md mx-auto">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">➕ Tambah Template Surat</h1>

    @if ($errors->any())
        <div class="mb-4 p-4 bg-red-100 border border-red-300 text-red-700 rounded-md text-sm">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('template-results.store') }}" method="POST">
        @csrf

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Nama Template</label>
            <input type="text" name="name" value="{{ old('name') }}"
                   class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 text-sm">
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Kode Template</label>
            <input type="text" name="code" value="{{ old('code') }}"
                   class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 text-sm">
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Jenis Surat</label>
            <select name="type"
                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 text-sm">
                <option value="skb" {{ old('type') === 'skb' ? 'selected' : '' }}>Surat Sehat (SKB)</option>
                <option value="mc" {{ old('type') === 'mc' ? 'selected' : '' }}>Surat Sakit (MC)</option>
            </select>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Deskripsi (Opsional)</label>
            <textarea name="description" rows="2"
                      class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 text-sm">{{ old('description') }}</textarea>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Isi HTML Template</label>
            <textarea name="html_content" rows="10"
                      class="mt-1 font-mono block w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 text-sm">{{ old('html_content') }}</textarea>
        </div>

        <div class="mb-4 flex items-center gap-6">
            <label class="inline-flex items-center">
                <input type="checkbox" name="default" class="rounded border-gray-300" {{ old('default') ? 'checked' : '' }}>
                <span class="ml-2 text-sm text-gray-700">Template Default</span>
            </label>

            <label class="inline-flex items-center">
                <input type="checkbox" name="is_active" class="rounded border-gray-300" {{ old('is_active', true) ? 'checked' : '' }}>
                <span class="ml-2 text-sm text-gray-700">Aktif</span>
            </label>
        </div>

        <div class="mt-6 flex justify-between">
            <a href="{{ route('template-results.index') }}"
               class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md text-sm hover:bg-gray-200">← Kembali</a>

            <button type="submit"
                    class="px-4 py-2 bg-blue-600 text-white rounded-md text-sm hover:bg-blue-700">
                💾 Simpan Template
            </button>
        </div>
    </form>
</div>
@endsection
