@extends('layouts.app')

@section('content')
<div class="max-w-screen-md mx-auto">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">✏️ Edit Template: {{ $templateResult->name }}</h1>

    @if ($errors->any())
        <div class="mb-4 p-4 bg-red-100 border border-red-300 text-red-700 rounded-md text-sm">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('template-results.update', $templateResult) }}" method="POST">
        @csrf
        @method('PUT')

        {{-- Nama --}}
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Nama Template</label>
            <input type="text" name="name" value="{{ old('name', $templateResult->name) }}"
                   class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 text-sm">
        </div>

        {{-- Kode --}}
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Kode Template</label>
            <input type="text" name="code" value="{{ old('code', $templateResult->code) }}"
                   class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 text-sm">
        </div>

        {{-- Jenis --}}
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Jenis Surat</label>
            <select name="type" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 text-sm">
                <option value="skb" {{ old('type', $templateResult->type) === 'skb' ? 'selected' : '' }}>Surat Sehat (SKB)</option>
                <option value="mc"  {{ old('type', $templateResult->type) === 'mc'  ? 'selected' : '' }}>Surat Sakit (MC)</option>
            </select>
        </div>

        {{-- Deskripsi --}}
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Deskripsi (Opsional)</label>
            <textarea name="description" rows="2"
                      class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 text-sm">{{ old('description', $templateResult->description) }}</textarea>
        </div>

        {{-- CKEditor --}}
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Isi HTML Template</label>
            <textarea id="html_content" name="html_content" class="ckeditor mt-1 block w-full border border-gray-300 rounded-md shadow-sm text-sm px-3 py-2" rows="10">
                {!! old('html_content', $templateResult->html_content) !!}
            </textarea>
        </div>

        {{-- Checkbox --}}
        <div class="mb-4 flex items-center gap-6">
            <label class="inline-flex items-center">
                <input type="checkbox" name="default" class="rounded border-gray-300"
                       {{ old('default', $templateResult->default) ? 'checked' : '' }}>
                <span class="ml-2 text-sm text-gray-700">Template Default</span>
            </label>

            <label class="inline-flex items-center">
                <input type="checkbox" name="is_active" class="rounded border-gray-300"
                       {{ old('is_active', $templateResult->is_active) ? 'checked' : '' }}>
                <span class="ml-2 text-sm text-gray-700">Aktif</span>
            </label>
        </div>

        {{-- Aksi --}}
        <div class="mt-6 flex justify-between">
            <a href="{{ route('template-results.index') }}"
               class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md text-sm hover:bg-gray-200">← Kembali</a>

            <button type="submit"
                    class="px-4 py-2 bg-blue-600 text-white rounded-md text-sm hover:bg-blue-700">
                💾 Update Template
            </button>
        </div>
    </form>
</div>
@endsection

{{-- CKEditor CDN --}}
@push('scripts')
<script src="https://cdn.ckeditor.com/ckeditor5/41.0.1/classic/ckeditor.js"></script>
<script>
    ClassicEditor
        .create(document.querySelector('#html_content'))
        .catch(error => {
            console.error(error);
        });
</script>
@endpush
