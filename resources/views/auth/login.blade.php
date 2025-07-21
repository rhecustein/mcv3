<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login - Surat Sehat v3</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <meta name="csrf-token" content="{{ csrf_token() }}">
    {{-- [IMPROVEMENT] CSS Kustom untuk Latar Belakang dan Animasi --}}
    <style>
        .grid-background {
            background-color: #f8fafc; /* bg-slate-50 */
            background-image:
                linear-gradient(to right, #e2e8f0 1px, transparent 1px),
                linear-gradient(to bottom, #e2e8f0 1px, transparent 1px);
            background-size: 40px 40px;
        }

        /* [IMPROVEMENT] Animasi untuk Ilustrasi SVG */
        .svg-draw-path {
            stroke-dasharray: 1000;
            stroke-dashoffset: 1000;
            animation: draw 2s ease-in-out forwards;
        }
        .svg-draw-path-delay {
            animation-delay: 0.5s;
        }
        .svg-fade-in {
            opacity: 0;
            animation: fade-in 1s ease-in forwards;
            animation-delay: 1.5s;
        }
        @keyframes draw {
            to { stroke-dashoffset: 0; }
        }
        @keyframes fade-in {
            to { opacity: 1; }
        }

        /* [IMPROVEMENT] Animasi untuk Pin Lokasi yang Berdenyut (Pulse) */
        .pulsing-pin .pulse-ring {
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            width: 2rem;
            height: 2rem;
            border: 2px solid #3b82f6; /* border-blue-600 */
            border-radius: 9999px;
            animation: pulse 2s ease-out infinite;
            opacity: 0;
        }
        .pulsing-pin .pulse-ring-delay {
            animation-delay: 1s;
        }
        @keyframes pulse {
            0% {
                transform: translate(-50%, -50%) scale(0.5);
                opacity: 0;
            }
            50% {
                opacity: 1;
            }
            100% {
                transform: translate(-50%, -50%) scale(1.5);
                opacity: 0;
            }
        }
    </style>
</head>
<body class="min-h-screen font-sans antialiased text-slate-800">

    {{-- [IMPROVEMENT] Latar belakang yang konsisten dengan halaman utama --}}
    <div class="absolute top-0 left-0 -z-10 h-full w-full">
        <div class="grid-background h-full w-full"></div>
        <div class="absolute top-0 left-0 h-full w-full bg-[radial-gradient(circle_800px_at_10%_10%,#d5f5f6,transparent)]"></div>
    </div>
    
    <div class="relative min-h-screen flex items-center justify-center p-4">
        {{-- [IMPROVEMENT] Layout dua kolom untuk layar besar --}}
        <div class="w-full max-w-6xl mx-auto lg:grid lg:grid-cols-2 lg:gap-20 items-center">
            
            {{-- Kolom Kiri: Ilustrasi dan Branding (hanya terlihat di layar besar) --}}
            <div class="hidden lg:block">
                <a href="/" class="inline-block mb-4" aria-label="Beranda">
                    <svg class="h-12 w-12 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </a>
                <h1 class="text-4xl font-bold tracking-tight text-slate-900">
                    Akses Aman, Validasi Terpercaya.
                </h1>
                <p class="mt-4 text-lg text-slate-600">
                    Masuk ke dasbor Anda untuk mengelola dan menerbitkan surat kesehatan digital dengan cepat dan aman.
                </p>
                <div class="mt-10">
                    {{-- Ilustrasi SVG Animasi --}}
                    <svg viewBox="0 0 200 150" xmlns="http://www.w3.org/2000/svg">
                        <g stroke-width="2" fill="none">
                            <path class="svg-draw-path" stroke="#9ca3af" d="M40 10 H140 V140 H40z M40 30 H140 M40 50 H140 M40 70 H100"/>
                            <path class="svg-draw-path svg-draw-path-delay" stroke="#3b82f6" d="M50 110 L70 110 L75 100 L85 120 L90 105 L95 110 L115 110"/>
                             <path class="svg-fade-in" stroke="#10b981" stroke-width="4" d="M145 90 L155 105 L175 80"/>
                        </g>
                    </svg>
                </div>
            </div>

            {{-- Kolom Kanan: Form Login --}}
            <div class="w-full max-w-md mx-auto">
                <div class="text-center mb-8 lg:hidden">
                    <a href="/" aria-label="Beranda">
                        <svg class="mx-auto h-12 w-12 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </a>
                    <h1 class="text-3xl font-bold text-slate-800 mt-2">Surat Sehat v3</h1>
                </div>
                
                <div class="bg-white/70 backdrop-blur-xl border border-slate-200/80 shadow-2xl rounded-2xl p-8">
                    
                    <div id="permission-state" class="text-center animate-fade-in-up">
                        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-blue-100 mb-4">
                            <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" /></svg>
                        </div>
                        <h2 class="text-xl font-semibold text-slate-800 mb-2">Verifikasi Lokasi Anda</h2>
                        <p class="text-slate-500 text-sm mb-6">Untuk keamanan, kami perlu memastikan Anda login dari lokasi yang terdaftar. Mohon izinkan akses lokasi.</p>
                        <button onclick="requestLocation()" class="w-full py-3 px-4 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow-lg shadow-blue-500/30 transition-all duration-300 transform hover:scale-105">
                            Izinkan & Lanjutkan
                        </button>
                        <div id="location-error" class="mt-4 text-xs text-red-600 hidden"></div>
                         @if ($errors->any())
                             <div class="mt-4 text-sm text-red-700 bg-red-100 border border-red-300 rounded p-3 text-left">
                                 <ul class="list-disc list-inside">
                                     @foreach ($errors->all() as $error)
                                         <li>{{ $error }}</li>
                                     @endforeach
                                 </ul>
                             </div>
                         @endif
                    </div>

                    <div id="loading-state" class="text-center hidden animate-fade-in-up">
                        {{-- Animasi Pin Lokasi Berdenyut --}}
                        <div class="relative h-16 w-16 mx-auto mb-4 pulsing-pin">
                            <div class="pulse-ring"></div>
                            <div class="pulse-ring pulse-ring-delay"></div>
                            <svg class="h-16 w-16 text-blue-600 absolute top-0 left-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" /></svg>
                        </div>
                        <h2 class="text-xl font-semibold text-slate-800 mb-2">Memverifikasi Lokasi...</h2>
                        <p class="text-slate-500 text-sm">Mohon tunggu sebentar.</p>
                    </div>

                    <div id="login-form-state" class="hidden animate-fade-in-up">
                        <h2 class="text-2xl font-bold text-center text-slate-800 mb-2">Selamat Datang Kembali</h2>
                        <p class="text-center text-sm text-slate-500 mb-8">Login untuk melanjutkan ke dasbor Anda.</p>
                        <form method="POST" action="{{ route('login') }}" class="space-y-6">
                            @csrf
                            <input type="hidden" name="latitude" id="latitude">
                            <input type="hidden" name="longitude" id="longitude">
                            
                            {{-- [IMPROVEMENT] Input dengan Ikon --}}
                            <div>
                                <label for="email" class="block text-sm font-medium text-slate-700 mb-1">Email</label>
                                <div class="relative">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                        <svg class="h-5 w-5 text-slate-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path d="M3 4a2 2 0 00-2 2v1.161l8.441 4.221a1.25 1.25 0 001.118 0L19 7.162V6a2 2 0 00-2-2H3z" /><path d="M19 8.839l-7.77 3.885a2.75 2.75 0 01-2.46 0L1 8.839V14a2 2 0 002 2h14a2 2 0 002-2V8.839z" /></svg>
                                    </div>
                                    <input id="email" type="email" name="email" required autofocus placeholder="nama@email.com" class="block w-full rounded-md border-slate-300 py-2.5 pl-10 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                </div>
                            </div>

                            <div>
                                <label for="password" class="block text-sm font-medium text-slate-700 mb-1">Kata Sandi</label>
                                <div class="relative">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                        <svg class="h-5 w-5 text-slate-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 1a4.5 4.5 0 00-4.5 4.5V9H5a2 2 0 00-2 2v6a2 2 0 002 2h10a2 2 0 002-2v-6a2 2 0 00-2-2h-.5V5.5A4.5 4.5 0 0010 1zm3 8V5.5a3 3 0 10-6 0V9h6z" clip-rule="evenodd" /></svg>
                                    </div>
                                    <input id="password" type="password" name="password" required placeholder="••••••••" class="block w-full rounded-md border-slate-300 py-2.5 pl-10 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                </div>
                            </div>

                            <div class="flex items-center justify-between">
                                <label class="flex items-center space-x-2 cursor-pointer">
                                    <input type="checkbox" name="remember" class="h-4 w-4 text-blue-600 border-slate-300 rounded focus:ring-blue-600">
                                    <span class="text-sm text-slate-600">Ingat saya</span>
                                </label>
                                <a href="{{ route('password.request') }}" class="text-sm font-medium text-blue-600 hover:text-blue-500">Lupa sandi?</a>
                            </div>
                            
                            <button type="submit" class="w-full py-3 px-4 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg shadow-lg shadow-blue-500/30 transition-all duration-300 transform hover:scale-105">
                                Masuk
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Script JavaScript tetap sama, karena hanya mengontrol logika state --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const IS_LOCAL = @json(app()->environment('local'));
            const permissionState = document.getElementById('permission-state');
            const loadingState = document.getElementById('loading-state');
            const loginFormState = document.getElementById('login-form-state');
            const locationError = document.getElementById('location-error');
            const latInput = document.getElementById('latitude');
            const lonInput = document.getElementById('longitude');

            window.requestLocation = function() {
                permissionState.classList.add('hidden');
                loadingState.classList.remove('hidden');
                locationError.classList.add('hidden');

                if (IS_LOCAL) {
                    console.log("ENV lokal terdeteksi — melewati geolocation.");
                    setTimeout(() => {
                        latInput.value = -6.2;
                        lonInput.value = 106.816666;
                        showLoginForm();
                    }, 1500); // Diberi sedikit delay agar animasi loading terlihat
                    return;
                }
                
                if ("geolocation" in navigator) {
                    navigator.geolocation.getCurrentPosition(
                        (position) => {
                            latInput.value = position.coords.latitude;
                            lonInput.value = position.coords.longitude;
                            setTimeout(showLoginForm, 500); // Sedikit delay untuk transisi
                        },
                        (error) => {
                            showPermissionError("Gagal mengambil lokasi. Izinkan lokasi untuk melanjutkan.");
                        },
                        { timeout: 10000, enableHighAccuracy: true }
                    );
                } else {
                    showPermissionError("Browser tidak mendukung geolocation.");
                }
            };

            function showLoginForm() {
                loadingState.classList.add('hidden');
                loginFormState.classList.remove('hidden');
            }

            function showPermissionError(message) {
                loadingState.classList.add('hidden');
                permissionState.classList.remove('hidden');
                locationError.textContent = message;
                locationError.classList.remove('hidden');
            }
        });
    </script>
</body>
</html>