@extends('layouts.app')

@section('content')
<div class="max-w-screen-xl mx-auto">
    {{-- Header --}}
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">📄 Manajemen Template Surat</h1>
        <a href="{{ route('template-results.create') }}"
           class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg shadow hover:bg-blue-700 transition">
            ➕ Tambah Template
        </a>
    </div>

    {{-- Alert sukses --}}
    @if(session('success'))
        <div class="mb-4 px-4 py-3 bg-green-100 text-green-800 border border-green-300 rounded-md text-sm shadow-sm">
            ✅ {{ session('success') }}
        </div>
    @endif

    {{-- Count Card --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm text-gray-500">Total Template</div>
            <div class="text-2xl font-bold text-blue-600">{{ $totalTemplates }}</div>
        </div>
    </div>

    {{-- Filter & Search --}}
    <form method="GET" class="bg-white p-4 rounded-lg shadow mb-6 flex flex-wrap gap-4 items-center">
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Cari nama / kode template..."
               class="w-full md:w-1/3 px-4 py-2 border border-gray-300 rounded-md text-sm">

        <select name="type" class="w-full md:w-1/4 px-4 py-2 border border-gray-300 rounded-md text-sm">
            <option value="">-- Jenis Surat --</option>
            <option value="skb" {{ request('type') === 'skb' ? 'selected' : '' }}>Surat Sehat (SKB)</option>
            <option value="mc" {{ request('type') === 'mc' ? 'selected' : '' }}>Surat Sakit (MC)</option>
        </select>

        <button type="submit"
                class="px-4 py-2 bg-blue-600 text-white rounded-md text-sm hover:bg-blue-700 transition">
            🔍 Filter
        </button>

        <a href="{{ route('template-results.index') }}"
           class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md text-sm hover:bg-gray-200 transition">
            🔄 Reset
        </a>
    </form>

    {{-- Tabel --}}
    <div class="bg-white shadow rounded-lg overflow-x-auto">
        <table class="min-w-full text-sm text-left text-gray-700">
            <thead class="bg-gray-100 text-gray-800 font-semibold">
                <tr>
                    <th class="px-4 py-3">Nama</th>
                    <th class="px-4 py-3">Kode</th>
                    <th class="px-4 py-3">Jenis</th>
                    <th class="px-4 py-3">Default</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">Dibuat</th>
                    <th class="px-4 py-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($templates as $template)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium">{{ $template->name }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $template->code }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 rounded text-xs font-medium {{ $template->type === 'skb' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                {{ strtoupper($template->type) }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            @if($template->default)
                                <span class="text-green-600 font-semibold text-xs">✓ Ya</span>
                            @else
                                <span class="text-gray-400 text-xs">Tidak</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @if($template->is_active)
                                <span class="px-2 py-1 bg-green-100 text-green-700 rounded text-xs font-medium">Aktif</span>
                            @else
                                <span class="px-2 py-1 bg-red-100 text-red-700 rounded text-xs font-medium">Nonaktif</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-500">
                            {{ $template->created_at->format('d M Y') }}
                        </td>
                        <td class="px-4 py-3 text-center align-top">
                            <div class="flex flex-col items-center gap-2 w-24">
                                <a href="{{ route('template-results.edit', $template) }}"
                                   class="w-full text-center px-3 py-1.5 text-sm bg-yellow-100 text-yellow-800 rounded hover:bg-yellow-200 transition">
                                    ✏️ Edit
                                </a>

                                <form action="{{ route('template-results.destroy', $template) }}" method="POST" class="w-full">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            onclick="return confirm('Hapus template ini?')"
                                            class="w-full px-3 py-1.5 text-sm bg-red-100 text-red-700 rounded hover:bg-red-200 transition">
                                        🗑️ Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-4 text-center text-gray-500">Belum ada template tersedia.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="mt-6">
        {{ $templates->appends(request()->query())->links() }}
    </div>
</div>
@endsection
