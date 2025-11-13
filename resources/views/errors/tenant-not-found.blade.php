<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tenant Tidak Ditemukan - MCv3</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 flex items-center justify-center min-h-screen">
    <div class="max-w-md w-full mx-4">
        <div class="bg-white rounded-lg shadow-lg p-8 text-center">
            <!-- Icon -->
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-4">
                <svg class="h-8 w-8 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>

            <!-- Title -->
            <h1 class="text-2xl font-bold text-gray-900 mb-2">
                Tenant Tidak Ditemukan
            </h1>

            <!-- Message -->
            <p class="text-gray-600 mb-6">
                Subdomain <strong class="text-gray-900">{{ $subdomain }}</strong> tidak terdaftar atau tidak aktif di sistem kami.
            </p>

            <!-- Possible Reasons -->
            <div class="bg-gray-50 rounded-lg p-4 mb-6 text-left">
                <p class="text-sm text-gray-700 font-semibold mb-2">Kemungkinan penyebab:</p>
                <ul class="text-sm text-gray-600 space-y-1 list-disc list-inside">
                    <li>Subdomain salah atau typo</li>
                    <li>Akun tenant belum diaktifkan</li>
                    <li>Akun tenant telah dinonaktifkan</li>
                </ul>
            </div>

            <!-- Actions -->
            <div class="space-y-3">
                <a href="{{ config('app.url') }}"
                   class="block w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition duration-150">
                    Kembali ke Halaman Utama
                </a>

                <a href="mailto:support@mcv3.local"
                   class="block w-full bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-2 px-4 rounded-lg transition duration-150">
                    Hubungi Support
                </a>
            </div>

            <!-- Help Text -->
            <p class="text-xs text-gray-500 mt-6">
                Butuh bantuan? Hubungi tim support kami untuk informasi lebih lanjut.
            </p>
        </div>
    </div>
</body>
</html>
