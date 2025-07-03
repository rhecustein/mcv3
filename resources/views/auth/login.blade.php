<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login - Surat Sehat v3</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="bg-slate-50 min-h-screen flex items-center justify-center px-4 font-sans">

    <div class="w-full max-w-md">

        <div class="text-center mb-8">
            <a href="/" aria-label="Beranda">
                <svg class="mx-auto h-16 w-16 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </a>
            <h1 class="text-3xl font-bold text-slate-800 mt-2">Surat Sehat v3</h1>
        </div>
        
        <div class="bg-white border border-slate-200 shadow-xl rounded-2xl p-8">
            
            <div id="permission-state" class="text-center animate-fade-in-up">
                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-blue-100 mb-4">
                    <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" /></svg>
                </div>
                <h2 class="text-xl font-semibold text-slate-800 mb-2">Verifikasi Lokasi Anda</h2>
                <p class="text-slate-500 text-sm mb-6">Untuk keamanan, kami perlu memastikan Anda login dari lokasi yang terdaftar. Mohon izinkan akses lokasi.</p>
                <button onclick="requestLocation()" class="w-full py-3 px-4 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow-md transition-all duration-200">
                    Izinkan & Lanjutkan
                </button>
                <div id="location-error" class="mt-4 text-xs text-red-600 hidden">Gagal mengambil lokasi. Pastikan Anda mengizinkan akses lokasi pada browser.</div>
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
                <svg class="animate-spin h-8 w-8 text-blue-600 mx-auto mb-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
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
                    
                    <div>
                        <label for="email" class="sr-only">Email</label>
                        <input id="email" type="email" name="email" required autofocus placeholder="Email Anda" class="block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    </div>

                    <div>
                         <label for="password" class="sr-only">Kata Sandi</label>
                        <input id="password" type="password" name="password" required placeholder="Kata Sandi" class="block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    </div>

                    <div class="flex items-center justify-between">
                        <label class="flex items-center space-x-2 cursor-pointer">
                            <input type="checkbox" name="remember" class="h-4 w-4 text-blue-600 border-slate-300 rounded focus:ring-blue-600">
                            <span class="text-sm text-slate-600">Ingat saya</span>
                        </label>
                        <a href="{{ route('password.request') }}" class="text-sm font-semibold text-blue-600 hover:underline">Lupa sandi?</a>
                    </div>
                    
                    <button type="submit" class="w-full py-3 px-4 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg shadow-md transition-all duration-200">
                        Masuk
                    </button>
                </form>
            </div>
        </div>

    </div>

    <script>
        // Script JavaScript tetap sama, tidak perlu diubah.
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
                    }, 500);
                    return;
                }
                
                if ("geolocation" in navigator) {
                    navigator.geolocation.getCurrentPosition(
                        (position) => {
                            latInput.value = position.coords.latitude;
                            lonInput.value = position.coords.longitude;
                            showLoginForm();
                        },
                        (error) => {
                            showPermissionError("Gagal mengambil lokasi. Izinkan lokasi untuk melanjutkan.");
                        },
                        { timeout: 10000 }
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