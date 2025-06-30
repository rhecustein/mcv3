<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Verifikasi Surat</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            @apply bg-gradient-to-br from-blue-50 via-green-50 to-white;
        }
    </style>
</head>
<body class="min-h-screen text-gray-800 flex items-center justify-center px-4 py-8 bg-no-repeat bg-cover relative overflow-hidden">
    {{-- Optional static background wave at bottom --}}
    <div class="absolute inset-0 z-0 bg-gradient-to-tr from-blue-100 via-white to-green-100 opacity-60"></div>

    {{-- Content --}}
    <div class="relative z-10 w-full max-w-2xl">
        @yield('content')
    </div>
</body>
</html>
