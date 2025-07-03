<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <title>{{ $title ?? 'Dasbor - Surat Sehat v3' }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Inter', sans-serif; }
        /* Scrollbar kustom untuk light mode */
        .custom-scrollbar::-webkit-scrollbar { width: 8px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #f1f5f9; } /* slate-100 */
        .custom-scrollbar::-webkit-scrollbar-thumb { background-color: #cbd5e1; border-radius: 20px; } /* slate-300 */
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background-color: #94a3b8; } /* slate-400 */
    </style>
    @stack('styles')
</head>
<body class="h-full bg-slate-50 text-slate-800">

<div class="flex h-screen overflow-hidden">

    <aside class="w-64 bg-white border-r border-slate-200 hidden md:flex flex-col justify-between">
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

        <div class="p-4 border-t border-slate-200">
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open" class="w-full flex items-center justify-between p-2 rounded-lg hover:bg-slate-100 transition-colors duration-200">
                    <div class="flex items-center space-x-3">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=dbeafe&color=1e3a8a"
                             class="w-9 h-9 rounded-full ring-2 ring-white" alt="Avatar">
                        <div class="text-left">
                            <div class="text-sm font-semibold text-slate-800">{{ Auth::user()->name }}</div>
                            <div class="text-xs text-slate-500 capitalize">{{ Auth::user()->role }}</div>
                        </div>
                    </div>
                    <svg class="w-5 h-5 text-slate-500 transition-transform" :class="{ 'rotate-180': open }" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                </button>
                <div x-show="open" @click.away="open = false"
                     x-transition
                     class="absolute bottom-full left-0 mb-2 w-full bg-white border border-slate-200 rounded-lg shadow-xl z-50 p-2 text-sm space-y-1">
                    
                    <a href="{{ route('profile.show', encrypt(Auth::user()->id)) }}" class="flex items-center w-full px-3 py-2 rounded-md text-slate-700 hover:bg-slate-100 hover:text-slate-900 transition">
                        <svg class="w-4 h-4 mr-2 text-slate-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" /></svg>
                        Profil Saya
                    </a>
                    
                    <a href="#" class="flex items-center w-full px-3 py-2 rounded-md text-slate-700 hover:bg-slate-100 hover:text-slate-900 transition">
                         <svg class="w-4 h-4 mr-2 text-slate-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-1.008 1.11-1.226.55-.218 1.19-.243 1.76.026.57.268 1.02.81 1.11 1.408l.092.542m-3.074 0l-1.42 7.102a1.125 1.125 0 001.125 1.226h4.155a1.125 1.125 0 001.125-1.226l-1.42-7.102m-3.074 0h3.074M3 7.5h18M3 12h18m-9 9v.008" /></svg>
                        Pengaturan
                    </a>

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

    <div class="flex-1 flex flex-col bg-slate-100">
        <header class="w-full bg-white px-6 py-3 flex justify-between items-center border-b border-slate-200">
            <div>
                 <h1 class="text-lg font-semibold text-slate-900">{{ $header ?? 'Dasbor' }}</h1>
            </div>

            <div class="flex items-center space-x-5">
                @php
                    $user = Auth::user();
                    $activeSessions = $user->active_sessions_count ?? 1;
                    $maxSessions = 3;
                @endphp
                <a href="#" class="flex items-center space-x-2 px-2 py-1 rounded-lg hover:bg-slate-100 transition">
                    <span class="text-sm text-slate-600 hidden sm:inline">Sesi</span>
                    <span class="px-2 py-0.5 text-xs font-semibold rounded-full
                        @if($activeSessions >= $maxSessions) bg-red-100 text-red-800 border border-red-200
                        @elseif($activeSessions === ($maxSessions - 1)) bg-yellow-100 text-yellow-800 border border-yellow-200
                        @else bg-green-100 text-green-800 border border-green-200
                        @endif">
                        {{ $activeSessions }}/{{ $maxSessions }}
                    </span>
                </a>
                
                <a href="#" class="hidden lg:inline-flex items-center px-4 py-1.5 text-sm font-semibold text-blue-700 bg-blue-100 border border-transparent rounded-full hover:bg-blue-200 transition">
                    ✨ Upgrade ke Pro
                </a>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto p-6 custom-scrollbar">
            {{-- Konten dari setiap halaman akan dimuat di sini --}}
            @yield('content')
        </main>
    </div>
</div>

@stack('scripts')
</body>
</html>