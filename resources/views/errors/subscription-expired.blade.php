<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Langganan Berakhir - MCv3</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 flex items-center justify-center min-h-screen">
    <div class="max-w-md w-full mx-4">
        <div class="bg-white rounded-lg shadow-lg p-8 text-center">
            <!-- Icon -->
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-yellow-100 mb-4">
                <svg class="h-8 w-8 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
            </div>

            <!-- Title -->
            <h1 class="text-2xl font-bold text-gray-900 mb-2">
                Langganan Berakhir
            </h1>

            <!-- Tenant Info -->
            @if(isset($tenant))
            <div class="bg-gray-50 rounded-lg p-4 mb-4">
                <p class="text-sm text-gray-600">Tenant</p>
                <p class="text-lg font-semibold text-gray-900">{{ $tenant->name }}</p>
            </div>
            @endif

            <!-- Message -->
            <p class="text-gray-600 mb-6">
                @if(isset($tenant) && $tenant->subscription_status === 'trial')
                    Masa trial Anda telah berakhir. Silakan upgrade ke paket berbayar untuk melanjutkan.
                @elseif(isset($tenant) && $tenant->subscription_status === 'suspended')
                    Akun Anda telah disuspend. Silakan hubungi administrator atau lakukan pembayaran.
                @else
                    Langganan Anda telah berakhir. Perpanjang langganan untuk mengakses kembali.
                @endif
            </p>

            <!-- Subscription Info -->
            @if(isset($tenant))
            <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-600">Status:</span>
                    <span class="font-semibold text-red-600 uppercase">
                        {{ $tenant->subscription_status }}
                    </span>
                </div>
                @if($tenant->subscription_ends_at)
                <div class="flex items-center justify-between text-sm mt-2">
                    <span class="text-gray-600">Berakhir:</span>
                    <span class="font-medium text-gray-900">
                        {{ $tenant->subscription_ends_at->format('d M Y') }}
                    </span>
                </div>
                @endif
            </div>
            @endif

            <!-- Actions -->
            <div class="space-y-3">
                <a href="mailto:billing@mcv3.local?subject=Perpanjang Langganan - {{ $tenant->slug ?? '' }}"
                   class="block w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition duration-150">
                    Perpanjang Langganan
                </a>

                <a href="mailto:support@mcv3.local?subject=Bantuan Langganan - {{ $tenant->slug ?? '' }}"
                   class="block w-full bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-2 px-4 rounded-lg transition duration-150">
                    Hubungi Support
                </a>
            </div>

            <!-- Help Text -->
            <p class="text-xs text-gray-500 mt-6">
                Hubungi tim billing kami untuk memperpanjang langganan atau informasi lebih lanjut.
            </p>
        </div>
    </div>
</body>
</html>
