@extends('layouts.app', ['header' => 'Manajemen Outlet'])

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
     integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
     crossorigin=""/>
<style>
    /* Z-index penting agar peta tampil di atas backdrop modal */
    #map-modal { height: 400px; z-index: 1000; }
</style>
@endpush

@section('content')

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

<div class="bg-white p-6 sm:p-8 rounded-2xl shadow-lg" 
     x-data="{ 
        view: 'grid',
        showModal: false,
        outletName: '',
        outletLat: 0,
        outletLng: 0,
        mapInstance: null
     }">

    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 pb-6 border-b border-slate-200">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-slate-900">Manajemen Outlet</h1>
            <p class="mt-2 text-slate-500">Kelola semua outlet, pantau performa, dan atur status operasional.</p>
        </div>
        <div class="flex items-center gap-3 flex-shrink-0">
            <div class="bg-slate-100 border border-slate-200 p-1 rounded-lg flex items-center">
                <button @click="view = 'grid'" :class="view === 'grid' ? 'bg-white shadow text-blue-600' : 'text-slate-500 hover:text-slate-800'" class="p-1.5 rounded-md transition-colors duration-200" aria-label="Tampilan Grid">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" /></svg>
                </button>
                <button @click="view = 'table'" :class="view === 'table' ? 'bg-white shadow text-blue-600' : 'text-slate-500 hover:text-slate-800'" class="p-1.5 rounded-md transition-colors duration-200" aria-label="Tampilan Tabel">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 010 3.75H5.625a1.875 1.875 0 010-3.75z" /></svg>
                </button>
            </div>
            <a href="{{ route('superadmin.outlets.create') }}" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-blue-600 text-white font-semibold rounded-lg shadow-md hover:bg-blue-700 transition-all duration-200">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m6-6H6" /></svg>
                <span>Tambah Outlet</span>
            </a>
        </div>
    </div>

    <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-6">
        @php
            $avgLetters = $totalOutlets > 0 ? $totalLetters / $totalOutlets : 0;
            $statCards = [
                ['label' => 'Total Outlet', 'value' => number_format($totalOutlets), 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.25M19.5 21v-7.5a.75.75 0 00-.75-.75h-3a.75.75 0 00-.75.75V21m-4.5 0H2.25m19.5 0H2.25M4.5 9.75v8.25a2.25 2.25 0 002.25 2.25h10.5a2.25 2.25 0 002.25-2.25V9.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25z" />', 'color' => 'sky'],
                ['label' => 'Outlet Diblokir', 'value' => number_format($bannedOutlets), 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />', 'color' => 'red'],
                ['label' => 'Rata-rata Surat/Outlet', 'value' => number_format($avgLetters, 1), 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6a7.5 7.5 0 100 15 7.5 7.5 0 000-15z" /><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 10.5H21A7.5 7.5 0 0013.5 3v7.5z" />', 'color' => 'amber']
            ];
        @endphp
        @foreach ($statCards as $card)
        <div class="bg-slate-50 border border-slate-200 rounded-xl p-5 flex items-center gap-5">
             <div class="w-12 h-12 flex-shrink-0 flex items-center justify-center bg-{{$card['color']}}-100 rounded-full">
                <svg class="w-7 h-7 text-{{$card['color']}}-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">{!! $card['icon'] !!}</svg>
            </div>
            <div>
                <p class="text-sm text-slate-500">{{ $card['label'] }}</p>
                <p class="text-2xl font-bold text-slate-800 mt-1">{{ $card['value'] }}</p>
            </div>
        </div>
        @endforeach
    </div>

    <div class="mt-6 bg-slate-50 border border-slate-200 rounded-xl p-4">
        <form method="GET" class="flex flex-col md:flex-row gap-4 items-center">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, kota, atau email..."
                   class="w-full md:flex-1 rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
            <select name="is_active" class="w-full md:w-auto rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                <option value="">Semua Status</option>
                <option value="1" @selected(request('is_active') === '1')>Aktif</option>
                <option value="0" @selected(request('is_active') === '0')>Nonaktif</option>
            </select>
            <button type="submit" class="w-full md:w-auto px-4 py-2 bg-slate-800 text-white rounded-md text-sm font-semibold hover:bg-slate-700 transition">Filter</button>
            <a href="{{ route('superadmin.outlets.index') }}" class="w-full md:w-auto text-center px-4 py-2 bg-slate-200 text-slate-700 rounded-md text-sm font-medium hover:bg-slate-300 transition">Reset</a>
            <a href="#" class="w-full md:w-auto inline-flex items-center justify-center gap-2 px-4 py-2 border border-green-300 bg-green-50 text-green-700 rounded-md text-sm font-medium hover:bg-green-100 transition">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
                <span>Ekspor</span>
            </a>
        </form>
    </div>
    
    <div class="mt-6">
        <div x-show="view === 'table'" x-cloak x-transition.opacity>
            <div class="bg-white border border-slate-200 rounded-xl overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-slate-50 text-xs uppercase text-slate-500 tracking-wider">
                            <tr>
                                <th class="px-6 py-3 font-medium">Nama Outlet</th>
                                <th class="px-6 py-3 font-medium">Lokasi</th>
                                <th class="px-6 py-3 font-medium">Admin</th>
                                <th class="px-6 py-3 font-medium text-center">Status</th>
                                <th class="px-6 py-3 font-medium text-center">Statistik</th>
                                <th class="px-6 py-3 font-medium text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            @forelse ($outlets as $outlet)
                                <tr class="hover:bg-slate-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="font-semibold text-slate-800">{{ $outlet->name }}</div>
                                        <div class="text-slate-500 font-mono text-xs">{{ $outlet->code ?? 'N/A' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div>{{ $outlet->city ?? '-' }}, {{ $outlet->province ?? '-' }}</div>
                                        {{-- Tombol Peta --}}
                                        @if($outlet->latitude && $outlet->longitude)
                                            <button @click="showModal = true; outletName = '{{ addslashes($outlet->name) }}'; outletLat = {{ $outlet->latitude }}; outletLng = {{ $outlet->longitude }}; $nextTick(() => initModalMap.call($data))"
                                                    class="inline-flex items-center gap-1 mt-1 text-xs text-blue-600 hover:underline">
                                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" /></svg>
                                                Lihat Peta
                                            </button>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-slate-800">{{ $outlet->admin->user->name ?? '-' }}</div>
                                        <div class="text-slate-500 text-xs">{{ $outlet->admin->user->email ?? '' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        @if($outlet->is_active)
                                            <span class="inline-flex items-center gap-1.5 px-2 py-1 text-xs font-medium text-green-700 bg-green-100 rounded-full">
                                                <span class="h-1.5 w-1.5 rounded-full bg-green-500"></span> Aktif
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-2 py-1 text-xs font-medium text-red-700 bg-red-100 rounded-full">
                                                <span class="h-1.5 w-1.5 rounded-full bg-red-500"></span> Nonaktif
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-slate-600 text-center">
                                        {{ $outlet->doctors_count }} Dokter / {{ $outlet->letter_count }} Surat
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <x-kebab-menu :outlet="$outlet" />
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center text-slate-500">
                                        <svg class="w-12 h-12 mx-auto text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" /></svg>
                                        <p class="mt-4 font-semibold">Data Tidak Ditemukan</p>
                                        <p class="text-sm">Coba ubah kata kunci pencarian atau reset filter.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <div x-show="view === 'grid'" x-cloak x-transition.opacity>
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                @forelse ($outlets as $outlet)
                <div class="bg-white border border-slate-200 rounded-xl p-5 flex flex-col justify-between transition-all duration-300 hover:shadow-lg hover:-translate-y-1">
                    <div>
                        <div class="flex justify-between items-start mb-2">
                            <div class="font-bold text-slate-800 text-lg pr-4">{{ $outlet->name }}</div>
                            <x-kebab-menu :outlet="$outlet" />
                        </div>
                        <div class="text-sm text-slate-500">{{ $outlet->city ?? '-' }}, {{ $outlet->province ?? '-' }}</div>
                        
                        <div class="mt-4 pt-4 border-t border-slate-200 flex justify-around text-center">
                            <div>
                                <div class="font-bold text-lg text-slate-800">{{ $outlet->doctors_count }}</div>
                                <div class="text-xs text-slate-500">Dokter</div>
                            </div>
                            <div>
                                <div class="font-bold text-lg text-slate-800">{{ $outlet->letter_count }}</div>
                                <div class="text-xs text-slate-500">Surat</div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-5 pt-3 border-t border-slate-200 flex justify-between items-center">
                        <div class="text-xs text-slate-500">Admin: <span class="font-medium text-slate-700">{{ $outlet->admin->user->name ?? '-' }}</span></div>
                        <div class="flex items-center gap-3">
                            {{-- Tombol Peta --}}
                            @if($outlet->latitude && $outlet->longitude)
                                <button @click="showModal = true; outletName = '{{ addslashes($outlet->name) }}'; outletLat = {{ $outlet->latitude }}; outletLng = {{ $outlet->longitude }}; $nextTick(() => initModalMap.call($data))"
                                        class="inline-flex items-center gap-1.5 text-xs font-medium text-blue-600 hover:text-blue-800">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" /></svg>
                                    Peta
                                </button>
                            @endif
                            @if($outlet->is_active)
                                <span class="inline-flex items-center gap-1.5 text-xs font-medium text-green-700"><span class="h-1.5 w-1.5 rounded-full bg-green-500"></span>Aktif</span>
                            @else
                                <span class="inline-flex items-center gap-1.5 text-xs font-medium text-red-700"><span class="h-1.5 w-1.5 rounded-full bg-red-500"></span>Nonaktif</span>
                            @endif
                        </div>
                    </div>
                </div>
                @empty
                <div class="md:col-span-2 xl:col-span-3 text-center text-slate-500 py-12 bg-slate-50 rounded-xl border-2 border-dashed border-slate-200">
                    <svg class="w-16 h-16 mx-auto text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.25M19.5 21v-7.5a.75.75 0 00-.75-.75h-3a.75.75 0 00-.75.75V21m-4.5 0H2.25m19.5 0H2.25M4.5 9.75v8.25a2.25 2.25 0 002.25 2.25h10.5a2.25 2.25 0 002.25-2.25V9.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25z" /></svg>
                     <p class="mt-4 font-semibold">Belum Ada Outlet Terdaftar</p>
                     <p class="text-sm">Gunakan tombol "Tambah Outlet" untuk memulai.</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
    
    @if ($outlets->hasPages())
        <div class="mt-6">
            {{ $outlets->appends(request()->query())->links() }}
        </div>
    @endif

    <div x-show="showModal" x-cloak 
         @keydown.escape.window="showModal = false"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black bg-opacity-60 backdrop-blur-sm z-40 flex items-center justify-center p-4">
        <div @click.away="showModal = false" 
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="bg-white rounded-2xl shadow-xl w-full max-w-2xl">
            <div class="p-5 border-b border-slate-200 flex justify-between items-center">
                <div>
                    <h3 class="text-lg font-bold text-slate-800">Lokasi Outlet</h3>
                    <p class="text-sm text-slate-500" x-text="outletName"></p>
                </div>
                <button @click="showModal = false" class="p-2 text-slate-400 hover:text-slate-700 hover:bg-slate-100 rounded-full">&times;</button>
            </div>
            <div class="p-5">
                <div id="map-modal" class="rounded-lg overflow-hidden border border-slate-300"></div>
            </div>
        </div>
    </div>

</div> @endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
     integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
     crossorigin=""></script>
<script>
    // [IMPROVEMENT] Fungsi untuk inisialisasi peta di modal
    function initModalMap() {
        const alpineData = this;
        
        // Hancurkan instance peta sebelumnya jika ada, untuk mencegah error
        if (alpineData.mapInstance) {
            alpineData.mapInstance.remove();
        }

        // Inisialisasi peta baru
        const map = L.map('map-modal').setView([alpineData.outletLat, alpineData.outletLng], 15);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        L.marker([alpineData.outletLat, alpineData.outletLng]).addTo(map)
            .bindPopup(`<b>${alpineData.outletName}</b>`)
            .openPopup();
        
        // Simpan instance peta untuk dihancurkan nanti
        alpineData.mapInstance = map;

        // Leaflet terkadang butuh 'refresh' saat di-render di dalam modal yang tadinya hidden
        setTimeout(() => map.invalidateSize(), 100);
    }
</script>
@endpush