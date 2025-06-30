<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Surat Sehat v3</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite(['resources/css/app.css', 'resources/js/app.js']) {{-- Laravel Vite --}}
    <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>
</head>
<body class="bg-gray-50 text-gray-800 flex items-center justify-center min-h-screen px-4">

    <div class="max-w-xl text-center animate-fade-in-up">
        <div class="flex justify-center mb-6">
            <lottie-player
                src="https://assets1.lottiefiles.com/packages/lf20_zrqthn6o.json"
                background="transparent"
                speed="1"
                style="width: 250px; height: 250px;"
                loop
                autoplay
            ></lottie-player>
        </div>

        <h1 class="text-3xl sm:text-4xl font-bold mb-4">Selamat Datang di <span class="text-blue-600">Surat Sehat v3</span></h1>
        <p class="text-gray-600 mb-6">Platform digital untuk pembuatan surat sehat, monitoring pasien, dan analitik kesehatan perusahaan.</p>

        <a href="{{ route('login') }}"
           class="inline-block px-8 py-3 rounded-full text-white font-semibold bg-blue-600 hover:bg-blue-700 transition duration-300 shadow-md">
            Masuk Sekarang
        </a>
    </div>

    <style>
        @keyframes fade-in-up {
            0% {
                opacity: 0;
                transform: translateY(20px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in-up {
            animation: fade-in-up 0.8s ease-out forwards;
        }
    </style>
</body>
</html>
