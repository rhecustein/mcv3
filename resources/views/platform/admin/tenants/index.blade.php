<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Tenants - MCv3 Platform</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    @include('platform.admin.partials.nav')

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="mb-8 flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Tenants</h1>
                <p class="text-gray-600 mt-1">Manage all tenant accounts and subscriptions</p>
            </div>
            <a href="{{ route('platform.admin.tenants.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition duration-150">
                + Add New Tenant
            </a>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Name, slug, or email..." class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select name="status" class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Statuses</option>
                        <option value="trial" {{ request('status') === 'trial' ? 'selected' : '' }}>Trial</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Suspended</option>
                        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Plan</label>
                    <select name="plan" class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Plans</option>
                        <option value="starter" {{ request('plan') === 'starter' ? 'selected' : '' }}>Starter</option>
                        <option value="professional" {{ request('plan') === 'professional' ? 'selected' : '' }}>Professional</option>
                        <option value="enterprise" {{ request('plan') === 'enterprise' ? 'selected' : '' }}>Enterprise</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full bg-gray-800 hover:bg-gray-900 text-white px-4 py-2 rounded-lg font-medium transition duration-150">
                        Apply Filters
                    </button>
                </div>
            </form>
        </div>

        <!-- Tenants Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tenant</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Plan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Usage</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($tenants as $tenant)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ $tenant->name }}</div>
                                    <div class="text-sm text-gray-500">
                                        <a href="http://{{ $tenant->slug }}.mcv3.local" target="_blank" class="hover:text-blue-600">
                                            {{ $tenant->slug }}.mcv3.local
                                        </a>
                                    </div>
                                    <div class="text-xs text-gray-400">{{ $tenant->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($tenant->subscription_plan === 'starter') bg-blue-100 text-blue-800
                                @elseif($tenant->subscription_plan === 'professional') bg-green-100 text-green-800
                                @else bg-purple-100 text-purple-800
                                @endif">
                                {{ ucfirst($tenant->subscription_plan) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($tenant->subscription_status === 'active') bg-green-100 text-green-800
                                @elseif($tenant->subscription_status === 'trial') bg-yellow-100 text-yellow-800
                                @elseif($tenant->subscription_status === 'suspended') bg-red-100 text-red-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ ucfirst($tenant->subscription_status) }}
                            </span>
                            @if($tenant->isTrialing())
                            <div class="text-xs text-gray-500 mt-1">{{ $tenant->trialDaysRemaining() }} days left</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <div>Users: {{ $tenant->current_users }}/{{ $tenant->max_users }}</div>
                            <div>Docs: {{ $tenant->current_documents_this_month }}/{{ $tenant->max_documents_per_month }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $tenant->created_at->format('d M Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('platform.admin.tenants.show', $tenant) }}" class="text-blue-600 hover:text-blue-900 mr-3">View</a>
                            <a href="{{ route('platform.admin.tenants.edit', $tenant) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                            @if($tenant->subscription_status !== 'suspended')
                            <form action="{{ route('platform.admin.tenants.suspend', $tenant) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Suspend this tenant?')">Suspend</button>
                            </form>
                            @else
                            <form action="{{ route('platform.admin.tenants.reactivate', $tenant) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="text-green-600 hover:text-green-900">Reactivate</button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            No tenants found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $tenants->links() }}
        </div>
    </main>
</body>
</html>
