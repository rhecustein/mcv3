<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Akses Ditolak - Surat Sehat v3</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>
</head>
<body class="bg-gray-50 flex items-center justify-center min-h-screen px-4">

    <div class="max-w-lg bg-white rounded-2xl shadow-lg p-8 text-center animate-fade-in-up">
        <lottie-player
            src="https://assets1.lottiefiles.com/packages/lf20_iwmd6pyr.json"
            background="transparent"
            speed="1"
            style="width: 200px; height: 200px;"
            loop
            autoplay
        ></lottie-player>

        <h2 class="text-2xl font-bold text-red-600 mb-2">Akses Ditolak</h2>
        <p class="text-gray-700 mb-4">
            Login Anda berhasil, namun sistem mendeteksi kondisi yang mencegah akses:
        </p>

        <ul class="text-left text-gray-600 list-disc list-inside mb-6 space-y-2">
            <li>Akun Anda sedang aktif di perangkat lain.</li>
            <li>Alamat IP Anda belum terdaftar di sistem.</li>
        </ul>

        <p class="text-gray-800 font-semibold mb-6">
            Silakan hubungi kantor <span class="text-blue-600">BM Pusat</span> untuk aktivasi akses IP Anda.
        </p>

        <a href="{{ route('logout') }}"
           class="inline-block px-6 py-2 bg-gray-200 hover:bg-gray-300 rounded-md text-sm text-gray-700 font-medium transition">
            Kembali ke Halaman Login
        </a>
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
</body>
</html>
