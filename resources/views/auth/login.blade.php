<!DOCTYPE html>
<html lang="id">
<head>tets
    <meta charset="UTF-8">
    <title>Login Lokasi - Surat Sehat v3</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center px-4">

    <!-- STEP 1: PROMPT PERMISSION -->
   <div id="locationPermission" class="w-full max-w-md bg-white rounded-2xl shadow-lg p-8 text-center animate-fade-in-up">
        <h2 class="text-xl font-semibold text-gray-800 mb-2">🔒 Izinkan Lokasi Anda</h2>
        <p class="text-gray-600 text-sm mb-6">
            Kami membutuhkan izin lokasi Anda untuk keamanan login akun. Silakan izinkan agar bisa lanjut login.
        </p>
        <button onclick="requestLocation()" class="py-2 px-4 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow-md">
            Izinkan Lokasi
        </button>
        <div id="location-error" class="mt-4 text-sm text-red-600 hidden">Gagal mengambil lokasi. Izinkan lokasi untuk melanjutkan.</div>

        <!-- Laravel Alert from Session -->
        @if ($errors->any())
            <div class="mt-4 text-sm text-red-600 bg-red-100 border border-red-300 rounded p-3 text-left">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>

    <!-- STEP 2: LOGIN FORM -->
    <div id="loginFormWrapper" class="hidden w-full max-w-md bg-white rounded-2xl shadow-lg p-8 animate-fade-in-up">
        <h2 class="text-2xl font-bold text-center text-gray-800 mb-2">Masuk ke Akun Anda</h2>
        <p class="text-center text-sm text-gray-500 mb-6">Silakan login untuk mengakses sistem Surat Sehat v3</p>

        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf
            <input type="hidden" name="latitude" id="latitude">
            <input type="hidden" name="longitude" id="longitude">

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input id="email" type="email" name="email" required autofocus
                    class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Kata Sandi</label>
                <input id="password" type="password" name="password" required
                    class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div class="flex items-center justify-between">
                <label class="flex items-center space-x-2">
                    <input type="checkbox" name="remember" class="h-4 w-4 text-blue-600 border-gray-300 rounded">
                    <span class="text-sm text-gray-600">Ingat saya</span>
                </label>

                <a href="{{ route('password.request') }}" class="text-sm text-blue-600 hover:underline">Lupa sandi?</a>
            </div>

            <button type="submit"
                class="w-full py-2 px-4 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow-md transition">
                Masuk
            </button>
        </form>
    </div>

    <style>
        @keyframes fade-in-up {
            0% { opacity: 0; transform: translateY(20px); }
            100% { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in-up {
            animation: fade-in-up 0.5s ease-out forwards;
        }
    </style>

    <script>
        const IS_LOCAL = @json(app()->environment('local'));

        function requestLocation() {
            // Jika environment lokal, skip lokasi
            if (IS_LOCAL) {
                console.log("ENV lokal terdeteksi — melewati geolocation dan menggunakan fallback.");
                document.getElementById("latitude").value = -6.2; // Jakarta
                document.getElementById("longitude").value = 106.816666;
                showLoginForm();
                return;
            }

            if ("geolocation" in navigator) {
                navigator.geolocation.getCurrentPosition(
                    function (position) {
                        document.getElementById("latitude").value = position.coords.latitude;
                        document.getElementById("longitude").value = position.coords.longitude;
                        showLoginForm();
                    },
                    function (error) {
                        console.warn("Geolocation error:", error);
                        document.getElementById("location-error").classList.remove("hidden");
                    }
                );
            } else {
                document.getElementById("location-error").textContent = "Browser tidak mendukung geolocation.";
                document.getElementById("location-error").classList.remove("hidden");
            }
        }

        function showLoginForm() {
            document.getElementById("locationPermission").classList.add("hidden");
            document.getElementById("loginFormWrapper").classList.remove("hidden");
        }
    </script>
</body>
</html>
