<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Dasbor - Surat Sehat v3' }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Inter', sans-serif; }

        /* [IMPROVEMENT] Latar belakang grid yang konsisten */
        .grid-background {
            background-color: #f8fafc; /* bg-slate-50 */
            background-image:
                linear-gradient(to right, #e2e8f0 1px, transparent 1px),
                linear-gradient(to bottom, #e2e8f0 1px, transparent 1px);
            background-size: 40px 40px;
        }

        /* [IMPROVEMENT] Scrollbar kustom yang lebih modern & menyatu */
        .custom-scrollbar::-webkit-scrollbar { width: 8px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background-color: rgba(0, 0, 0, 0.2); border-radius: 20px; border: 2px solid transparent; background-clip: content-box; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background-color: rgba(0, 0, 0, 0.3); }

        /* Tips: Gaya untuk link navigasi yang aktif di dalam layouts.navigation */
        .nav-link.active { 
            background-color: #eef2ff; /* bg-indigo-50 */
            color: #4338ca; /* text-indigo-700 */
            font-weight: 600; /* font-semibold */
        }
    </style>
    @stack('styles')
</head>
<body class="h-full bg-slate-50 text-slate-800 antialiased">

{{-- [IMPROVEMENT] Container utama untuk latar belakang yang konsisten --}}
<div class="relative h-screen w-full">
    <div class="absolute inset-0 -z-10 h-full w-full">
        <div class="grid-background h-full w-full"></div>
        <div class="absolute inset-0 h-full w-full bg-[radial-gradient(circle_1000px_at_10%_10%,#d5f5f6,transparent)]"></div>
    </div>

    <div class="flex h-screen overflow-hidden">

        {{-- [IMPROVEMENT] Sidebar dengan efek "glassmorphism" --}}
        <aside class="w-64 bg-white/80 backdrop-blur-xl border-r border-slate-200/80 hidden md:flex flex-col justify-between ring-1 ring-black/5">
            <div class="p-4 custom-scrollbar overflow-y-auto">
                <div class="flex items-center space-x-3 mb-8 px-2">
                    <div class="w-10 h-10 rounded-lg bg-gradient-to-tr from-blue-500 to-cyan-500 flex items-center justify-center shadow-lg shadow-cyan-500/30">
                        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                    <h1 class="text-xl font-bold text-slate-800 tracking-tight">
                        Surat<span class="text-blue-600">Sehat</span>
                    </h1>
                </div>
                
                @include('layouts.navigation')

            </div>

            <div class="p-4 border-t border-slate-200/80">
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" class="w-full flex items-center justify-between p-2 rounded-lg hover:bg-white/50 transition-colors duration-200">
                        <div class="flex items-center space-x-3">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=dbeafe&color=1e3a8a"
                                 class="w-9 h-9 rounded-full ring-2 ring-white/50" alt="Avatar">
                            <div class="text-left">
                                <div class="text-sm font-semibold text-slate-800">{{ Auth::user()->name }}</div>
                                <div class="text-xs text-slate-500 capitalize">{{ Auth::user()->role }}</div>
                            </div>
                        </div>
                        <svg class="w-5 h-5 text-slate-500 transition-transform" :class="{ 'rotate-180': open }" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                    </button>
                    <div x-show="open" @click.away="open = false"
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="absolute bottom-full left-0 mb-2 w-full bg-white/95 backdrop-blur-xl border border-slate-200 rounded-lg shadow-xl z-50 p-2 text-sm space-y-1" style="display: none;">
                        
                        {{-- Profile Section --}}
                        @if(Auth::user()->role_type === 'superadmin')
                            <a href="{{ route('profile.show', encrypt(Auth::user()->id)) }}" class="flex items-center w-full px-3 py-2 rounded-md text-slate-700 hover:bg-slate-100 hover:text-slate-900 transition">
                                <svg class="w-4 h-4 mr-2 text-slate-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" /></svg>
                                Profil Saya
                            </a>
                        @elseif(Auth::user()->role_type === 'outlet')
                            <a href="{{ route('outlet.profile.edit') }}" class="flex items-center w-full px-3 py-2 rounded-md text-slate-700 hover:bg-slate-100 hover:text-slate-900 transition">
                                <svg class="w-4 h-4 mr-2 text-slate-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" /></svg>
                                Profil Outlet
                            </a>
                        @else
                            <a href="{{ route('settings.profile.edit') }}" class="flex items-center w-full px-3 py-2 rounded-md text-slate-700 hover:bg-slate-100 hover:text-slate-900 transition">
                                <svg class="w-4 h-4 mr-2 text-slate-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" /></svg>
                                Profil Saya
                            </a>
                        @endif
                        
                        {{-- Settings Section --}}
                        @if(Auth::user()->role_type === 'outlet')
                            <a href="{{ route('outlet.profile.settings') }}" class="flex items-center w-full px-3 py-2 rounded-md text-slate-700 hover:bg-slate-100 hover:text-slate-900 transition">
                                 <svg class="w-4 h-4 mr-2 text-slate-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.942 1.11-1.06C10.825 2.74 11.175 2.74 11.297 2.88c.122.14.122.36.122.58 0 .22-.18.4-.4.4H9.594zM9.594 3.94L8.47 9.375c-.09.436-.18.872-.18 1.313 0 .612.5 1.125 1.125 1.125H13.5c.621 0 1.125-.504 1.125-1.125 0-.441-.09-.877-.18-1.313L13.32 3.94M9.594 3.94L13.32 3.94m-3.726 0H7.875c-.621 0-1.125.504-1.125 1.125V18c0 .621.504 1.125 1.125 1.125h8.25c.621 0 1.125-.504 1.125-1.125V5.065c0-.621-.504-1.125-1.125-1.125h-1.719" /></svg>
                                Pengaturan
                            </a>
                        @else
                            <a href="{{ route('settings.index') }}" class="flex items-center w-full px-3 py-2 rounded-md text-slate-700 hover:bg-slate-100 hover:text-slate-900 transition">
                                 <svg class="w-4 h-4 mr-2 text-slate-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.942 1.11-1.06C10.825 2.74 11.175 2.74 11.297 2.88c.122.14.122.36.122.58 0 .22-.18.4-.4.4H9.594zM9.594 3.94L8.47 9.375c-.09.436-.18.872-.18 1.313 0 .612.5 1.125 1.125 1.125H13.5c.621 0 1.125-.504 1.125-1.125 0-.441-.09-.877-.18-1.313L13.32 3.94M9.594 3.94L13.32 3.94m-3.726 0H7.875c-.621 0-1.125.504-1.125 1.125V18c0 .621.504 1.125 1.125 1.125h8.25c.621 0 1.125-.504 1.125-1.125V5.065c0-.621-.504-1.125-1.125-1.125h-1.719" /></svg>
                                Pengaturan
                            </a>
                        @endif

                        {{-- Session Management for Outlets --}}
                        @if(Auth::user()->role_type === 'outlet')
                            <a href="{{ route('outlet.sessions.index') }}" class="flex items-center w-full px-3 py-2 rounded-md text-slate-700 hover:bg-slate-100 hover:text-slate-900 transition">
                                <svg class="w-4 h-4 mr-2 text-slate-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-6.75 0L15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                Kelola Sesi
                            </a>
                        @endif

                        {{-- Activity Logs --}}
                        @if(Auth::user()->role_type === 'outlet')
                            <a href="{{ route('outlet.profile.activity') }}" class="flex items-center w-full px-3 py-2 rounded-md text-slate-700 hover:bg-slate-100 hover:text-slate-900 transition">
                                <svg class="w-4 h-4 mr-2 text-slate-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 006 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0118 16.5h-2.25m-7.5 0h7.5m-7.5 0l-1 3m8.5-3l1 3m0 0l-1-3m1 3l-1-3m-16.5 0h16.5" /></svg>
                                Aktivitas
                            </a>
                        @else
                            <a href="{{ route('settings.activity') }}" class="flex items-center w-full px-3 py-2 rounded-md text-slate-700 hover:bg-slate-100 hover:text-slate-900 transition">
                                <svg class="w-4 h-4 mr-2 text-slate-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 006 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0118 16.5h-2.25m-7.5 0h7.5m-7.5 0l-1 3m8.5-3l1 3m0 0l-1-3m1 3l-1-3m-16.5 0h16.5" /></svg>
                                Aktivitas
                            </a>
                        @endif

                        {{-- Notifications --}}
                        @if(Auth::user()->role_type === 'outlet')
                            <a href="{{ route('outlet.profile.notifications') }}" class="flex items-center w-full px-3 py-2 rounded-md text-slate-700 hover:bg-slate-100 hover:text-slate-900 transition">
                                <svg class="w-4 h-4 mr-2 text-slate-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" /></svg>
                                Notifikasi
                            </a>
                        @else
                            <a href="{{ route('settings.notifications') }}" class="flex items-center w-full px-3 py-2 rounded-md text-slate-700 hover:bg-slate-100 hover:text-slate-900 transition">
                                <svg class="w-4 h-4 mr-2 text-slate-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" /></svg>
                                Notifikasi
                            </a>
                        @endif

                        <div class="!my-2 border-t border-slate-200"></div>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full flex items-center px-3 py-2 rounded-md text-red-600 hover:bg-red-50 hover:text-red-700 transition">
                                <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" /></svg>
                                Keluar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </aside>

        {{-- [IMPROVEMENT] Area konten utama dibuat transparan agar latar belakang terlihat --}}
        <div class="flex-1 flex flex-col bg-transparent">
            {{-- [IMPROVEMENT] Header dengan efek "glassmorphism" --}}
            <header class="w-full bg-white/80 backdrop-blur-xl px-6 py-3 flex justify-between items-center border-b border-slate-200/80 z-10 ring-1 ring-black/5">
                <div>
                    <h1 class="text-lg font-semibold text-slate-900">{{ $header ?? 'Dasbor' }}</h1>
                </div>
                <div class="flex items-center space-x-5">
                    @php
                        $user = Auth::user();
                        $activeSessions = $user->active_sessions_count ?? 1;
                        $maxSessions = 3;
                    @endphp
                    
                    {{-- Session Status Indicator --}}
                    @if(Auth::user()->role_type === 'outlet')
                        <a href="{{ route('outlet.sessions.index') }}" class="flex items-center space-x-2 px-3 py-1.5 rounded-lg hover:bg-white/50 transition group">
                            <svg class="w-4 h-4 text-slate-500 group-hover:text-slate-700" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-6.75 0L15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="text-sm text-slate-600 hidden sm:inline group-hover:text-slate-800">Sesi</span>
                            <span class="px-2 py-0.5 text-xs font-semibold rounded-full
                                @if($activeSessions >= $maxSessions) bg-red-100 text-red-800 border border-red-200
                                @elseif($activeSessions === ($maxSessions - 1)) bg-yellow-100 text-yellow-800 border border-yellow-200
                                @else bg-green-100 text-green-800 border border-green-200
                                @endif">
                                {{ $activeSessions }}/{{ $maxSessions }}
                            </span>
                        </a>
                    @endif

                    {{-- Notifications Bell --}}
                    <div x-data="{ open: false, count: 0 }" class="relative">
                        <button @click="open = !open" class="relative p-2 text-slate-600 hover:text-slate-800 hover:bg-white/50 rounded-lg transition-colors">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                            </svg>
                            <span x-show="count > 0" class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center" x-text="count"></span>
                        </button>
                        
                        <div x-show="open" @click.away="open = false"
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95"
                             class="absolute right-0 mt-2 w-80 bg-white/95 backdrop-blur-xl border border-slate-200 rounded-xl shadow-xl z-50 p-4" style="display: none;">
                            
                            <div class="flex items-center justify-between mb-3">
                                <h3 class="text-sm font-semibold text-slate-800">Notifikasi</h3>
                                <a href="{{ route('settings.notifications') }}" class="text-xs text-blue-600 hover:text-blue-800">Lihat Semua</a>
                            </div>
                            
                            <div class="space-y-2 max-h-64 overflow-y-auto">
                                <div class="p-3 bg-slate-50 rounded-lg">
                                    <p class="text-sm text-slate-600">Belum ada notifikasi baru</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Upgrade Button (hanya untuk outlet) --}}
                    @if(Auth::user()->role_type === 'outlet')
                        <a href="#" class="hidden lg:inline-flex items-center px-4 py-1.5 text-sm font-semibold text-blue-700 bg-blue-100 border border-transparent rounded-full hover:bg-blue-200 transition">
                            ✨ Upgrade ke Pro
                        </a>
                    @endif
                </div>
            </header>

            <main class="flex-1 overflow-y-auto p-6 custom-scrollbar">
                {{-- [PENTING] Konten dari setiap halaman akan dimuat di sini.
                     Agar terlihat bagus, bungkus konten Anda di dalam card dengan efek glass.
                     Contoh: <div class="bg-white/70 backdrop-blur-xl rounded-2xl p-6 shadow-lg">...</div>
                --}}
                @yield('content')
            </main>
        </div>
    </div>
</div>

@stack('scripts')
</body>
</html>