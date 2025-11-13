<nav class="bg-white shadow-lg">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <div class="flex-shrink-0 flex items-center">
                    <h1 class="text-2xl font-bold text-blue-600">MCv3 Platform</h1>
                </div>
                <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                    <a href="{{ route('platform.admin.dashboard') }}" class="{{ request()->routeIs('platform.admin.dashboard') ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }} inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                        Dashboard
                    </a>
                    <a href="{{ route('platform.admin.tenants.index') }}" class="{{ request()->routeIs('platform.admin.tenants.*') ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }} inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                        Tenants
                    </a>
                    <a href="{{ route('platform.billing.index') }}" class="{{ request()->routeIs('platform.billing.*') ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }} inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                        Billing
                    </a>
                    <a href="{{ route('platform.admin.analytics') }}" class="{{ request()->routeIs('platform.admin.analytics') ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }} inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
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
