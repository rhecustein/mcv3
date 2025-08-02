<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login - Surat Sehat v3</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        .grid-background {
            background-color: #f8fafc;
            background-image:
                linear-gradient(to right, #e2e8f0 1px, transparent 1px),
                linear-gradient(to bottom, #e2e8f0 1px, transparent 1px);
            background-size: 40px 40px;
        }

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

        .pulsing-pin .pulse-ring {
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            width: 2rem;
            height: 2rem;
            border: 2px solid #3b82f6;
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

        .alert-slide-down {
            animation: slideDown 0.4s ease-out forwards;
        }
        .alert-slide-up {
            animation: slideUp 0.3s ease-in forwards;
        }
        @keyframes slideDown {
            from { transform: translateY(-100%); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        @keyframes slideUp {
            from { transform: translateY(0); opacity: 1; }
            to { transform: translateY(-100%); opacity: 0; }
        }

        .shake {
            animation: shake 0.5s ease-in-out;
        }
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }

        .btn-loading {
            position: relative;
            color: transparent !important;
        }
        .btn-loading::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 20px;
            height: 20px;
            border: 2px solid transparent;
            border-top: 2px solid currentColor;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            color: white;
        }
        @keyframes spin {
            0% { transform: translate(-50%, -50%) rotate(0deg); }
            100% { transform: translate(-50%, -50%) rotate(360deg); }
        }

        .animate-fade-in-up {
            animation: fadeInUp 0.6s ease-out forwards;
        }
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body class="min-h-screen font-sans antialiased text-slate-800">

    <!-- Alert Container -->
    <div id="alert-container" class="fixed top-4 left-1/2 transform -translate-x-1/2 z-50 w-full max-w-md px-4">
        <!-- Alerts will be inserted here dynamically -->
    </div>

    <!-- Background -->
    <div class="absolute top-0 left-0 -z-10 h-full w-full">
        <div class="grid-background h-full w-full"></div>
        <div class="absolute top-0 left-0 h-full w-full bg-[radial-gradient(circle_800px_at_10%_10%,#d5f5f6,transparent)]"></div>
    </div>
    
    <div class="relative min-h-screen flex items-center justify-center p-4">
        <div class="w-full max-w-6xl mx-auto lg:grid lg:grid-cols-2 lg:gap-20 items-center">
            
            <!-- Left Column: Illustration and Branding -->
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
                    <svg viewBox="0 0 200 150" xmlns="http://www.w3.org/2000/svg">
                        <g stroke-width="2" fill="none">
                            <path class="svg-draw-path" stroke="#9ca3af" d="M40 10 H140 V140 H40z M40 30 H140 M40 50 H140 M40 70 H100"/>
                            <path class="svg-draw-path svg-draw-path-delay" stroke="#3b82f6" d="M50 110 L70 110 L75 100 L85 120 L90 105 L95 110 L115 110"/>
                             <path class="svg-fade-in" stroke="#10b981" stroke-width="4" d="M145 90 L155 105 L175 80"/>
                        </g>
                    </svg>
                </div>
            </div>

            <!-- Right Column: Login Form -->
            <div class="w-full max-w-md mx-auto">
                <!-- Mobile Header -->
                <div class="text-center mb-8 lg:hidden">
                    <a href="/" aria-label="Beranda">
                        <svg class="mx-auto h-12 w-12 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </a>
                    <h1 class="text-3xl font-bold text-slate-800 mt-2">Surat Sehat v3</h1>
                </div>
                
                <!-- Login Card -->
                <div id="login-card" class="bg-white/70 backdrop-blur-xl border border-slate-200/80 shadow-2xl rounded-2xl p-8">
                    
                    <!-- Permission State -->
                    <div id="permission-state" class="text-center animate-fade-in-up">
                        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-blue-100 mb-4">
                            <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                            </svg>
                        </div>
                        <h2 class="text-xl font-semibold text-slate-800 mb-2">Verifikasi Lokasi Anda</h2>
                        <p class="text-slate-500 text-sm mb-6">Untuk keamanan, kami perlu memastikan Anda login dari lokasi yang terdaftar. Mohon izinkan akses lokasi.</p>
                        <button onclick="requestLocation()" class="w-full py-3 px-4 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow-lg shadow-blue-500/30 transition-all duration-300 transform hover:scale-105">
                            Izinkan & Lanjutkan
                        </button>
                        <div id="location-error" class="mt-4 text-xs text-red-600 hidden"></div>
                    </div>

                    <!-- Loading State -->
                    <div id="loading-state" class="text-center hidden animate-fade-in-up">
                        <div class="relative h-16 w-16 mx-auto mb-4 pulsing-pin">
                            <div class="pulse-ring"></div>
                            <div class="pulse-ring pulse-ring-delay"></div>
                            <svg class="h-16 w-16 text-blue-600 absolute top-0 left-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                            </svg>
                        </div>
                        <h2 class="text-xl font-semibold text-slate-800 mb-2">Memverifikasi Lokasi...</h2>
                        <p class="text-slate-500 text-sm">Mohon tunggu sebentar.</p>
                    </div>

                    <!-- Login Form State -->
                    <div id="login-form-state" class="hidden animate-fade-in-up">
                        <h2 class="text-2xl font-bold text-center text-slate-800 mb-2">Selamat Datang Kembali</h2>
                        <p class="text-center text-sm text-slate-500 mb-8">Login untuk melanjutkan ke dasbor Anda.</p>
                        
                        <form method="POST" action="{{ route('login') }}" class="space-y-6" id="login-form">
                            @csrf
                            <input type="hidden" name="latitude" id="latitude">
                            <input type="hidden" name="longitude" id="longitude">
                            
                            <!-- Email Input -->
                            <div>
                                <label for="email" class="block text-sm font-medium text-slate-700 mb-1">Email</label>
                                <div class="relative">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                        <svg class="h-5 w-5 text-slate-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M3 4a2 2 0 00-2 2v1.161l8.441 4.221a1.25 1.25 0 001.118 0L19 7.162V6a2 2 0 00-2-2H3z" />
                                            <path d="M19 8.839l-7.77 3.885a2.75 2.75 0 01-2.46 0L1 8.839V14a2 2 0 002 2h14a2 2 0 002-2V8.839z" />
                                        </svg>
                                    </div>
                                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus placeholder="nama@email.com" class="block w-full rounded-md border-slate-300 py-2.5 pl-10 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                </div>
                                @error('email')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Password Input -->
                            <div>
                                <label for="password" class="block text-sm font-medium text-slate-700 mb-1">Kata Sandi</label>
                                <div class="relative">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                        <svg class="h-5 w-5 text-slate-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 1a4.5 4.5 0 00-4.5 4.5V9H5a2 2 0 00-2 2v6a2 2 0 002 2h10a2 2 0 002-2v-6a2 2 0 00-2-2h-.5V5.5A4.5 4.5 0 0010 1zm3 8V5.5a3 3 0 10-6 0V9h6z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <input id="password" type="password" name="password" required placeholder="••••••••" class="block w-full rounded-md border-slate-300 py-2.5 pl-10 pr-10 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                    <button type="button" onclick="togglePassword()" class="absolute inset-y-0 right-0 flex items-center pr-3">
                                        <svg id="eye-icon" class="h-5 w-5 text-slate-400 hover:text-slate-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                    </button>
                                </div>
                                @error('password')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Remember Me & Forgot Password -->
                            <div class="flex items-center justify-between">
                                <label class="flex items-center space-x-2 cursor-pointer">
                                    <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }} class="h-4 w-4 text-blue-600 border-slate-300 rounded focus:ring-blue-600">
                                    <span class="text-sm text-slate-600">Ingat saya</span>
                                </label>
                                @if (Route::has('password.request'))
                                    <a href="{{ route('password.request') }}" class="text-sm font-medium text-blue-600 hover:text-blue-500">Lupa sandi?</a>
                                @endif
                            </div>
                            
                            <!-- Submit Button -->
                            <button type="submit" id="login-btn" class="w-full py-3 px-4 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg shadow-lg shadow-blue-500/30 transition-all duration-300 transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none">
                                Masuk
                            </button>
                        </form>

                        <!-- Development Mode Indicator -->
                        @if (app()->environment('local'))
                            <div class="mt-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                                <div class="flex items-center">
                                    <svg class="h-4 w-4 text-yellow-400 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                                    </svg>
                                    <p class="text-xs text-yellow-800">Mode Pengembangan - Validasi lokasi dilewati</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const IS_LOCAL = @json(app()->environment('local'));
            const permissionState = document.getElementById('permission-state');
            const loadingState = document.getElementById('loading-state');
            const loginFormState = document.getElementById('login-form-state');
            const loginCard = document.getElementById('login-card');
            const locationError = document.getElementById('location-error');
            const latInput = document.getElementById('latitude');
            const lonInput = document.getElementById('longitude');
            const loginForm = document.getElementById('login-form');
            const loginBtn = document.getElementById('login-btn');

            // Check for server-side errors and show alerts
            @if ($errors->any())
                @foreach ($errors->all() as $error)
                    showAlert('{{ addslashes($error) }}', 'error');
                @endforeach
                // If there are errors, show the login form immediately
                showLoginForm();
            @endif

            // Check for success/status messages
            @if (session('status'))
                showAlert('{{ addslashes(session('status')) }}', 'success');
            @endif

            @if (session('success'))
                showAlert('{{ addslashes(session('success')) }}', 'success');
            @endif

            @if (session('error'))
                showAlert('{{ addslashes(session('error')) }}', 'error');
            @endif

            // Alert System
            function showAlert(message, type = 'error', duration = 5000) {
                const alertContainer = document.getElementById('alert-container');
                const alertId = 'alert-' + Date.now();
                
                const alertTypes = {
                    error: {
                        bgColor: 'bg-red-50',
                        borderColor: 'border-red-200',
                        textColor: 'text-red-800',
                        iconColor: 'text-red-400',
                        icon: `<path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />`
                    },
                    success: {
                        bgColor: 'bg-green-50',
                        borderColor: 'border-green-200', 
                        textColor: 'text-green-800',
                        iconColor: 'text-green-400',
                        icon: `<path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />`
                    },
                    warning: {
                        bgColor: 'bg-yellow-50',
                        borderColor: 'border-yellow-200',
                        textColor: 'text-yellow-800', 
                        iconColor: 'text-yellow-400',
                        icon: `<path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />`
                    }
                };

                const config = alertTypes[type] || alertTypes.error;
                
                const alertHTML = `
                    <div id="${alertId}" class="alert-slide-down ${config.bgColor} ${config.borderColor} border rounded-lg p-4 shadow-lg backdrop-blur-sm mb-2">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 ${config.iconColor}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    ${config.icon}
                                </svg>
                            </div>
                            <div class="ml-3 flex-1">
                                <p class="text-sm font-medium ${config.textColor}">${message}</p>
                            </div>
                            <div class="ml-auto pl-3">
                                <button onclick="hideAlert('${alertId}')" class="inline-flex ${config.textColor} hover:opacity-75">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                `;
                
                alertContainer.insertAdjacentHTML('beforeend', alertHTML);
                
                // Auto-hide after duration
                if (duration > 0) {
                    setTimeout(() => hideAlert(alertId), duration);
                }

                // Add shake effect to login card for errors
                if (type === 'error') {
                    loginCard.classList.add('shake');
                    setTimeout(() => loginCard.classList.remove('shake'), 500);
                }
            }

            // Hide Alert Function
            window.hideAlert = function(alertId) {
                const alert = document.getElementById(alertId);
                if (alert) {
                    alert.classList.remove('alert-slide-down');
                    alert.classList.add('alert-slide-up');
                    setTimeout(() => alert.remove(), 300);
                }
            };

            // Toggle Password Visibility
            window.togglePassword = function() {
                const passwordInput = document.getElementById('password');
                const eyeIcon = document.getElementById('eye-icon');
                
                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    eyeIcon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 11-4.243-4.243m4.242 4.242L9.88 9.88" />`;
                } else {
                    passwordInput.type = 'password';
                    eyeIcon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />`;
                }
            };

            // Enhanced Form Submission with Loading State
            loginForm.addEventListener('submit', function(e) {
                const email = document.getElementById('email').value;
                const password = document.getElementById('password').value;
                
                if (!email || !password) {
                    e.preventDefault();
                    showAlert('Silakan isi email dan password.', 'warning');
                    return;
                }

                if (!IS_LOCAL && (!latInput.value || !lonInput.value)) {
                    e.preventDefault();
                    showAlert('Data lokasi tidak tersedia. Silakan izinkan akses lokasi terlebih dahulu.', 'error');
                    return;
                }

                // Show loading state
                loginBtn.classList.add('btn-loading');
                loginBtn.disabled = true;
                
                // Timeout handler
                setTimeout(() => {
                    if (loginBtn.classList.contains('btn-loading')) {
                        loginBtn.classList.remove('btn-loading');
                        loginBtn.disabled = false;
                        showAlert('Login memakan waktu lebih lama dari biasanya. Silakan coba lagi.', 'warning');
                    }
                }, 15000);
            });

            // Location Request Function
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
                        showAlert('Mode pengembangan: Lokasi simulasi digunakan.', 'warning', 3000);
                    }, 1500);
                    return;
                }
                
                if ("geolocation" in navigator) {
                    navigator.geolocation.getCurrentPosition(
                        (position) => {
                            latInput.value = position.coords.latitude;
                            lonInput.value = position.coords.longitude;
                            setTimeout(() => {
                                showLoginForm();
                                showAlert('Lokasi berhasil diverifikasi.', 'success', 2000);
                            }, 500);
                        },
                        (error) => {
                            let errorMessage = "Gagal mengambil lokasi. ";
                            switch(error.code) {
                                case error.PERMISSION_DENIED:
                                    errorMessage += "Izin lokasi ditolak. Mohon izinkan akses lokasi untuk melanjutkan.";
                                    break;
                                case error.POSITION_UNAVAILABLE:
                                    errorMessage += "Informasi lokasi tidak tersedia.";
                                    break;
                                case error.TIMEOUT:
                                    errorMessage += "Permintaan lokasi timeout. Silakan coba lagi.";
                                    break;
                                default:
                                    errorMessage += "Terjadi kesalahan yang tidak diketahui.";
                                    break;
                            }
                            showPermissionError(errorMessage);
                        },
                        { timeout: 10000, enableHighAccuracy: true }
                    );
                } else {
                    showPermissionError("Browser tidak mendukung geolocation. Mohon gunakan browser yang lebih modern.");
                }
            };

            function showLoginForm() {
                loadingState.classList.add('hidden');
                loginFormState.classList.remove('hidden');
                
                // Set default location for local environment
                if (IS_LOCAL) {
                    latInput.value = -6.2;
                    lonInput.value = 106.816666;
                }
            }

            function showPermissionError(message) {
                loadingState.classList.add('hidden');
                permissionState.classList.remove('hidden');
                locationError.textContent = message;
                locationError.classList.remove('hidden');
                showAlert(message, 'error');
            }

            // Auto-show login form if there are validation errors or if it's local environment
            @if ($errors->any() || app()->environment('local'))
                showLoginForm();
            @endif
        });
    </script>
</body>
</html>