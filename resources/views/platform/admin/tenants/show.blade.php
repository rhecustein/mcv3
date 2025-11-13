<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $tenant->name }} - Tenant Details</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-50">
    @include('platform.admin.partials.nav')

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <a href="{{ route('platform.admin.tenants.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium mb-2 inline-block">
                        ← Back to Tenants
                    </a>
                    <h1 class="text-3xl font-bold text-gray-900">{{ $tenant->name }}</h1>
                    <p class="text-gray-600 mt-1">{{ $tenant->subdomain }}.{{ config('app.domain') }}</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('platform.admin.tenants.edit', $tenant) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-semibold transition duration-150">
                        Edit Tenant
                    </a>
                    @if($tenant->status === 'active')
                    <form action="{{ route('platform.admin.tenants.suspend', $tenant) }}" method="POST" onsubmit="return confirm('Are you sure you want to suspend this tenant?');">
                        @csrf
                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg font-semibold transition duration-150">
                            Suspend
                        </button>
                    </form>
                    @else
                    <form action="{{ route('platform.admin.tenants.activate', $tenant) }}" method="POST">
                        @csrf
                        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg font-semibold transition duration-150">
                            Activate
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </div>

        <!-- Status Banner -->
        @if($tenant->status === 'suspended')
        <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded">
            <div class="flex items-center">
                <svg class="w-6 h-6 text-red-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                </svg>
                <p class="text-red-800 font-semibold">This tenant is currently suspended</p>
            </div>
        </div>
        @elseif($tenant->trial_ends_at && $tenant->trial_ends_at->isFuture())
        <div class="mb-6 bg-blue-50 border-l-4 border-blue-500 p-4 rounded">
            <div class="flex items-center">
                <svg class="w-6 h-6 text-blue-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                </svg>
                <p class="text-blue-800 font-semibold">Trial period ends {{ $tenant->trial_ends_at->diffForHumans() }}</p>
            </div>
        </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Overview Stats -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Users</p>
                                <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['users'] ?? 0 }}</p>
                            </div>
                            <div class="bg-blue-100 p-3 rounded-full">
                                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Patients</p>
                                <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['patients'] ?? 0 }}</p>
                            </div>
                            <div class="bg-green-100 p-3 rounded-full">
                                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600">MCU Bookings</p>
                                <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['bookings'] ?? 0 }}</p>
                            </div>
                            <div class="bg-purple-100 p-3 rounded-full">
                                <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Usage Chart -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Usage Trends (Last 30 Days)</h3>
                    <canvas id="usageChart" height="80"></canvas>
                </div>

                <!-- Recent Users -->
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-bold text-gray-900">Recent Users</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Joined</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Login</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($recentUsers ?? [] as $user)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="font-medium text-gray-900">{{ $user->name }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        {{ $user->email }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                            {{ $user->role }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        {{ $user->created_at->format('d M Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Never' }}
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                        No users found
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Recent Bookings -->
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-bold text-gray-900">Recent MCU Bookings</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Booking #</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Patient</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Package</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($recentBookings ?? [] as $booking)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $booking->booking_number }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        {{ $booking->patient_name }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        {{ $booking->package->name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        {{ $booking->booking_date->format('d M Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                        Rp {{ number_format($booking->total_amount, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full
                                            @if($booking->status === 'confirmed') bg-green-100 text-green-800
                                            @elseif($booking->status === 'pending') bg-yellow-100 text-yellow-800
                                            @elseif($booking->status === 'cancelled') bg-red-100 text-red-800
                                            @elseif($booking->status === 'completed') bg-blue-100 text-blue-800
                                            @endif">
                                            {{ ucfirst($booking->status) }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                        No bookings found
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Activity Log -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Recent Activity</h3>
                    <div class="space-y-4">
                        @forelse($activityLog ?? [] as $activity)
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4 flex-1">
                                <p class="text-sm text-gray-900">{{ $activity->description }}</p>
                                <p class="text-xs text-gray-500 mt-1">{{ $activity->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                        @empty
                        <p class="text-gray-500 text-center py-4">No recent activity</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Basic Info -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Basic Information</h3>
                    <div class="space-y-3">
                        <div>
                            <p class="text-sm text-gray-600">Status</p>
                            <span class="inline-block mt-1 px-3 py-1 text-sm font-semibold rounded-full
                                @if($tenant->status === 'active') bg-green-100 text-green-800
                                @elseif($tenant->status === 'suspended') bg-red-100 text-red-800
                                @elseif($tenant->status === 'trial') bg-blue-100 text-blue-800
                                @endif">
                                {{ ucfirst($tenant->status) }}
                            </span>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Subdomain</p>
                            <p class="font-semibold text-gray-900">{{ $tenant->subdomain }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Domain</p>
                            <a href="http://{{ $tenant->subdomain }}.{{ config('app.domain') }}" target="_blank" class="font-semibold text-blue-600 hover:text-blue-800">
                                {{ $tenant->subdomain }}.{{ config('app.domain') }}
                                <svg class="w-4 h-4 inline ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                </svg>
                            </a>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Created</p>
                            <p class="font-semibold text-gray-900">{{ $tenant->created_at->format('d M Y') }}</p>
                            <p class="text-xs text-gray-500">{{ $tenant->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                </div>

                <!-- Subscription Info -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Subscription</h3>
                    <div class="space-y-3">
                        <div>
                            <p class="text-sm text-gray-600">Current Plan</p>
                            <p class="font-bold text-xl text-blue-600 mt-1 capitalize">{{ $tenant->subscription_plan }}</p>
                        </div>
                        @if($tenant->trial_ends_at)
                        <div>
                            <p class="text-sm text-gray-600">Trial Ends</p>
                            <p class="font-semibold text-gray-900">{{ $tenant->trial_ends_at->format('d M Y') }}</p>
                            <p class="text-xs text-gray-500">{{ $tenant->trial_ends_at->diffForHumans() }}</p>
                        </div>
                        @endif
                        @if($tenant->subscription_ends_at)
                        <div>
                            <p class="text-sm text-gray-600">Subscription Ends</p>
                            <p class="font-semibold text-gray-900">{{ $tenant->subscription_ends_at->format('d M Y') }}</p>
                            <p class="text-xs text-gray-500">{{ $tenant->subscription_ends_at->diffForHumans() }}</p>
                        </div>
                        @endif
                        <div>
                            <p class="text-sm text-gray-600">Monthly Revenue</p>
                            <p class="font-bold text-2xl text-green-600 mt-1">
                                Rp {{ number_format($stats['monthly_revenue'] ?? 0, 0, ',', '.') }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Usage Quotas -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Usage Quotas</h3>
                    <div class="space-y-4">
                        <!-- Users Quota -->
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span class="text-gray-600">Users</span>
                                <span class="font-semibold text-gray-900">{{ $stats['users'] ?? 0 }} / {{ $tenant->max_users ?? '∞' }}</span>
                            </div>
                            @if($tenant->max_users)
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-blue-600 h-2 rounded-full" style="width: {{ min(100, (($stats['users'] ?? 0) / $tenant->max_users) * 100) }}%"></div>
                            </div>
                            @endif
                        </div>

                        <!-- Patients Quota -->
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span class="text-gray-600">Patients</span>
                                <span class="font-semibold text-gray-900">{{ $stats['patients'] ?? 0 }} / {{ $tenant->max_patients ?? '∞' }}</span>
                            </div>
                            @if($tenant->max_patients)
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-green-600 h-2 rounded-full" style="width: {{ min(100, (($stats['patients'] ?? 0) / $tenant->max_patients) * 100) }}%"></div>
                            </div>
                            @endif
                        </div>

                        <!-- Storage Quota -->
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span class="text-gray-600">Storage</span>
                                <span class="font-semibold text-gray-900">{{ number_format(($stats['storage_used'] ?? 0) / 1024, 2) }} GB / {{ $tenant->max_storage_gb ?? '∞' }} GB</span>
                            </div>
                            @if($tenant->max_storage_gb)
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-purple-600 h-2 rounded-full" style="width: {{ min(100, (($stats['storage_used'] ?? 0) / ($tenant->max_storage_gb * 1024)) * 100) }}%"></div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Contact Info -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Contact Information</h3>
                    <div class="space-y-3 text-sm">
                        @if($tenant->contact_email)
                        <div>
                            <p class="text-gray-600">Email</p>
                            <a href="mailto:{{ $tenant->contact_email }}" class="text-blue-600 hover:text-blue-800 font-semibold">
                                {{ $tenant->contact_email }}
                            </a>
                        </div>
                        @endif
                        @if($tenant->contact_phone)
                        <div>
                            <p class="text-gray-600">Phone</p>
                            <a href="tel:{{ $tenant->contact_phone }}" class="text-blue-600 hover:text-blue-800 font-semibold">
                                {{ $tenant->contact_phone }}
                            </a>
                        </div>
                        @endif
                        @if($tenant->contact_address)
                        <div>
                            <p class="text-gray-600">Address</p>
                            <p class="text-gray-900">{{ $tenant->contact_address }}</p>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Billing Summary -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Billing Summary</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Total Invoices</span>
                            <span class="font-semibold text-gray-900">{{ $stats['total_invoices'] ?? 0 }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Paid</span>
                            <span class="font-semibold text-green-600">{{ $stats['paid_invoices'] ?? 0 }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Pending</span>
                            <span class="font-semibold text-yellow-600">{{ $stats['pending_invoices'] ?? 0 }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Overdue</span>
                            <span class="font-semibold text-red-600">{{ $stats['overdue_invoices'] ?? 0 }}</span>
                        </div>
                        <div class="pt-3 border-t border-gray-200">
                            <div class="flex justify-between">
                                <span class="text-sm font-medium text-gray-900">Lifetime Revenue</span>
                                <span class="font-bold text-lg text-blue-600">
                                    Rp {{ number_format($stats['lifetime_revenue'] ?? 0, 0, ',', '.') }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <a href="{{ route('platform.admin.tenants.billing', $tenant) }}" class="mt-4 block w-full text-center bg-gray-100 hover:bg-gray-200 text-gray-900 px-4 py-2 rounded-lg font-semibold transition duration-150">
                        View All Invoices
                    </a>
                </div>
            </div>
        </div>
    </main>

    <script>
        // Usage Trends Chart
        const usageCtx = document.getElementById('usageChart').getContext('2d');
        new Chart(usageCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($usageData['dates'] ?? []) !!},
                datasets: [
                    {
                        label: 'Users',
                        data: {!! json_encode($usageData['users'] ?? []) !!},
                        borderColor: 'rgb(59, 130, 246)',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        tension: 0.4
                    },
                    {
                        label: 'Bookings',
                        data: {!! json_encode($usageData['bookings'] ?? []) !!},
                        borderColor: 'rgb(147, 51, 234)',
                        backgroundColor: 'rgba(147, 51, 234, 0.1)',
                        tension: 0.4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>
