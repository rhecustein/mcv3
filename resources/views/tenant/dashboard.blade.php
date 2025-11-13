<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - {{ $tenant->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $tenant->name }}</h1>
                    <p class="text-sm text-gray-500">{{ $tenant->slug }}.mcv3.local</p>
                </div>
                <div class="flex items-center space-x-4">
                    <!-- Subscription Badge -->
                    <span class="px-3 py-1 rounded-full text-xs font-semibold
                        @if($subscriptionInfo['status'] === 'active') bg-green-100 text-green-800
                        @elseif($subscriptionInfo['status'] === 'trial') bg-blue-100 text-blue-800
                        @elseif($subscriptionInfo['status'] === 'suspended') bg-red-100 text-red-800
                        @else bg-gray-100 text-gray-800
                        @endif">
                        {{ strtoupper($subscriptionInfo['plan']) }} - {{ strtoupper($subscriptionInfo['status']) }}
                    </span>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <!-- Trial Warning (if applicable) -->
        @if($subscriptionInfo['is_trial'])
        <div class="mb-6 bg-yellow-50 border-l-4 border-yellow-400 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-yellow-700">
                        <strong>Masa Trial:</strong> Tersisa {{ $subscriptionInfo['trial_days_remaining'] }} hari lagi.
                        <a href="{{ route('tenant.settings.subscription') }}" class="font-medium underline hover:text-yellow-600">
                            Upgrade sekarang
                        </a>
                    </p>
                </div>
            </div>
        </div>
        @endif

        <!-- Usage Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">

            <!-- Users Card -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-sm font-medium text-gray-600">Pengguna</h3>
                    <svg class="h-6 w-6 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
                <div class="flex items-end justify-between">
                    <div>
                        <p class="text-3xl font-bold text-gray-900">{{ $usageStats['users']['current'] }}</p>
                        <p class="text-sm text-gray-500">dari {{ $usageStats['users']['max'] }} user</p>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="relative pt-1">
                        <div class="overflow-hidden h-2 text-xs flex rounded bg-gray-200">
                            <div style="width:{{ $usageStats['users']['percentage'] }}%"
                                 class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center
                                 @if($usageStats['users']['percentage'] >= 90) bg-red-500
                                 @elseif($usageStats['users']['percentage'] >= 70) bg-yellow-500
                                 @else bg-green-500
                                 @endif">
                            </div>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">{{ number_format($usageStats['users']['percentage'], 1) }}% terpakai</p>
                </div>
            </div>

            <!-- Documents Card -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-sm font-medium text-gray-600">Dokumen (Bulan Ini)</h3>
                    <svg class="h-6 w-6 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <div class="flex items-end justify-between">
                    <div>
                        <p class="text-3xl font-bold text-gray-900">{{ number_format($usageStats['documents']['current']) }}</p>
                        <p class="text-sm text-gray-500">dari {{ number_format($usageStats['documents']['max']) }} dokumen</p>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="relative pt-1">
                        <div class="overflow-hidden h-2 text-xs flex rounded bg-gray-200">
                            <div style="width:{{ $usageStats['documents']['percentage'] }}%"
                                 class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center
                                 @if($usageStats['documents']['percentage'] >= 90) bg-red-500
                                 @elseif($usageStats['documents']['percentage'] >= 70) bg-yellow-500
                                 @else bg-green-500
                                 @endif">
                            </div>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">{{ number_format($usageStats['documents']['percentage'], 1) }}% terpakai</p>
                </div>
            </div>

            <!-- Storage Card -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-sm font-medium text-gray-600">Penyimpanan</h3>
                    <svg class="h-6 w-6 text-purple-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                    </svg>
                </div>
                <div class="flex items-end justify-between">
                    <div>
                        <p class="text-3xl font-bold text-gray-900">{{ number_format($usageStats['storage']['current'], 1) }}</p>
                        <p class="text-sm text-gray-500">dari {{ number_format($usageStats['storage']['max']) }} MB</p>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="relative pt-1">
                        <div class="overflow-hidden h-2 text-xs flex rounded bg-gray-200">
                            <div style="width:{{ $usageStats['storage']['percentage'] }}%"
                                 class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center
                                 @if($usageStats['storage']['percentage'] >= 90) bg-red-500
                                 @elseif($usageStats['storage']['percentage'] >= 70) bg-yellow-500
                                 @else bg-green-500
                                 @endif">
                            </div>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">{{ number_format($usageStats['storage']['percentage'], 1) }}% terpakai</p>
                </div>
            </div>

        </div>

        <!-- Quick Actions & Recent Activity -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            <!-- Quick Actions -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Aksi Cepat</h2>
                <div class="space-y-3">
                    <a href="/results/create" class="block p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-900">Buat Surat Kesehatan Baru</p>
                                <p class="text-xs text-gray-500">Buat surat sakit atau surat sehat</p>
                            </div>
                        </div>
                    </a>

                    <a href="{{ route('tenant.settings.usage') }}" class="block p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10 bg-green-100 rounded-lg flex items-center justify-center">
                                <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-900">Lihat Statistik Penggunaan</p>
                                <p class="text-xs text-gray-500">Riwayat penggunaan 6 bulan terakhir</p>
                            </div>
                        </div>
                    </a>

                    <a href="{{ route('tenant.settings.subscription') }}" class="block p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10 bg-purple-100 rounded-lg flex items-center justify-center">
                                <svg class="h-6 w-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-900">Kelola Langganan</p>
                                <p class="text-xs text-gray-500">Upgrade atau lihat paket</p>
                            </div>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Recent Invoices -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-gray-900">Invoice Terbaru</h2>
                    <a href="{{ route('tenant.settings.billing') }}" class="text-sm text-blue-600 hover:text-blue-700">
                        Lihat Semua
                    </a>
                </div>

                @if($recentInvoices->count() > 0)
                <div class="space-y-3">
                    @foreach($recentInvoices as $invoice)
                    <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg">
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ $invoice->invoice_number }}</p>
                            <p class="text-xs text-gray-500">{{ $invoice->invoice_date->format('d M Y') }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-semibold text-gray-900">Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}</p>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                @if($invoice->status === 'paid') bg-green-100 text-green-800
                                @elseif($invoice->status === 'overdue') bg-red-100 text-red-800
                                @else bg-yellow-100 text-yellow-800
                                @endif">
                                {{ ucfirst($invoice->status) }}
                            </span>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-sm text-gray-500 text-center py-8">Belum ada invoice</p>
                @endif
            </div>

        </div>

    </main>
</body>
</html>
