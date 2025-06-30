
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Konfirmasi Logout Paksa - Surat Sehat v3</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center px-4">

    <div class="w-full max-w-md bg-white rounded-2xl shadow-lg p-8 animate-fade-in-up">
        <h2 class="text-2xl font-bold text-center text-gray-800 mb-2">Sesi Aktif Terdeteksi</h2>
        <p class="text-center text-sm text-gray-500 mb-6">
            Kami mendeteksi sesi login lain yang masih aktif untuk akun ini dari IP berbeda. 
            Anda dapat mengakhiri sesi tersebut dan melanjutkan login.
        </p>

        @if(session('status'))
            <div class="mb-4 text-sm text-green-600">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('auth.force-logout') }}" class="space-y-5">
            @csrf

            <input type="hidden" name="user_id" value="{{ session('user_id') }}">
            <input type="hidden" name="ip" value="{{ session('ip') }}">
            <input type="hidden" name="email" value="{{ session('email') }}">

            <button type="submit"
                class="w-full py-2 px-4 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg shadow-md transition">
                Logout Paksa Sesi Aktif & Login Ulang
            </button>

            <a href="{{ route('login') }}"
                class="w-full block text-center py-2 px-4 bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold rounded-lg shadow-md transition">
                Batal
            </a>
        </form>
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
            animation: fade-in-up 0.5s ease-out forwards;
        }
    </style>
</body>
</html>
