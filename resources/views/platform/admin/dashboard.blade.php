<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Platform Admin - MCv3</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <div class="flex-shrink-0 flex items-center">
                        <h1 class="text-2xl font-bold text-blue-600">MCv3 Platform</h1>
                    </div>
                    <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                        <a href="{{ route('platform.admin.dashboard') }}" class="border-blue-500 text-gray-900 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                            Dashboard
                        </a>
                        <a href="{{ route('platform.admin.tenants.index') }}" class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                            Tenants
                        </a>
                        <a href="{{ route('platform.billing.index') }}" class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                            Billing
                        </a>
                        <a href="{{ route('platform.admin.analytics') }}" class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                            Analytics
                        </a>
                    </div>
                </div>
                <div class="flex items-center">
                    <span class="text-sm text-gray-700 mr-4">{{ auth()->user()->name ?? 'Admin' }}</span>
                    <form method="POST" action="/logout" class="inline">
                        @csrf
                        <button type="submit" class="text-sm text-red-600 hover:text-red-800">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <!-- Stats Overview -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Tenants -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Tenants</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $stats['total_tenants'] }}</p>
                        <p class="text-sm text-gray-500 mt-1">
                            <span class="text-green-600">{{ $stats['active_tenants'] }}</span> active
                        </p>
                    </div>
                    <div class="bg-blue-100 rounded-full p-3">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Monthly Revenue -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Revenue (This Month)</p>
                        <p class="text-3xl font-bold text-gray-900">Rp {{ number_format($stats['total_revenue_this_month'] / 1000000, 1) }}M</p>
                        <p class="text-sm text-green-600 mt-1">
                            +12% from last month
                        </p>
                    </div>
                    <div class="bg-green-100 rounded-full p-3">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Trial Tenants -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Trial Tenants</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $stats['trial_tenants'] }}</p>
                        <p class="text-sm text-yellow-600 mt-1">
                            Expiring soon
                        </p>
                    </div>
                    <div class="bg-yellow-100 rounded-full p-3">
                        <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Pending Invoices -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Pending Invoices</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $stats['pending_invoices'] }}</p>
                        <p class="text-sm text-red-600 mt-1">
                            {{ $stats['overdue_invoices'] }} overdue
                        </p>
                    </div>
                    <div class="bg-red-100 rounded-full p-3">
                        <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Revenue Trend -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Revenue Trend (6 Months)</h3>
                <canvas id="revenueChart" height="200"></canvas>
            </div>

            <!-- Subscription Distribution -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Subscription Distribution</h3>
                <canvas id="subscriptionChart" height="200"></canvas>
            </div>
        </div>

        <!-- Recent Tenants & Expiring Tenants -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Recent Tenants -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Recent Tenants</h3>
                </div>
                <div class="divide-y divide-gray-200">
                    @foreach($recentTenants as $tenant)
                    <div class="px-6 py-4 hover:bg-gray-50">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <h4 class="text-sm font-medium text-gray-900">{{ $tenant->name }}</h4>
                                <p class="text-sm text-gray-500">{{ $tenant->slug }}.mcv3.local</p>
                            </div>
                            <div class="text-right">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($tenant->subscription_status === 'active') bg-green-100 text-green-800
                                    @elseif($tenant->subscription_status === 'trial') bg-blue-100 text-blue-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ ucfirst($tenant->subscription_plan) }}
                                </span>
                                <p class="text-xs text-gray-500 mt-1">{{ $tenant->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="px-6 py-4 bg-gray-50">
                    <a href="{{ route('platform.admin.tenants.index') }}" class="text-sm font-medium text-blue-600 hover:text-blue-800">
                        View all tenants →
                    </a>
                </div>
            </div>

            <!-- Expiring Soon -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Expiring Soon</h3>
                </div>
                <div class="divide-y divide-gray-200">
                    @forelse($expiringTenants as $tenant)
                    <div class="px-6 py-4 hover:bg-gray-50">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <h4 class="text-sm font-medium text-gray-900">{{ $tenant->name }}</h4>
                                <p class="text-sm text-gray-500">{{ $tenant->email }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-medium text-red-600">
                                    {{ $tenant->subscription_ends_at->diffForHumans() }}
                                </p>
                                <p class="text-xs text-gray-500">{{ ucfirst($tenant->subscription_plan) }}</p>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="px-6 py-8 text-center text-gray-500">
                        No tenants expiring soon
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </main>

    <script>
        // Revenue Trend Chart
        const revenueCtx = document.getElementById('revenueChart').getContext('2d');
        new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($revenueTrend->pluck('month')) !!},
                datasets: [{
                    label: 'Revenue (Rp)',
                    data: {!! json_encode($revenueTrend->pluck('revenue')) !!},
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + (value / 1000000).toFixed(1) + 'M';
                            }
                        }
                    }
                }
            }
        });

        // Subscription Distribution Chart
        const subscriptionCtx = document.getElementById('subscriptionChart').getContext('2d');
        new Chart(subscriptionCtx, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($subscriptionDistribution->keys()) !!},
                datasets: [{
                    data: {!! json_encode($subscriptionDistribution->values()) !!},
                    backgroundColor: [
                        'rgb(59, 130, 246)',
                        'rgb(16, 185, 129)',
                        'rgb(245, 158, 11)'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    </script>
</body>
</html>
