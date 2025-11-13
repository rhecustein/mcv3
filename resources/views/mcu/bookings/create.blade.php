<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book MCU Package - {{ $package->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="mb-8">
            <a href="{{ route('mcu.marketplace.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                ← Back to Marketplace
            </a>
            <h1 class="text-3xl font-bold text-gray-900 mt-4">Book MCU Package</h1>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Booking Form -->
            <div class="lg:col-span-2">
                <form method="POST" action="{{ route('mcu.bookings.store', $package) }}" class="bg-white rounded-lg shadow p-6">
                    @csrf

                    <!-- Patient Information -->
                    <div class="mb-8">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">Patient Information</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Full Name *</label>
                                <input type="text" name="patient_name" required class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
                                <input type="email" name="patient_email" required class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Phone *</label>
                                <input type="text" name="patient_phone" required class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Birth Date *</label>
                                <input type="date" name="patient_birth_date" required class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Gender *</label>
                                <select name="patient_gender" required class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Select Gender</option>
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">NIK (Optional)</label>
                                <input type="text" name="patient_nik" class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>
                    </div>

                    <!-- Booking Schedule -->
                    <div class="mb-8">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">Schedule</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Booking Date *</label>
                                <input type="date" name="booking_date" required min="{{ date('Y-m-d') }}" class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Preferred Time *</label>
                                <select name="booking_time" required class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Select Time</option>
                                    <option value="07:00">07:00</option>
                                    <option value="08:00">08:00</option>
                                    <option value="09:00">09:00</option>
                                    <option value="10:00">10:00</option>
                                    <option value="11:00">11:00</option>
                                    <option value="13:00">13:00</option>
                                    <option value="14:00">14:00</option>
                                    <option value="15:00">15:00</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Special Notes -->
                    <div class="mb-8">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Special Notes (Optional)</label>
                        <textarea name="special_notes" rows="3" class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" placeholder="Any special requests or medical conditions we should know about..."></textarea>
                    </div>

                    <!-- Payment Method -->
                    <div class="mb-8">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">Payment Method</h2>
                        <div class="space-y-3">
                            <label class="flex items-center p-4 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50">
                                <input type="radio" name="payment_method" value="credit_card" checked class="mr-3">
                                <div class="flex-1">
                                    <p class="font-medium text-gray-900">Credit/Debit Card</p>
                                    <p class="text-sm text-gray-500">Visa, Mastercard, JCB</p>
                                </div>
                            </label>
                            <label class="flex items-center p-4 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50">
                                <input type="radio" name="payment_method" value="bank_transfer" class="mr-3">
                                <div class="flex-1">
                                    <p class="font-medium text-gray-900">Bank Transfer</p>
                                    <p class="text-sm text-gray-500">BCA, Mandiri, BNI, BRI</p>
                                </div>
                            </label>
                            <label class="flex items-center p-4 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50">
                                <input type="radio" name="payment_method" value="e_wallet" class="mr-3">
                                <div class="flex-1">
                                    <p class="font-medium text-gray-900">E-Wallet</p>
                                    <p class="text-sm text-gray-500">GoPay, OVO, DANA, LinkAja</p>
                                </div>
                            </label>
                            <label class="flex items-center p-4 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50">
                                <input type="radio" name="payment_method" value="qris" class="mr-3">
                                <div class="flex-1">
                                    <p class="font-medium text-gray-900">QRIS</p>
                                    <p class="text-sm text-gray-500">Scan QR Code</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Terms & Conditions -->
                    <div class="mb-6">
                        <label class="flex items-start">
                            <input type="checkbox" required class="mt-1 mr-3">
                            <span class="text-sm text-gray-600">
                                I agree to the <a href="#" class="text-blue-600 hover:underline">Terms & Conditions</a> and <a href="#" class="text-blue-600 hover:underline">Privacy Policy</a>
                            </span>
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition duration-150">
                        Proceed to Payment
                    </button>
                </form>
            </div>

            <!-- Order Summary -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow p-6 sticky top-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Order Summary</h2>

                    <div class="border-b border-gray-200 pb-4 mb-4">
                        <h3 class="font-medium text-gray-900">{{ $package->name }}</h3>
                        <p class="text-sm text-gray-600">{{ $package->provider->name }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ $package->provider->city }}</p>
                    </div>

                    <div class="space-y-2 mb-4">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Package Price</span>
                            <span class="text-gray-900">Rp {{ number_format($package->price, 0, ',', '.') }}</span>
                        </div>
                        @if($package->hasDiscount())
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Discount</span>
                            <span class="text-green-600">-Rp {{ number_format($package->discount_amount, 0, ',', '.') }}</span>
                        </div>
                        @endif
                    </div>

                    <div class="border-t border-gray-200 pt-4">
                        <div class="flex justify-between">
                            <span class="text-lg font-semibold text-gray-900">Total</span>
                            <span class="text-2xl font-bold text-blue-600">Rp {{ number_format($package->final_price, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <div class="mt-6 p-4 bg-blue-50 rounded-lg">
                        <h4 class="font-medium text-blue-900 text-sm mb-2">What's Included:</h4>
                        <ul class="space-y-1">
                            @foreach(array_slice($package->inclusions, 0, 5) as $inclusion)
                            <li class="text-xs text-blue-800 flex items-start">
                                <svg class="w-4 h-4 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                {{ $inclusion }}
                            </li>
                            @endforeach
                            @if(count($package->inclusions) > 5)
                            <li class="text-xs text-blue-600 ml-6">+{{ count($package->inclusions) - 5 }} more</li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
