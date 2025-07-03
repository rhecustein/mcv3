@extends('layouts.app', ['header' => 'Manajemen Paket Langganan'])

@section('content')
<div class="space-y-8">

    <div aria-live="assertive" class="pointer-events-none fixed inset-0 flex items-start px-4 py-6 sm:p-6 z-50">
        <div class="flex w-full flex-col items-center space-y-4 sm:items-end">
             @if (session('success'))
                <x-alert type="success" :message="session('success')" />
            @endif
             @if (session('error'))
                <x-alert type="error" :message="session('error')" />
            @endif
        </div>
    </div>

    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Manajemen Paket</h1>
            <p class="mt-1 text-sm text-slate-500">Buat, kelola, dan atur paket langganan untuk perusahaan klien.</p>
        </div>
        <a href="{{ route('packages.create') }}"
           class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-blue-600 text-white font-semibold rounded-lg shadow-md hover:bg-blue-700 transition-all duration-200">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m6-6H6" /></svg>
            <span>Tambah Paket Baru</span>
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        @php
            // Anda perlu mengirimkan data ini dari controller
            $mostPopularPackage = $packages->firstWhere('id', 2) ?? $packages->first(); // Contoh logika
            $activeSubscriptions = 50; // Contoh data
        @endphp
        <div class="bg-white border border-slate-200 rounded-xl p-5 flex items-center gap-5">
             <div class="w-12 h-12 flex-shrink-0 flex items-center justify-center bg-sky-100 rounded-lg">
                <svg class="w-7 h-7 text-sky-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 11.25v8.25a2.25 2.25 0 01-2.25 2.25H5.25a2.25 2.25 0 01-2.25-2.25v-8.25a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 11.25z" /></svg>
            </div>
            <div>
                <p class="text-sm text-slate-500">Total Paket Tersedia</p>
                <p class="text-2xl font-bold text-slate-800 mt-1">{{ $totalPackages }}</p>
            </div>
        </div>
        <div class="bg-white border border-slate-200 rounded-xl p-5 flex items-center gap-5">
             <div class="w-12 h-12 flex-shrink-0 flex items-center justify-center bg-amber-100 rounded-lg">
                <svg class="w-7 h-7 text-amber-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.324h5.362a.562.562 0 01.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.562.562 0 01.321-.988h5.362a.563.563 0 00.475-.324L11.48 3.5z" /></svg>
            </div>
            <div>
                <p class="text-sm text-slate-500">Paket Paling Populer</p>
                <p class="text-2xl font-bold text-slate-800 mt-1">{{ $mostPopularPackage->name ?? 'N/A' }}</p>
            </div>
        </div>
         <div class="bg-white border border-slate-200 rounded-xl p-5 flex items-center gap-5">
             <div class="w-12 h-12 flex-shrink-0 flex items-center justify-center bg-green-100 rounded-lg">
                <svg class="w-7 h-7 text-green-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.003c0 1.113.285 2.16.786 3.07m-9.328-1.635a9.38 9.38 0 012.625-.372 9.337 9.337 0 014.121.952 4.125 4.125 0 01-7.533 2.493m-3.328 1.635c1.256.368 2.355.936 3.328-1.635m0 0a13.792 13.792 0 00-3.328-1.635" /></svg>
            </div>
            <div>
                <p class="text-sm text-slate-500">Total Langganan Aktif</p>
                <p class="text-2xl font-bold text-slate-800 mt-1">{{ $activeSubscriptions ?? 0 }}</p>
            </div>
        </div>
    </div>

    <div class="space-y-8" x-data="{ billing: 'monthly' }">
        <div class="flex items-center justify-center gap-4">
            <span :class="billing === 'monthly' ? 'text-blue-600 font-semibold' : 'text-slate-500'">Bulanan</span>
            <button @click="billing = billing === 'monthly' ? 'annually' : 'monthly'" type="button" class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-blue-600 focus:ring-offset-2" :class="billing === 'annually' ? 'bg-blue-600' : 'bg-slate-300'" role="switch" aria-checked="false">
                <span aria-hidden="true" class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out" :class="billing === 'annually' ? 'translate-x-5' : 'translate-x-0'"></span>
            </button>
            <span :class="billing === 'annually' ? 'text-blue-600 font-semibold' : 'text-slate-500'">Tahunan <span class="text-green-600">(Hemat 20%)</span></span>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            @forelse ($packages as $package)
                @php 
                    $isPopular = ($package->name === 'Pro'); // Logika untuk menandai paket populer
                @endphp
                <div class="border rounded-2xl p-8 flex flex-col relative {{ $isPopular ? 'border-blue-500 ring-2 ring-blue-500 shadow-2xl' : 'border-slate-200 shadow-lg' }}">
                    @if($isPopular)
                        <p class="absolute -top-3 left-1/2 -translate-x-1/2 bg-blue-500 text-white text-xs font-semibold px-3 py-1 rounded-full">Paling Populer</p>
                    @endif
                    <h3 class="text-lg font-semibold text-slate-800">{{ $package->name }}</h3>
                    <p class="mt-4 text-slate-500 text-sm h-12">{{ $package->description ?? 'Deskripsi default untuk paket ini.' }}</p>
                    <div class="mt-6">
                        <span class="text-4xl font-bold text-slate-900">Rp {{ number_format($package->price) }}</span>
                        <span class="text-base font-medium text-slate-500">/bulan</span>
                    </div>

                    <ul role="list" class="mt-8 space-y-3 text-sm leading-6 text-slate-600 flex-grow">
                        <li class="flex gap-x-3">
                            <svg class="h-6 w-5 flex-none text-blue-600" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" /></svg>
                            {{ $package->duration_in_days }} Hari Durasi Aktif
                        </li>
                        <li class="flex gap-x-3">
                            <svg class="h-6 w-5 flex-none text-blue-600" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" /></svg>
                            Maksimal <strong>{{ $package->max_letters ?? 'Tak Terbatas' }}</strong> Surat
                        </li>
                         <li class="flex gap-x-3">
                            <svg class="h-6 w-5 flex-none text-blue-600" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" /></svg>
                            Maksimal <strong>{{ $package->max_patients ?? 'Tak Terbatas' }}</strong> Pasien
                        </li>
                    </ul>

                    <div class="mt-8 flex flex-col gap-2">
                         <a href="{{ route('packages.edit', $package) }}" class="w-full text-center px-4 py-2.5 text-sm bg-blue-600 text-white rounded-md font-semibold hover:bg-blue-700 transition">Edit Paket</a>
                         <form action="{{ route('packages.destroy', $package) }}" method="POST" class="w-full" onsubmit="return confirm('Yakin ingin menghapus paket ini?')">
                            @csrf @method('DELETE')
                             <button type="submit" class="w-full text-center px-4 py-2.5 text-sm text-red-600 rounded-md font-semibold hover:bg-red-50 transition">Hapus</button>
                         </form>
                    </div>
                </div>
            @empty
                <div class="lg:col-span-3 text-center text-slate-500 py-12">
                     <p class="font-semibold">Belum Ada Paket</p>
                     <p class="text-sm">Gunakan tombol "Tambah Paket Baru" untuk memulai.</p>
                </div>
            @endforelse
        </div>
    </div>

    @if ($packages->hasPages())
        <div class="mt-8">
            {{ $packages->links() }}
        </div>
    @endif
</div>
@endsection