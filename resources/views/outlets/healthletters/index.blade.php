@extends('layouts.app')

@section('content')
<div class="bg-gradient-to-r from-blue-600 to-blue-400 text-white p-6 rounded-xl shadow-lg mb-6 animate-fade-in">
    <h2 class="text-3xl font-bold mb-2">Manage Health Letters</h2>
    <nav class="text-sm">
        <ol class="list-reset flex">
            <li><a href="{{ route('outlet.healthletter.index') }}" class="underline">Health Letters</a></li>
        </ol>
    </nav>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-blue-100 p-5 rounded-lg shadow hover:shadow-md transition duration-300">
        <p class="text-sm text-blue-800">Total Letters</p>
        <h3 class="text-2xl font-extrabold text-blue-900">{{ $totalLettersAllTime }}</h3>
        <p class="text-xs text-blue-700">All Time</p>
    </div>
    <div class="bg-red-100 p-5 rounded-lg shadow hover:shadow-md transition duration-300">
        <p class="text-sm text-red-800">Total MC</p>
        <h3 class="text-2xl font-extrabold text-red-900">{{ $totalMCAllTime }}</h3>
        <p class="text-xs text-red-700">All Time</p>
    </div>
    <div class="bg-green-100 p-5 rounded-lg shadow hover:shadow-md transition duration-300">
        <p class="text-sm text-green-800">Total MCL</p>
        <h3 class="text-2xl font-extrabold text-green-900">{{ $totalMCLAllTime }}</h3>
        <p class="text-xs text-green-700">All Time</p>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-blue-200 p-5 rounded-lg shadow hover:shadow-md transition duration-300">
        <p class="text-sm text-blue-900">This Month Letters</p>
        <h3 class="text-2xl font-extrabold text-blue-950">{{ $totalLettersThisMonth }}</h3>
    </div>
    <div class="bg-red-200 p-5 rounded-lg shadow hover:shadow-md transition duration-300">
        <p class="text-sm text-red-900">This Month MC</p>
        <h3 class="text-2xl font-extrabold text-red-950">{{ $totalMCThisMonth }}</h3>
    </div>
    <div class="bg-green-200 p-5 rounded-lg shadow hover:shadow-md transition duration-300">
        <p class="text-sm text-green-900">This Month MCL</p>
        <h3 class="text-2xl font-extrabold text-green-950">{{ $totalMCLThisMonth }}</h3>
    </div>
</div>

<div class="bg-white shadow-xl rounded-lg p-6 animate-fade-in">
    <div class="flex justify-between items-center mb-4">
        <div class="font-semibold text-lg">Filter Surat</div>
        <div class="flex gap-2">
            <a href="{{ route('outlet.results.skb.create') }}"
                class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded">+ Surat Sehat (SKB)</a>
            <a href="{{ route('outlet.results.mc.create') }}"
                class="bg-rose-600 hover:bg-rose-700 text-white px-4 py-2 rounded">+ Surat Sakit (MC)</a>
        </div>
    </div>

    <form action="{{ route('outlet.healthletter.index') }}" method="GET"
        class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
        <!-- Keyword -->
        <div>
            <label for="keyword" class="block text-sm font-medium text-gray-700">Cari Pasien</label>
            <input type="text" name="keyword" id="keyword" value="{{ request('keyword') }}" placeholder="Nama pasien..."
                class="mt-1 block w-full rounded border-gray-300 px-3 py-2">
        </div>

        <!-- Date From -->
        <div>
            <label for="from" class="block text-sm font-medium text-gray-700">Dari Tanggal</label>
            <input type="date" name="from" id="from" value="{{ request('from') }}"
                class="mt-1 block w-full rounded border-gray-300 px-3 py-2">
        </div>

        <!-- Date To -->
        <div>
            <label for="to" class="block text-sm font-medium text-gray-700">Sampai Tanggal</label>
            <input type="date" name="to" id="to" value="{{ request('to') }}"
                class="mt-1 block w-full rounded border-gray-300 px-3 py-2">
        </div>

        <!-- Filter Type -->
        <div>
            <label for="type" class="block text-sm font-medium text-gray-700">Jenis Surat</label>
            <select name="type" id="type" class="mt-1 block w-full rounded border-gray-300 px-3 py-2">
                <option value="">Semua</option>
                <option value="skb" {{ request('type') === 'skb' ? 'selected' : '' }}>Surat Sehat (SKB)</option>
                <option value="mc" {{ request('type') === 'mc' ? 'selected' : '' }}>Surat Sakit (MC)</option>
            </select>
        </div>

        <!-- Filter Doctor -->
        <div>
            <label for="doctor_id" class="block text-sm font-medium text-gray-700">Dokter</label>
            <select name="doctor_id" id="doctor_id" class="mt-1 block w-full rounded border-gray-300 px-3 py-2">
                <option value="">Semua</option>
                @foreach($doctors as $doctor)
                <option value="{{ $doctor->id }}" {{ request('doctor_id') == $doctor->id ? 'selected' : '' }}>
                    {{ $doctor->user->name }}
                </option>
                @endforeach
            </select>
        </div>
    </form>

   <!-- Filter Action -->
<div class="flex justify-end gap-2 mb-4">

    <!-- Filter -->
    <button type="submit" form="filter-form"
        class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded shadow-sm inline-flex items-center gap-1">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
            stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L15 12.414V18a1 1 0 01-1.447.894l-4-2A1 1 0 019 16v-3.586L3.293 6.707A1 1 0 013 6V4z" />
        </svg>
        Filter
    </button>

    <!-- Reset Filter -->
    <a href="{{ route('outlet.healthletter.index') }}"
        class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded shadow-sm inline-flex items-center gap-1">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
            stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M4 4v5h.582M20 20v-5h-.581M4 9a9 9 0 0116 6M20 15a9 9 0 01-16-6" />
        </svg>
        Reset
    </a>

    <!-- Antrian PDF -->
    <a href="{{ route('outlet.queue.index') }}"
        class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded shadow-sm inline-flex items-center gap-1">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
            stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8m-9 11V5" />
        </svg>
        Antrian PDF
    </a>

    <!-- Refresh Page -->
    <a href="{{ route('outlet.healthletter.index') }}"
        class="bg-indigo-500 hover:bg-indigo-600 text-white text-sm px-4 py-2 rounded inline-flex items-center gap-1">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
            stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M4 4v5h.582M20 20v-5h-.581M4 9a9 9 0 0116 6M20 15a9 9 0 01-16-6" />
        </svg>
        Refresh
    </a>

    <!-- Trash / Deleted -->
    <a href="{{ route('outlet.result.trash.index') }}" {{-- replace with actual trash route --}}
        class="bg-red-500 hover:bg-red-600 text-white text-sm px-4 py-2 rounded inline-flex items-center gap-1">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
            stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3H4m16 0h-4" />
        </svg>
        Trash
    </a>

</div>

    <div class="flex justify-end gap-2 mb-4">

        @if (auth()->user()->can('healthletter.bulking_generate_document'))
        <button data-bs-toggle="modal" data-bs-target="#bulkingGenerateDocument"
            class="bg-yellow-500 hover:bg-yellow-600 text-white text-sm px-4 py-2 rounded">Bulking Generate</button>
        @endif
    </div>

    @if (session('success'))
    <div class="bg-green-100 border border-green-300 text-green-800 p-3 rounded mb-4">{{ session('success') }}</div>
    @endif
    @if (session('error'))
    <div class="bg-red-100 border border-red-300 text-red-800 p-3 rounded mb-4">{{ session('error') }}</div>
    @endif

    @if (strpos(session()->get('error'), 'Please set your sign virtual first') !== false)
    <button data-bs-toggle="modal" data-bs-target="#setSignVirtualDoctor"
        class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded mb-4">
        Set Your Sign Virtual Here
    </button>
    @endif

    <div class="overflow-x-auto">
        <table id="datatable" class="min-w-full bg-white border rounded shadow-sm">
            <thead class="bg-gray-100">
                <tr class="text-left border-b text-sm text-gray-600">
                    <th class="p-2">No.</th>
                    <th class="p-2">Patient Name</th>
                    <th class="p-2">No Letter</th>
                    <th class="p-2">Type Surat</th>
                    <th class="p-2">Company</th>
                    <th class="p-2">No Employe</th>
                    <th class="p-2">Phone Number</th>
                    <th class="p-2">Date</th>
                    <th class="p-2">Outlet</th>
                    <th class="p-2">Doctor</th>
                    <th class="p-2">Status</th>
                    <th class="p-2">Edit?</th>
                    <th class="p-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($results as $index => $result)
                <tr class="border-b text-sm text-gray-700 hover:bg-gray-50 transition duration-200">
                    <td class="p-2">{{ $index + 1 }}</td>
                    <td class="p-2">{{ $result->patient->full_name ?? '-' }}</td>
                    <td class="p-2">{{ $result->no_letters ?? '-' }}</td>
                    <td class="p-2 uppercase">{{ $result->type }}</td>
                    <td class="p-2">{{ $result->company->name ?? '-' }}</td>
                    <td class="p-2">{{ $result->patient->identity ?? '-' }}</td>
                    <td class="p-2">{{ $result->patient->phone ?? '-' }}</td>
                    <td class="p-2">{{ \Carbon\Carbon::parse($result->date)->format('d M Y') }}</td>
                    <td class="p-2">{{ $result->outlet->name ?? '-' }}</td>
                    <td class="p-2">{{ $result->doctor->user->name ?? '-' }}</td>
                    <td class="p-2">
                        <span class="px-2 py-1 text-xs font-medium rounded 
                    {{ $result->document_name ? 'bg-blue-100 text-blue-700' : 'bg-yellow-100 text-yellow-700' }}">
                            {{ $result->document_name ? 'Selesai' : 'Draft' }}
                        </span>
                    </td>
                    <td class="p-2">
                        <span class="px-2 py-1 text-xs font-medium rounded 
                    {{ $result->document_name ? 'bg-grey-100 text-white-700' : 'bg-yellow-100 text-yellow-700' }}">
                           @if($result->edit == 'yes')
                            <span class="text-green-700">Yes</span>
                            @else
                            <span class="text-yellow-700">No</span>
                            @endif
                        </span>
                    </td>
                    <td class="p-2 flex flex-wrap gap-2">

                        <!-- Preview -->
                        @if(in_array($result->type, ['mc', 'skb']) && $result->document_name)
                            @php
                                $previewRoute = $result->type === 'mc'
                                    ? route('outlet.results.mc.preview', Crypt::encrypt($result->id))
                                    : route('outlet.results.skb.preview', Crypt::encrypt($result->id));
                            @endphp

                            <a href="{{ $previewRoute }}"
                                target="_blank"
                                class="inline-flex items-center gap-1 bg-indigo-100 hover:bg-indigo-200 text-indigo-800 text-xs font-medium px-3 py-1 rounded transition">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12H9m12 0A9 9 0 1112 3a9 9 0 019 9z" />
                                </svg>
                                Preview
                            </a>
                        @endif


                        <!-- Download -->
                       @if($result->type && $result->document_name)
                        <a href="{{ route('outlet.results.download', Crypt::encrypt($result->id)) }}"
                            target="_blank"
                            class="inline-flex items-center gap-1 bg-green-100 hover:bg-green-200 text-green-800 text-xs font-medium px-3 py-1 rounded transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1M7 10l5 5m0 0l5-5m-5 5V4" />
                            </svg>
                            Download
                        </a>
                    @endif

                        <!-- Edit -->
                        <!-- <a href="{{ route('outlet.results.' . $result->type . '.edit', $result->id) }}"
                            class="inline-flex items-center gap-1 bg-yellow-100 hover:bg-yellow-200 text-yellow-800 text-xs font-medium px-3 py-1 rounded transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5h10M3 12h18M3 19h10" />
                            </svg>
                            Edit
                        </a> -->

                        <!-- Regenerate PDF -->
                        @if($result->type === 'skb' && $result->document_name)
                        <a href="{{ route('outlet.results.skb.regenerate', $result->id) }}" class="inline-block"
                            onsubmit="return confirm('Generate ulang file PDF untuk surat ini?')">
                            @csrf
                            <button type="submit"
                                class="inline-flex items-center gap-1 bg-blue-100 hover:bg-blue-200 text-blue-800 text-xs font-medium px-3 py-1 rounded transition">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v1m0 14v1m8-8h1M4 12H3m15.364-6.364l.707.707M5.636 5.636l-.707.707m12.728 12.728l.707-.707M5.636 18.364l-.707-.707" />
                                </svg>
                                Generate PDF SKB
                            </button>
                        </a>
                        @elseif($result->type === 'mc' && $result->document_name)
                        <a href="{{ route('outlet.results.mc.regenerate', $result->id) }}" class="inline-block"
                            onsubmit="return confirm('Generate ulang file PDF untuk surat ini?')">
                            @csrf
                            <button type="submit"
                                class="inline-flex items-center gap-1 bg-blue-100 hover:bg-blue-200 text-blue-800 text-xs font-medium px-3 py-1 rounded transition">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v1m0 14v1m8-8h1M4 12H3m15.364-6.364l.707.707M5.636 5.636l-.707.707m12.728 12.728l.707-.707M5.636 18.364l-.707-.707" />
                                </svg>
                                Generate PDF MC
                            </button>
                        </a>
                        @endif

                        <!-- Delete -->
                        <form method="POST"
                            action="{{ route('outlet.results.' . $result->type . '.delete', $result->id) }}"
                            class="inline-block" onsubmit="return confirm('Hapus surat ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="inline-flex items-center gap-1 bg-red-100 hover:bg-red-200 text-red-800 text-xs font-medium px-3 py-1 rounded transition">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                Hapus
                            </button>
                        </form>

                    </td>

                </tr>
                @empty
                <tr>
                    <td colspan="12" class="text-center text-gray-500 p-4">Belum ada surat yang dibuat.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="mt-4">
            {{ $results->links() }}
        </div>
    </div>
</div>
@endsection