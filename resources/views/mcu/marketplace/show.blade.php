<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $package->name }} - MCU Package</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <a href="{{ route('mcu.marketplace.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                ← Back to Marketplace
            </a>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Package Header -->
                <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                    <div class="bg-gradient-to-r from-blue-600 to-blue-700 p-8 text-white">
                        <div class="flex items-start justify-between">
                            <div>
                                <span class="inline-block px-3 py-1 bg-white/20 rounded-full text-sm font-medium mb-3 capitalize">
                                    {{ $package->category }}
                                </span>
                                <h1 class="text-3xl font-bold mb-2">{{ $package->name }}</h1>
                                <p class="text-blue-100">{{ $package->provider->name }}</p>
                            </div>
                            @if($package->is_featured)
                            <span class="bg-yellow-400 text-yellow-900 px-3 py-1 rounded-full text-sm font-semibold">
                                ⭐ Featured
                            </span>
                            @endif
                        </div>

                        <div class="mt-6 flex items-baseline">
                            @if($package->hasDiscount())
                            <div class="flex items-baseline">
                                <span class="text-4xl font-bold">Rp {{ number_format($package->final_price, 0, ',', '.') }}</span>
                                <span class="ml-3 text-xl text-blue-200 line-through">Rp {{ number_format($package->price, 0, ',', '.') }}</span>
                                <span class="ml-3 bg-red-500 text-white px-3 py-1 rounded-full text-sm font-bold">
                                    SAVE {{ $package->discount_percentage }}%
                                </span>
                            </div>
                            @else
                            <span class="text-4xl font-bold">Rp {{ number_format($package->price, 0, ',', '.') }}</span>
                            @endif
                        </div>
                    </div>

                    <div class="p-8">
                        <h2 class="text-xl font-bold text-gray-900 mb-4">Description</h2>
                        <p class="text-gray-600 leading-relaxed">{{ $package->description }}</p>

                        <div class="mt-6 grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div class="text-center p-4 bg-gray-50 rounded-lg">
                                <svg class="w-8 h-8 mx-auto text-blue-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <p class="text-sm text-gray-600">Duration</p>
                                <p class="font-semibold text-gray-900">~{{ $package->duration_minutes }} min</p>
                            </div>
                            <div class="text-center p-4 bg-gray-50 rounded-lg">
                                <svg class="w-8 h-8 mx-auto text-green-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <p class="text-sm text-gray-600">Valid For</p>
                                <p class="font-semibold text-gray-900">{{ $package->validity_days }} days</p>
                            </div>
                            <div class="text-center p-4 bg-gray-50 rounded-lg">
                                <svg class="w-8 h-8 mx-auto text-purple-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                                <p class="text-sm text-gray-600">For</p>
                                <p class="font-semibold text-gray-900 capitalize">{{ $package->gender_target }}</p>
                            </div>
                            <div class="text-center p-4 bg-gray-50 rounded-lg">
                                <svg class="w-8 h-8 mx-auto text-red-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                </svg>
                                <p class="text-sm text-gray-600">Age Range</p>
                                <p class="font-semibold text-gray-900">{{ $package->min_age }}-{{ $package->max_age }} years</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- What's Included -->
                <div class="bg-white rounded-lg shadow-lg p-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">What's Included</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($package->inclusions as $inclusion)
                        <div class="flex items-start">
                            <svg class="w-6 h-6 text-green-500 mr-3 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-gray-700">{{ $inclusion }}</span>
                        </div>
                        @endforeach
                    </div>

                    @if($package->exclusions && count($package->exclusions) > 0)
                    <div class="mt-8">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Not Included</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($package->exclusions as $exclusion)
                            <div class="flex items-start">
                                <svg class="w-6 h-6 text-red-500 mr-3 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="text-gray-700">{{ $exclusion }}</span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Preparation Instructions -->
                @if($package->preparation_instructions && count($package->preparation_instructions) > 0)
                <div class="bg-yellow-50 border-l-4 border-yellow-400 rounded-lg p-8">
                    <div class="flex items-start">
                        <svg class="w-6 h-6 text-yellow-600 mr-3 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-yellow-900 mb-4">Important Preparation Instructions</h3>
                            <ul class="space-y-2">
                                @foreach($package->preparation_instructions as $instruction)
                                <li class="text-yellow-800">• {{ $instruction }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Related Packages -->
                @if($relatedPackages->count() > 0)
                <div class="bg-white rounded-lg shadow-lg p-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Other Packages from {{ $package->provider->name }}</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @foreach($relatedPackages as $related)
                        <a href="{{ route('mcu.marketplace.package', $related) }}" class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition duration-200">
                            <h3 class="font-semibold text-gray-900 mb-2">{{ $related->name }}</h3>
                            <p class="text-sm text-gray-600 mb-3 line-clamp-2">{{ $related->description }}</p>
                            <div class="flex items-center justify-between">
                                <span class="text-lg font-bold text-blue-600">Rp {{ number_format($related->final_price, 0, ',', '.') }}</span>
                                <span class="text-sm text-blue-600 font-medium">View Details →</span>
                            </div>
                        </a>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-lg p-6 sticky top-6">
                    <!-- Provider Info -->
                    <div class="mb-6 pb-6 border-b border-gray-200">
                        <h3 class="font-semibold text-gray-900 mb-3">Provider</h3>
                        <div class="flex items-start">
                            <div class="flex-1">
                                <h4 class="font-medium text-gray-900">{{ $package->provider->name }}</h4>
                                <div class="flex items-center mt-1">
                                    <svg class="w-4 h-4 text-yellow-400 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                    </svg>
                                    <span class="text-sm font-medium text-gray-700">{{ $package->provider->rating }}</span>
                                    <span class="text-sm text-gray-500 ml-1">({{ number_format($package->provider->total_reviews) }} reviews)</span>
                                </div>
                                <p class="text-sm text-gray-600 mt-2">
                                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    {{ $package->provider->city }}, {{ $package->provider->province }}
                                </p>
                            </div>
                        </div>
                        <a href="{{ route('mcu.marketplace.provider', $package->provider) }}" class="mt-4 block text-center text-sm text-blue-600 hover:text-blue-800 font-medium">
                            View Provider Profile →
                        </a>
                    </div>

                    <!-- Booking Stats -->
                    <div class="mb-6 pb-6 border-b border-gray-200">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-600">Bookings</span>
                            <span class="font-semibold text-gray-900">{{ number_format($package->booking_count) }}</span>
                        </div>
                    </div>

                    <!-- CTA Button -->
                    <a href="{{ route('mcu.bookings.create', $package) }}" class="block w-full bg-blue-600 hover:bg-blue-700 text-white text-center px-6 py-4 rounded-lg font-semibold text-lg transition duration-150 shadow-lg">
                        Book Now
                    </a>

                    <!-- Contact Info -->
                    <div class="mt-6 text-sm text-gray-600">
                        <p class="mb-2">
                            <strong>Phone:</strong><br>
                            {{ $package->provider->phone }}
                        </p>
                        <p>
                            <strong>Email:</strong><br>
                            {{ $package->provider->email }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
