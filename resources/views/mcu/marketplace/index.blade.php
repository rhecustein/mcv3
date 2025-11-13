<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MCU Marketplace - {{ $tenant->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Medical Check-Up Marketplace</h1>
                    <p class="text-gray-600 mt-1">Pesan paket MCU dari provider terpercaya</p>
                </div>
                <a href="{{ route('mcu.bookings.index') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition duration-150">
                    My Bookings
                </a>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Filters -->
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari paket MCU..." class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                    <select name="category" class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Categories</option>
                        <option value="basic" {{ request('category') === 'basic' ? 'selected' : '' }}>Basic</option>
                        <option value="standard" {{ request('category') === 'standard' ? 'selected' : '' }}>Standard</option>
                        <option value="premium" {{ request('category') === 'premium' ? 'selected' : '' }}>Premium</option>
                        <option value="executive" {{ request('category') === 'executive' ? 'selected' : '' }}>Executive</option>
                        <option value="specialized" {{ request('category') === 'specialized' ? 'selected' : '' }}>Specialized</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Price Range</label>
                    <select name="price" class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Prices</option>
                        <option value="0-1000000">< Rp 1 Juta</option>
                        <option value="1000000-3000000">Rp 1-3 Juta</option>
                        <option value="3000000-5000000">Rp 3-5 Juta</option>
                        <option value="5000000-999999999">> Rp 5 Juta</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition duration-150">
                        Filter
                    </button>
                </div>
            </form>
        </div>

        <!-- Featured Packages -->
        @if($featuredPackages->count() > 0)
        <div class="mb-12">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Paket Unggulan</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($featuredPackages as $package)
                <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition duration-300">
                    <div class="bg-gradient-to-r from-blue-500 to-blue-600 p-4">
                        <h3 class="text-white font-bold text-lg">{{ $package->name }}</h3>
                        <p class="text-blue-100 text-sm">{{ $package->provider->name }}</p>
                    </div>
                    <div class="p-6">
                        <div class="mb-4">
                            @if($package->hasDiscount())
                            <div class="flex items-baseline">
                                <span class="text-3xl font-bold text-gray-900">Rp {{ number_format($package->final_price, 0, ',', '.') }}</span>
                                <span class="ml-2 text-sm text-gray-500 line-through">Rp {{ number_format($package->price, 0, ',', '.') }}</span>
                            </div>
                            <span class="inline-block mt-2 px-2 py-1 bg-red-100 text-red-800 text-xs font-medium rounded">
                                Save {{ $package->discount_percentage }}%
                            </span>
                            @else
                            <span class="text-3xl font-bold text-gray-900">Rp {{ number_format($package->price, 0, ',', '.') }}</span>
                            @endif
                        </div>
                        <div class="space-y-2 mb-4">
                            <div class="flex items-center text-sm text-gray-600">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                </svg>
                                ~{{ $package->duration_minutes }} menit
                            </div>
                            <div class="flex items-center text-sm text-gray-600">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"></path>
                                    <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"></path>
                                </svg>
                                {{ count($package->inclusions) }} pemeriksaan
                            </div>
                        </div>
                        <div class="flex space-x-2">
                            <a href="{{ route('mcu.marketplace.package', $package) }}" class="flex-1 text-center bg-gray-100 hover:bg-gray-200 text-gray-800 px-4 py-2 rounded-lg font-medium transition duration-150">
                                Detail
                            </a>
                            <a href="{{ route('mcu.bookings.create', $package) }}" class="flex-1 text-center bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition duration-150">
                                Book Now
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- All Packages -->
        <div>
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Semua Paket MCU</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($packages as $package)
                <div class="bg-white rounded-lg shadow overflow-hidden hover:shadow-lg transition duration-300">
                    <div class="p-6">
                        <div class="flex items-start justify-between mb-4">
                            <div>
                                <h3 class="font-bold text-lg text-gray-900">{{ $package->name }}</h3>
                                <p class="text-sm text-gray-600">{{ $package->provider->name }}</p>
                            </div>
                            <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs font-medium rounded capitalize">
                                {{ $package->category }}
                            </span>
                        </div>

                        <p class="text-sm text-gray-600 mb-4 line-clamp-2">{{ $package->description }}</p>

                        <div class="mb-4">
                            @if($package->hasDiscount())
                            <div class="flex items-baseline">
                                <span class="text-2xl font-bold text-gray-900">Rp {{ number_format($package->final_price, 0, ',', '.') }}</span>
                                <span class="ml-2 text-sm text-gray-500 line-through">Rp {{ number_format($package->price, 0, ',', '.') }}</span>
                            </div>
                            @else
                            <span class="text-2xl font-bold text-gray-900">Rp {{ number_format($package->price, 0, ',', '.') }}</span>
                            @endif
                        </div>

                        <div class="flex items-center text-sm text-gray-600 mb-4">
                            <svg class="w-4 h-4 mr-1 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                            </svg>
                            <span class="font-medium">{{ $package->provider->rating }}</span>
                            <span class="mx-1">·</span>
                            <span>{{ $package->provider->city }}</span>
                        </div>

                        <div class="flex space-x-2">
                            <a href="{{ route('mcu.marketplace.package', $package) }}" class="flex-1 text-center bg-gray-100 hover:bg-gray-200 text-gray-800 px-4 py-2 rounded-lg font-medium transition duration-150">
                                Detail
                            </a>
                            <a href="{{ route('mcu.bookings.create', $package) }}" class="flex-1 text-center bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition duration-150">
                                Book
                            </a>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-span-3 text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No packages found</h3>
                    <p class="mt-1 text-sm text-gray-500">Try adjusting your filters</p>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Pagination -->
        @if($packages->hasPages())
        <div class="mt-8">
            {{ $packages->links() }}
        </div>
        @endif
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <p class="text-center text-gray-500 text-sm">© 2025 {{ $tenant->name }}. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
