@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto p-6 space-y-6">

    {{-- Alert sukses --}}
    @if(session('success'))
        <div class="mb-4 px-4 py-3 bg-green-100 text-green-800 border border-green-300 rounded-md text-sm shadow-sm">
            ✅ {{ session('success') }}
        </div>
    @endif


    <!-- Header & Tombol Tambah -->
    <div class="flex justify-between items-center">
        <h2 class="text-xl font-bold text-gray-800">👨‍⚕️ Daftar Dokter</h2>
        <a href="{{ route('outlet.doctors.create') }}"
           class="px-4 py-2 bg-blue-600 text-white text-sm rounded shadow hover:bg-blue-700 transition">+ Tambah Dokter</a>
    </div>

    <!-- Filter -->
    <form method="GET" class="flex items-center space-x-2">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama/email..."
               class="px-3 py-2 w-1/3 text-sm border rounded-md shadow-sm focus:ring focus:border-blue-300" />
        <button type="submit"
                class="px-4 py-2 bg-gray-100 border text-sm rounded hover:bg-gray-200">🔍 Cari</button>
    </form>

    <!-- Tabel Dokter -->
    @if($doctors->count())
        <div class="overflow-x-auto rounded shadow border">
            <table class="min-w-full bg-white">
                <thead class="bg-gray-100 text-xs text-gray-700 uppercase">
                    <tr>
                        <th class="px-4 py-3 text-left">Nama</th>
                        <th class="px-4 py-3 text-left">Email</th>
                        <th class="px-4 py-3 text-left">Telepon</th>
                        <th class="px-4 py-3 text-center">Statistik</th>
                        <th class="px-4 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y text-gray-800">
                    @foreach($doctors as $doctor)
                        <tr>
                            <td class="px-4 py-3">{{ $doctor->user->name }}</td>
                            <td class="px-4 py-3">{{ $doctor->user->email }}</td>
                            <td class="px-4 py-3">{{ $doctor->user->phone ?? '-' }}</td>

                            <!-- Statistik Surat -->
                            <td class="px-4 py-3 text-center align-top">
                                <div class="grid grid-cols-2 gap-2 text-xs text-gray-700 justify-center">
                                    <div class="bg-gray-50 rounded-md px-2 py-1 shadow-sm">
                                        📄<br><strong>{{ $doctor->results_count ?? 0 }}</strong><br>
                                        <span class="text-[10px]">Surat</span>
                                    </div>
                                </div>
                            </td>

                            <!-- Tombol Aksi -->
                            <td class="px-4 py-3 text-center align-top">
                                <div class="flex flex-col space-y-2">
                                    <a href="{{ route('outlet.doctors.edit', $doctor) }}"
                                       class="px-3 py-1 text-sm bg-yellow-100 text-yellow-800 rounded hover:bg-yellow-200 transition text-center">✏️ Edit</a>

                                    <form action="{{ route('outlet.doctors.destroy', $doctor) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                onclick="return confirm('Yakin ingin menghapus dokter ini?')"
                                                class="w-full px-3 py-1 text-sm bg-red-100 text-red-700 rounded hover:bg-red-200 transition">
                                            🗑️ Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $doctors->withQueryString()->links() }}
        </div>
    @else
        <p class="text-gray-500 mt-4">Belum ada dokter terdaftar.</p>
    @endif
</div>
@endsection
