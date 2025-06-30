<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $title ?? 'Surat Sehat v3' }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Tambahan Style Dinamis --}}
    @stack('styles')
</head>
<body class="bg-gray-50 text-gray-800">

<div class="flex h-screen overflow-hidden">

    {{-- Sidebar --}}
    <aside class="w-64 bg-white border-r hidden md:flex flex-col justify-between shadow-md">
        <div class="p-6">
            <div class="flex items-center space-x-3 mb-8">
                <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-blue-500 to-purple-600 flex items-center justify-center animate-pulse shadow-md">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v6h6a1 1 0 110 2h-6v6a1 1 0 11-2 0v-6H3a1 1 0 110-2h6V3a1 1 0 011-1z" clip-rule="evenodd" />
                    </svg>
                </div>
                <h1 class="text-2xl font-extrabold text-gray-800 tracking-tight">
                    Surat<span class="text-blue-600">SehatV3</span>
                </h1>
            </div>
            @include('layouts.navigation')
        </div>

        <div class="p-4 border-t text-xs text-gray-400">
            <div class="space-y-3">
                <a href="{{ route('settings.index') }}" class="w-full flex items-center justify-between px-3 py-2 rounded-md text-sm text-gray-600 bg-gray-100 hover:bg-blue-50 transition">
                    <span>⚙️ Pengaturan</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center justify-between px-3 py-2 rounded-md text-sm text-red-600 bg-red-50 hover:bg-red-100 transition">
                        <span>🔓 Keluar</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1m0-9V4m-3 4H5"/>
                        </svg>
                    </button>
                </form>

                <div class="flex justify-center space-x-2 pt-3 border-t text-gray-400">
                    <a href="#"><i class="fab fa-youtube"></i></a>
                    <a href="#"><i class="fab fa-discord"></i></a>
                    <a href="#"><i class="fab fa-reddit"></i></a>
                    <a href="#"><i class="fab fa-tiktok"></i></a>
                </div>
            </div>
        </div>
    </aside>

    {{-- Main Content --}}
    <div class="flex-1 flex flex-col">
        @php
    $user = Auth::user();
    $activeSessions = $user->active_sessions_count ?? 1; // Default 1 jika tidak tersedia
    $maxSessions = 3;
@endphp

<header class="w-full bg-white shadow-sm px-8 py-4 flex justify-between items-center border-b">
    <div class="text-sm text-gray-600">
        🎉 <span class="font-medium">Langganan Pro</span> aktif hingga <strong>31-12-2025</strong>
    </div>

    <div class="flex items-center space-x-6 relative" x-data="{ open: false }">

        <!-- Informasi Pengguna & Outlet -->
        <div class="text-sm text-gray-700 hidden md:block">
            👋 Halo, <span class="font-semibold">{{ $user->name }}</span>
            @if($user->role === 'outlet' && $user->outlet)
                — <span class="text-blue-600">{{ $user->outlet->name }}</span>
            @endif
        </div>

    @if(Auth::check())
        <a href="{{ route('outlet.sessions.index') }}" class="block px-2 py-1 hover:bg-gray-100 rounded">
            <div class="flex items-center space-x-2">
                <span class="text-sm text-gray-600 hidden sm:inline">Sesi Aktif</span>
                <span class="px-2 py-0.5 text-xs font-semibold rounded-full
                    @if($activeSessions >= $maxSessions)
                        bg-red-100 text-red-700 border border-red-300
                    @elseif($activeSessions === ($maxSessions - 1))
                        bg-yellow-100 text-yellow-800 border border-yellow-300
                    @else
                        bg-green-100 text-green-700 border border-green-300
                    @endif
                ">
                    {{ $activeSessions }} / {{ $maxSessions }}
                </span>
            </div>
        </a>
    @endif

        <!-- Notifikasi -->
      <div class="relative" x-data="notificationDropdown()" x-init="loadNotifications()">
            <button @click="toggle" class="relative text-gray-500 hover:text-blue-600 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
                <template x-if="unreadCount > 0">
                    <span class="absolute -top-1 -right-1 inline-flex items-center justify-center px-1.5 py-0.5 text-xs font-bold leading-none text-white bg-red-500 rounded-full" x-text="unreadCount"></span>
                </template>
            </button>

            <div x-show="open" @click.away="open = false"
                class="absolute right-0 mt-2 w-80 bg-white border border-gray-200 rounded-lg shadow-lg z-50 p-3 text-sm text-gray-700 space-y-2 max-h-96 overflow-y-auto">
                <template x-for="item in notifications" :key="item.id">
                    <a :href="item.action_url || '#'" class="block px-2 py-2 rounded hover:bg-gray-100 transition"
                    :class="{ 'bg-blue-50 font-semibold': !item.is_read }">
                        <div class="text-sm font-medium text-gray-800" x-text="item.title"></div>
                        <div class="text-xs text-gray-500" x-text="item.message"></div>
                    </a>
                </template>
                <div x-show="notifications.length === 0" class="text-center text-gray-400 py-2">
                    Tidak ada notifikasi.
                </div>
            </div>
        </div>

        <!-- Tombol Upgrade -->
        <a href="#"
           class="inline-flex items-center px-4 py-1.5 text-sm font-semibold text-blue-700 bg-blue-50 border border-blue-200 rounded-full hover:bg-blue-100 hover:shadow-md transition-all duration-300 relative overflow-hidden group">
            Upgrade
            <span class="absolute left-0 top-0 w-full h-full bg-white opacity-10 transform -translate-x-full group-hover:translate-x-full transition duration-500 ease-in-out"></span>
        </a>

        <!-- Menu Pengguna -->
        <div class="relative">
            <button @click="open = !open" class="flex items-center space-x-2 focus:outline-none">
                <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}"
                     class="w-9 h-9 rounded-full border border-gray-300" alt="User Avatar">
            </button>

            <div x-show="open" @click.away="open = false"
                 class="absolute right-0 mt-2 w-48 bg-white border border-gray-200 rounded-lg shadow-lg z-50 p-2 text-sm text-gray-700 space-y-2">
                <div class="px-2 py-1 font-semibold">
                    {{ $user->name }}
                    @if($user->role === 'outlet' && $user->outlet)
                        <div class="text-xs text-gray-500">{{ $user->outlet->name }}</div>
                    @endif
                </div>
                <a href="{{ route('profile.show', encrypt($user->id)) }}" class="block px-2 py-1 hover:bg-gray-100 rounded">Profil Saya</a>
                <a href="{{ route('settings.notifications') }}" class="block px-2 py-1 hover:bg-gray-100 rounded">Notifikasi</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-left px-2 py-1 hover:bg-red-100 text-red-600 rounded">
                        Keluar
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>


        {{-- Slot Konten --}}
        <main class="flex-1 overflow-y-auto p-6">
            @yield('content')
        </main>
    </div>
</div>

{{-- Script Tambahan --}}
@stack('scripts')
<script>
function notificationDropdown() {
    return {
        open: false,
        notifications: [],
        unreadCount: 0,
        toggle() {
            this.open = !this.open;
        },
        async loadNotifications() {
            try {
                const res = await fetch('{{ route('header.notifications') }}');
                const data = await res.json();
                this.notifications = data.notifications;
                this.unreadCount = data.unread_count;
            } catch (e) {
                console.error("Gagal memuat notifikasi:", e);
            }
        }
    }
}
</script>

</body>
</html>
