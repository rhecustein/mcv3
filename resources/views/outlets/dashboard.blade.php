@extends('layouts.app', ['header' => 'Dashboard Outlet'])

@section('content')
<div class="space-y-8">

    {{-- Welcome Header Section --}}
    <div class="bg-gradient-to-br from-blue-600 via-blue-700 to-indigo-800 rounded-2xl shadow-xl overflow-hidden">
        <div class="grid grid-cols-1 lg:grid-cols-2">
            <div class="p-8 lg:p-10 text-white">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.25M19.5 21v-7.5a.75.75 0 00-.75-.75h-3a.75.75 0 00-.75.75V21m-4.5 0H2.25m19.5 0H2.25M4.5 9.75v8.25a2.25 2.25 0 002.25 2.25h10.5a2.25 2.25 0 002.25-2.25V9.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25z"/>
                        </svg>
                    </div>
                    <span class="text-white/80 text-sm font-medium">{{ now()->format('l, d F Y') }}</span>
                </div>
                
                <h1 class="text-3xl lg:text-4xl font-bold mb-3">
                    Selamat Datang! 👋
                </h1>
                <p class="text-xl lg:text-2xl font-semibold text-blue-100 mb-4">
                    {{ $outlet->name }}
                </p>
                <p class="text-blue-100 mb-6 leading-relaxed">
                    Pusat kendali operasional Anda. Kelola dokter, pasien, dan terbitkan surat kesehatan dengan mudah dan efisien.
                </p>
                
                {{-- Outlet Info Cards --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-6">
                    <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-blue-200" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/>
                            </svg>
                            <div>
                                <p class="text-blue-200 text-sm">Email</p>
                                <p class="text-white font-medium">{{ $outlet->email ?? 'Belum diatur' }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-blue-200" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z"/>
                            </svg>
                            <div>
                                <p class="text-blue-200 text-sm">Telepon</p>
                                <p class="text-white font-medium">{{ $outlet->phone ?? 'Belum diatur' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                @if($outlet->address)
                <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 mt-4">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-blue-200 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/>
                        </svg>
                        <div>
                            <p class="text-blue-200 text-sm">Alamat</p>
                            <p class="text-white font-medium">{{ $outlet->address }}</p>
                        </div>
                    </div>
                </div>
                @endif
            </div>
            
            {{-- Map Section --}}
            <div class="relative lg:h-auto h-64 bg-slate-100">
                @if($outlet->latitude && $outlet->longitude)
                    <iframe 
                        src="https://maps.google.com/maps?q={{ $outlet->latitude }},{{ $outlet->longitude }}&hl=id&z=15&output=embed" 
                        class="w-full h-full border-0" 
                        allowfullscreen="" 
                        loading="lazy" 
                        referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                    <div class="absolute top-4 right-4">
                        <button onclick="openFullMap()" class="bg-white/90 backdrop-blur-sm hover:bg-white text-gray-700 px-3 py-2 rounded-lg shadow-lg text-sm font-medium transition-all duration-200">
                            <svg class="w-4 h-4 inline mr-1" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3.75v4.5m0-4.5h4.5m-4.5 0L9 9M3.75 20.25v-4.5m0 4.5h4.5m-4.5 0L9 15M20.25 3.75h-4.5m4.5 0v4.5m0-4.5L15 9m5.25 11.25h-4.5m4.5 0v-4.5m0 4.5L15 15"/>
                            </svg>
                            Lihat Peta
                        </button>
                    </div>
                @else
                    <div class="flex items-center justify-center h-full bg-slate-200">
                        <div class="text-center text-slate-500">
                            <svg class="w-12 h-12 mx-auto mb-2 opacity-50" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/>
                            </svg>
                            <p class="text-sm italic">Lokasi peta belum diatur</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Statistics Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 hover:shadow-md transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-slate-500 text-sm font-medium">Total Dokter</p>
                    <p class="text-3xl font-bold text-slate-900 mt-1">{{ $totalDoctors ?? 0 }}</p>
                    <p class="text-emerald-600 text-sm mt-1">
                        <svg class="w-4 h-4 inline mr-1" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18L9 11.25l4.306 4.307a11.95 11.95 0 010 2.843A11.948 11.948 0 0112 21c-1.268 0-2.49-.196-3.65-.571L5.25 21l.75-3m0 0L9 11.25m-3.75 6.75L9 15"/>
                        </svg>
                        Aktif
                    </p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 18.75a6 6 0 006-6v-1.5m-6 7.5a6 6 0 01-6-6v-1.5m6 7.5v3.75m-3.75 0h7.5M12 15.75a3 3 0 01-3-3V4.5a3 3 0 116 0v8.25a3 3 0 01-3 3z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 hover:shadow-md transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-slate-500 text-sm font-medium">Total Surat</p>
                    <p class="text-3xl font-bold text-slate-900 mt-1">{{ $totalLetters ?? 0 }}</p>
                    <p class="text-blue-600 text-sm mt-1">
                        <svg class="w-4 h-4 inline mr-1" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
                        </svg>
                        Diterbitkan
                    </p>
                </div>
                <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 hover:shadow-md transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-slate-500 text-sm font-medium">Bulan Ini</p>
                    <p class="text-3xl font-bold text-slate-900 mt-1">{{ collect($data ?? [])->sum() }}</p>
                    <p class="text-purple-600 text-sm mt-1">
                        <svg class="w-4 h-4 inline mr-1" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5a2.25 2.25 0 002.25-2.25m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5a2.25 2.25 0 012.25 2.25v7.5"/>
                        </svg>
                        {{ now()->format('F') }}
                    </p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5a2.25 2.25 0 002.25-2.25m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5a2.25 2.25 0 012.25 2.25v7.5"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 hover:shadow-md transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-slate-500 text-sm font-medium">Hari Ini</p>
                    <p class="text-3xl font-bold text-slate-900 mt-1">12</p>
                    <p class="text-amber-600 text-sm mt-1">
                        <svg class="w-4 h-4 inline mr-1" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-6.364-.386l1.591-1.591M3 12h2.25m.386-6.364l1.591 1.591"/>
                        </svg>
                        {{ now()->format('d M') }}
                    </p>
                </div>
                <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-amber-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-6.364-.386l1.591-1.591M3 12h2.25m.386-6.364l1.591 1.591"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <a href="{{ route('outlet.results.skb.create') }}" class="group bg-gradient-to-br from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white p-8 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
            <div class="flex items-center gap-6">
                <div class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-xl font-bold mb-1">Surat Keterangan Sehat</h3>
                    <p class="text-blue-100 text-sm">Buat surat sehat untuk keperluan umum</p>
                    <div class="flex items-center mt-3 text-blue-200">
                        <span class="text-sm font-medium">Mulai Buat</span>
                        <svg class="w-4 h-4 ml-2 group-hover:translate-x-1 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
                        </svg>
                    </div>
                </div>
            </div>
        </a>

        <a href="{{ route('outlet.results.mc.create') }}" class="group bg-gradient-to-br from-emerald-500 to-emerald-600 hover:from-emerald-600 hover:to-emerald-700 text-white p-8 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
            <div class="flex items-center gap-6">
                <div class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.623 0-1.602-.39-3.124-1.098-4.486"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-xl font-bold mb-1">Medical Certificate</h3>
                    <p class="text-emerald-100 text-sm">Buat surat sakit atau izin medis</p>
                    <div class="flex items-center mt-3 text-emerald-200">
                        <span class="text-sm font-medium">Mulai Buat</span>
                        <svg class="w-4 h-4 ml-2 group-hover:translate-x-1 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
                        </svg>
                    </div>
                </div>
            </div>
        </a>

        <a href="{{ route('outlet.patients.index') }}" class="group bg-gradient-to-br from-purple-500 to-purple-600 hover:from-purple-600 hover:to-purple-700 text-white p-8 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
            <div class="flex items-center gap-6">
                <div class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.003c0 1.113.285 2.16.786 3.07m-9.328-1.635a9.38 9.38 0 012.625-.372 9.337 9.337 0 014.121.952 4.125 4.125 0 01-7.533 2.493m-3.328 1.635c1.256-.368 2.355-.936 3.328-1.635m0 0a13.792 13.792 0 00-3.328-1.635"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-xl font-bold mb-1">Kelola Pasien</h3>
                    <p class="text-purple-100 text-sm">Lihat dan kelola data pasien</p>
                    <div class="flex items-center mt-3 text-purple-200">
                        <span class="text-sm font-medium">Lihat Daftar</span>
                        <svg class="w-4 h-4 ml-2 group-hover:translate-x-1 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
                        </svg>
                    </div>
                </div>
            </div>
        </a>
    </div>

    {{-- Charts Section --}}
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
        {{-- Line Chart --}}
        <div class="xl:col-span-2 bg-white p-8 rounded-2xl shadow-sm border border-slate-200">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-xl font-bold text-slate-900">📈 Tren Penerbitan Surat</h2>
                    <p class="text-slate-500 text-sm mt-1">Grafik perkembangan penerbitan surat 12 bulan terakhir</p>
                </div>
                <div class="flex items-center gap-2">
                    <div class="flex items-center gap-2 text-sm">
                        <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                        <span class="text-slate-600">Jumlah Surat</span>
                    </div>
                </div>
            </div>
            <div class="h-80">
                <canvas id="chartSurat"></canvas>
            </div>
        </div>

        {{-- Doughnut Chart --}}
        <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-200">
            <div class="mb-6">
                <h2 class="text-xl font-bold text-slate-900">📊 Komposisi Surat</h2>
                <p class="text-slate-500 text-sm mt-1">Distribusi jenis surat yang diterbitkan</p>
            </div>
            <div class="h-80 flex items-center justify-center">
                <canvas id="compositionChart"></canvas>
            </div>
            <div class="mt-6 space-y-3">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-4 h-4 bg-blue-500 rounded-full"></div>
                        <span class="text-slate-700 font-medium">Surat Sehat</span>
                    </div>
                    <span class="text-slate-900 font-bold">65%</span>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-4 h-4 bg-emerald-500 rounded-full"></div>
                        <span class="text-slate-700 font-medium">Medical Certificate</span>
                    </div>
                    <span class="text-slate-900 font-bold">35%</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Recent Activity --}}
    @if(isset($latestLetters) && count($latestLetters) > 0)
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-8 border-b border-slate-200">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-bold text-slate-900">🔄 Aktivitas Terbaru</h2>
                    <p class="text-slate-500 text-sm mt-1">Surat yang baru saja diterbitkan</p>
                </div>
                <a href="{{ route('outlet.healthletter.index') }}" class="text-blue-600 hover:text-blue-700 font-medium text-sm flex items-center gap-1">
                    Lihat Semua
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
                    </svg>
                </a>
            </div>
        </div>
        <div class="divide-y divide-slate-100">
            @foreach($latestLetters as $letter)
            <div class="p-6 hover:bg-slate-50 transition-colors duration-200">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-100 to-blue-200 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="font-semibold text-slate-900">{{ $letter->patient->user->name ?? 'Nama Pasien' }}</p>
                            <div class="flex items-center gap-3 mt-1">
                                <span class="text-sm text-slate-500">
                                    Dokter: {{ $letter->doctor->user->name ?? 'Dokter' }}
                                </span>
                                @if(isset($letter->type))
                                <span class="px-2 py-1 bg-blue-100 text-blue-700 text-xs font-medium rounded-full">
                                    {{ $letter->type === 'skb' ? 'Surat Sehat' : 'Medical Certificate' }}
                                </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-medium text-slate-900">{{ $letter->created_at->format('d M Y') }}</p>
                        <p class="text-xs text-slate-500">{{ $letter->created_at->format('H:i') }}</p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Performance Metrics --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-200">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-bold text-slate-900">⚡ Performa Outlet</h3>
                    <p class="text-slate-500 text-sm mt-1">Metrik kinerja bulan ini</p>
                </div>
            </div>
            
            <div class="space-y-6">
                <div class="flex items-center justify-between p-4 bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="font-semibold text-slate-900">Rata-rata Penerbitan</p>
                            <p class="text-sm text-slate-500">Per hari kerja</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-2xl font-bold text-emerald-600">8.5</p>
                        <p class="text-xs text-emerald-600">surat/hari</p>
                    </div>
                </div>

                <div class="flex items-center justify-between p-4 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="font-semibold text-slate-900">Waktu Rata-rata</p>
                            <p class="text-sm text-slate-500">Proses penerbitan</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-2xl font-bold text-blue-600">15</p>
                        <p class="text-xs text-blue-600">menit</p>
                    </div>
                </div>

                <div class="flex items-center justify-between p-4 bg-gradient-to-r from-purple-50 to-violet-50 rounded-xl">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 013 3h-15a3 3 0 013-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.007 0H9.497m5.007 0a7.454 7.454 0 01-.982-3.172M9.497 14.25a7.454 7.454 0 00.981-3.172M15.75 4.5V2.25m0 2.25V6m0-1.5h4.5m-4.5 0h-2.25m0 0V2.25m0 2.25V6m-2.25-1.5H6.75m0 0V2.25m0 2.25V6"/>
                            </svg>
                        </div>
                        <div>
                            <p class="font-semibold text-slate-900">Tingkat Kepuasan</p>
                            <p class="text-sm text-slate-500">Feedback pasien</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-2xl font-bold text-purple-600">4.8</p>
                        <p class="text-xs text-purple-600">dari 5.0</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Quick Links --}}
        <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-200">
            <div class="mb-6">
                <h3 class="text-lg font-bold text-slate-900">🚀 Akses Cepat</h3>
                <p class="text-slate-500 text-sm mt-1">Fitur yang sering digunakan</p>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <a href="{{ route('outlet.doctors.index') }}" class="group p-4 bg-slate-50 hover:bg-slate-100 rounded-xl transition-all duration-200">
                    <div class="w-10 h-10 bg-blue-100 group-hover:bg-blue-200 rounded-lg flex items-center justify-center mb-3 transition-colors">
                        <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 18.75a6 6 0 006-6v-1.5m-6 7.5a6 6 0 01-6-6v-1.5m6 7.5v3.75m-3.75 0h7.5M12 15.75a3 3 0 01-3-3V4.5a3 3 0 116 0v8.25a3 3 0 01-3 3z"/>
                        </svg>
                    </div>
                    <p class="font-semibold text-slate-900 text-sm">Kelola Dokter</p>
                    <p class="text-xs text-slate-500 mt-1">{{ $totalDoctors ?? 0 }} dokter</p>
                </a>

                <a href="{{ route('outlet.reports.index') }}" class="group p-4 bg-slate-50 hover:bg-slate-100 rounded-xl transition-all duration-200">
                    <div class="w-10 h-10 bg-emerald-100 group-hover:bg-emerald-200 rounded-lg flex items-center justify-center mb-3 transition-colors">
                        <svg class="w-5 h-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m6 10.125h.008v.008H14.25v-.008zM15.75 18h.008v.008h-.008V18zM13.5 15.75h.008v.008H13.5v-.008zM11.25 18h.008v.008h-.008V18zM9 15.75h.008v.008H9v-.008zM15.75 12.75a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <p class="font-semibold text-slate-900 text-sm">Laporan</p>
                    <p class="text-xs text-slate-500 mt-1">Export data</p>
                </a>

                <a href="{{ route('outlet.statistics.index') }}" class="group p-4 bg-slate-50 hover:bg-slate-100 rounded-xl transition-all duration-200">
                    <div class="w-10 h-10 bg-purple-100 group-hover:bg-purple-200 rounded-lg flex items-center justify-center mb-3 transition-colors">
                        <svg class="w-5 h-5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 006 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0118 16.5h-2.25m-7.5 0h7.5m-7.5 0l-1 3m8.5-3l1 3m0 0l.5 1.5m-.5-1.5h-9.5m0 0l-.5 1.5M9 11.25v1.5M12 9v3.75m3-6v6"/>
                        </svg>
                    </div>
                    <p class="font-semibold text-slate-900 text-sm">Statistik</p>
                    <p class="text-xs text-slate-500 mt-1">Analytics</p>
                </a>

                <a href="{{ route('outlet.queue.index') }}" class="group p-4 bg-slate-50 hover:bg-slate-100 rounded-xl transition-all duration-200">
                    <div class="w-10 h-10 bg-amber-100 group-hover:bg-amber-200 rounded-lg flex items-center justify-center mb-3 transition-colors">
                        <svg class="w-5 h-5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.348a1.125 1.125 0 010 1.971l-11.54 6.347a1.125 1.125 0 01-1.667-.985V5.653z"/>
                        </svg>
                    </div>
                    <p class="font-semibold text-slate-900 text-sm">Queue Monitor</p>
                    <p class="text-xs text-slate-500 mt-1">Status proses</p>
                </a>
            </div>

            <div class="mt-6 pt-6 border-t border-slate-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="font-semibold text-slate-900 text-sm">Status Sistem</p>
                        <p class="text-xs text-slate-500 mt-1">Semua layanan berjalan normal</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 bg-emerald-400 rounded-full animate-pulse"></div>
                        <span class="text-sm font-medium text-emerald-600">Online</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Full Map Modal --}}
<div id="mapModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden opacity-0 transition-opacity duration-300">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl h-[600px] overflow-hidden transform scale-95 transition-transform duration-300" id="mapModalContent">
            <div class="flex items-center justify-between p-6 border-b border-slate-200">
                <div>
                    <h3 class="text-lg font-bold text-slate-900">Lokasi {{ $outlet->name }}</h3>
                    <p class="text-sm text-slate-500 mt-1">{{ $outlet->address ?? 'Alamat outlet' }}</p>
                </div>
                <button onclick="closeFullMap()" class="w-10 h-10 bg-slate-100 hover:bg-slate-200 rounded-lg flex items-center justify-center transition-colors">
                    <svg class="w-5 h-5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div class="h-full">
                @if($outlet->latitude && $outlet->longitude)
                <iframe 
                    src="https://maps.google.com/maps?q={{ $outlet->latitude }},{{ $outlet->longitude }}&hl=id&z=16&output=embed" 
                    class="w-full h-full border-0" 
                    allowfullscreen="" 
                    loading="lazy" 
                    referrerpolicy="no-referrer-when-downgrade">
                </iframe>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Chart.js Configuration
    Chart.defaults.color = '#64748b';
    Chart.defaults.borderColor = '#e2e8f0';
    Chart.defaults.font.family = "'Inter', system-ui, sans-serif";

    // Line Chart - Enhanced
    const ctxLine = document.getElementById('chartSurat').getContext('2d');
    const gradient = ctxLine.createLinearGradient(0, 0, 0, 320);
    gradient.addColorStop(0, 'rgba(59, 130, 246, 0.1)');
    gradient.addColorStop(1, 'rgba(59, 130, 246, 0)');
    
    new Chart(ctxLine, {
        type: 'line',
        data: {
            labels: @json($labels ?? []),
            datasets: [{
                label: 'Jumlah Surat',
                data: @json($data ?? []),
                backgroundColor: gradient,
                borderColor: '#3b82f6',
                borderWidth: 3,
                tension: 0.4,
                fill: true,
                pointBackgroundColor: '#fff',
                pointBorderColor: '#3b82f6',
                pointBorderWidth: 3,
                pointRadius: 6,
                pointHoverRadius: 8,
                pointHoverBackgroundColor: '#3b82f6',
                pointHoverBorderColor: '#fff',
                pointHoverBorderWidth: 3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                intersect: false,
                mode: 'index'
            },
            plugins: { 
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1e293b',
                    titleColor: '#f1f5f9',
                    bodyColor: '#f1f5f9',
                    borderColor: '#3b82f6',
                    borderWidth: 1,
                    cornerRadius: 12,
                    displayColors: false,
                    padding: 12
                }
            },
            scales: { 
                y: { 
                    beginAtZero: true,
                    grid: {
                        color: '#f1f5f9',
                        drawBorder: false
                    },
                    ticks: {
                        padding: 8,
                        font: {
                            size: 12
                        }
                    }
                }, 
                x: { 
                    grid: { 
                        display: false,
                        drawBorder: false
                    },
                    ticks: {
                        padding: 8,
                        font: {
                            size: 12
                        }
                    }
                }
            },
            elements: {
                point: {
                    hoverBorderWidth: 3
                }
            }
        }
    });

    // Doughnut Chart - Enhanced
    const ctxDoughnut = document.getElementById('compositionChart').getContext('2d');
    new Chart(ctxDoughnut, {
        type: 'doughnut',
        data: {
            labels: ['Surat Sehat', 'Medical Certificate'],
            datasets: [{
                label: 'Jumlah',
                data: [65, 35],
                backgroundColor: [
                    '#3b82f6', // blue-500
                    '#10b981'  // emerald-500
                ],
                borderColor: '#fff',
                borderWidth: 6,
                hoverBorderWidth: 8,
                hoverOffset: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '65%',
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: '#1e293b',
                    titleColor: '#f1f5f9',
                    bodyColor: '#f1f5f9',
                    borderColor: '#3b82f6',
                    borderWidth: 1,
                    cornerRadius: 12,
                    padding: 12,
                    callbacks: {
                        label: function(context) {
                            return context.label + ': ' + context.parsed + '%';
                        }
                    }
                }
            },
            animation: {
                animateRotate: true,
                animateScale: true,
                duration: 1000
            }
        }
    });
});

// Map Modal Functions
function openFullMap() {
    const modal = document.getElementById('mapModal');
    const content = document.getElementById('mapModalContent');
    
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    
    setTimeout(() => {
        modal.classList.remove('opacity-0');
        content.classList.remove('scale-95');
        content.classList.add('scale-100');
    }, 10);
}

function closeFullMap() {
    const modal = document.getElementById('mapModal');
    const content = document.getElementById('mapModalContent');
    
    modal.classList.add('opacity-0');
    content.classList.remove('scale-100');
    content.classList.add('scale-95');
    
    setTimeout(() => {
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
    }, 300);
}

// Close modal when clicking outside
document.getElementById('mapModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeFullMap();
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeFullMap();
    }
});
</script>
@endpush