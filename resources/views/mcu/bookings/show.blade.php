<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking #{{ $booking->booking_number }} - MCU Booking</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.3/build/qrcode.min.js"></script>
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <a href="{{ route('mcu.bookings.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                ← Back to My Bookings
            </a>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Status Banner -->
        <div class="mb-8">
            @if($booking->status === 'confirmed')
            <div class="bg-green-50 border-l-4 border-green-500 p-6 rounded-lg">
                <div class="flex items-center">
                    <svg class="w-8 h-8 text-green-500 mr-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    <div class="flex-1">
                        <h2 class="text-2xl font-bold text-green-900">Booking Confirmed!</h2>
                        <p class="text-green-700 mt-1">Your MCU appointment has been confirmed. Please arrive 15 minutes early.</p>
                    </div>
                </div>
            </div>
            @elseif($booking->status === 'pending')
            <div class="bg-yellow-50 border-l-4 border-yellow-500 p-6 rounded-lg">
                <div class="flex items-center">
                    <svg class="w-8 h-8 text-yellow-500 mr-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                    </svg>
                    <div class="flex-1">
                        <h2 class="text-2xl font-bold text-yellow-900">Awaiting Payment</h2>
                        <p class="text-yellow-700 mt-1">Please complete your payment to confirm this booking.</p>
                    </div>
                    @if($booking->payment && $booking->payment->status === 'pending')
                    <a href="{{ $booking->payment->payment_url }}" class="ml-4 bg-yellow-600 hover:bg-yellow-700 text-white px-6 py-3 rounded-lg font-semibold transition duration-150">
                        Complete Payment
                    </a>
                    @endif
                </div>
            </div>
            @elseif($booking->status === 'cancelled')
            <div class="bg-red-50 border-l-4 border-red-500 p-6 rounded-lg">
                <div class="flex items-center">
                    <svg class="w-8 h-8 text-red-500 mr-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                    </svg>
                    <div class="flex-1">
                        <h2 class="text-2xl font-bold text-red-900">Booking Cancelled</h2>
                        <p class="text-red-700 mt-1">This booking has been cancelled.</p>
                        @if($booking->cancellation_reason)
                        <p class="text-red-600 text-sm mt-2"><strong>Reason:</strong> {{ $booking->cancellation_reason }}</p>
                        @endif
                    </div>
                </div>
            </div>
            @elseif($booking->status === 'completed')
            <div class="bg-blue-50 border-l-4 border-blue-500 p-6 rounded-lg">
                <div class="flex items-center">
                    <svg class="w-8 h-8 text-blue-500 mr-4" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"></path>
                        <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"></path>
                    </svg>
                    <div class="flex-1">
                        <h2 class="text-2xl font-bold text-blue-900">MCU Completed</h2>
                        <p class="text-blue-700 mt-1">Your medical checkup has been completed.</p>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Booking Information -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Booking Information</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-600">Booking Number</p>
                            <p class="font-semibold text-gray-900">{{ $booking->booking_number }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Booking Date</p>
                            <p class="font-semibold text-gray-900">{{ $booking->created_at->format('d M Y, H:i') }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Status</p>
                            <span class="inline-block px-3 py-1 text-sm font-semibold rounded-full
                                @if($booking->status === 'confirmed') bg-green-100 text-green-800
                                @elseif($booking->status === 'pending') bg-yellow-100 text-yellow-800
                                @elseif($booking->status === 'cancelled') bg-red-100 text-red-800
                                @elseif($booking->status === 'completed') bg-blue-100 text-blue-800
                                @endif">
                                {{ ucfirst($booking->status) }}
                            </span>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Total Amount</p>
                            <p class="font-bold text-blue-600 text-lg">Rp {{ number_format($booking->total_amount, 0, ',', '.') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Schedule Information -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Schedule</h3>
                    <div class="space-y-4">
                        <div class="flex items-start">
                            <svg class="w-6 h-6 text-blue-600 mr-3 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <div>
                                <p class="text-sm text-gray-600">Date & Time</p>
                                <p class="font-semibold text-gray-900">{{ $booking->booking_date->format('l, d F Y') }}</p>
                                <p class="text-gray-700">{{ $booking->booking_time }}</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <svg class="w-6 h-6 text-blue-600 mr-3 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <div>
                                <p class="text-sm text-gray-600">Location</p>
                                <p class="font-semibold text-gray-900">{{ $booking->provider->name }}</p>
                                <p class="text-gray-700">{{ $booking->provider->address }}</p>
                                <p class="text-gray-700">{{ $booking->provider->city }}, {{ $booking->provider->province }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Patient Information -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Patient Information</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-600">Full Name</p>
                            <p class="font-semibold text-gray-900">{{ $booking->patient_name }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Email</p>
                            <p class="font-semibold text-gray-900">{{ $booking->patient_email }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Phone</p>
                            <p class="font-semibold text-gray-900">{{ $booking->patient_phone }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Birth Date</p>
                            <p class="font-semibold text-gray-900">{{ $booking->patient_birth_date->format('d M Y') }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Gender</p>
                            <p class="font-semibold text-gray-900 capitalize">{{ $booking->patient_gender }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">NIK</p>
                            <p class="font-semibold text-gray-900">{{ $booking->patient_nik }}</p>
                        </div>
                    </div>
                </div>

                <!-- Package Details -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Package Details</h3>
                    <div class="flex items-start">
                        <div class="flex-1">
                            <h4 class="font-semibold text-gray-900 text-lg">{{ $booking->package->name }}</h4>
                            <p class="text-gray-600 mt-1">{{ $booking->package->description }}</p>
                            <div class="mt-4 flex items-center text-sm text-gray-600">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Duration: ~{{ $booking->package->duration_minutes }} minutes
                            </div>
                        </div>
                    </div>

                    @if($booking->package->inclusions && count($booking->package->inclusions) > 0)
                    <div class="mt-6">
                        <h5 class="font-semibold text-gray-900 mb-3">What's Included</h5>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                            @foreach($booking->package->inclusions as $inclusion)
                            <div class="flex items-start">
                                <svg class="w-5 h-5 text-green-500 mr-2 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="text-sm text-gray-700">{{ $inclusion }}</span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Payment Information -->
                @if($booking->payment)
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Payment Information</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-600">Payment Number</p>
                            <p class="font-semibold text-gray-900">{{ $booking->payment->payment_number }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Payment Status</p>
                            <span class="inline-block px-3 py-1 text-sm font-semibold rounded-full
                                @if($booking->payment->status === 'paid') bg-green-100 text-green-800
                                @elseif($booking->payment->status === 'pending') bg-yellow-100 text-yellow-800
                                @elseif($booking->payment->status === 'failed') bg-red-100 text-red-800
                                @endif">
                                {{ ucfirst($booking->payment->status) }}
                            </span>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Payment Method</p>
                            <p class="font-semibold text-gray-900 capitalize">{{ str_replace('_', ' ', $booking->payment->payment_method) }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Amount</p>
                            <p class="font-bold text-blue-600">Rp {{ number_format($booking->payment->amount, 0, ',', '.') }}</p>
                        </div>
                        @if($booking->payment->paid_at)
                        <div>
                            <p class="text-sm text-gray-600">Paid At</p>
                            <p class="font-semibold text-gray-900">{{ $booking->payment->paid_at->format('d M Y, H:i') }}</p>
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                <!-- Preparation Instructions -->
                @if($booking->package->preparation_instructions && count($booking->package->preparation_instructions) > 0)
                <div class="bg-yellow-50 border-l-4 border-yellow-400 rounded-lg p-6">
                    <div class="flex items-start">
                        <svg class="w-6 h-6 text-yellow-600 mr-3 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-yellow-900 mb-3">Important Preparation Instructions</h3>
                            <ul class="space-y-2">
                                @foreach($booking->package->preparation_instructions as $instruction)
                                <li class="text-yellow-800 text-sm">• {{ $instruction }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <div class="space-y-6">
                    <!-- QR Code -->
                    @if($booking->status === 'confirmed')
                    <div class="bg-white rounded-lg shadow-lg p-6 text-center">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Booking QR Code</h3>
                        <div class="bg-gray-50 p-4 rounded-lg inline-block">
                            <canvas id="qrcode"></canvas>
                        </div>
                        <p class="text-sm text-gray-600 mt-4">Show this QR code at the provider's location</p>
                    </div>
                    @endif

                    <!-- Quick Actions -->
                    <div class="bg-white rounded-lg shadow-lg p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Actions</h3>
                        <div class="space-y-3">
                            @if($booking->status === 'confirmed')
                            <a href="#" onclick="window.print(); return false;" class="block w-full bg-blue-600 hover:bg-blue-700 text-white text-center px-4 py-3 rounded-lg font-semibold transition duration-150">
                                <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                                </svg>
                                Print Voucher
                            </a>
                            <a href="#" class="block w-full bg-green-600 hover:bg-green-700 text-white text-center px-4 py-3 rounded-lg font-semibold transition duration-150">
                                <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                </svg>
                                Download Voucher
                            </a>
                            @endif

                            @if($booking->status === 'confirmed' && $booking->booking_date->isFuture())
                            <button onclick="if(confirm('Are you sure you want to cancel this booking?')) document.getElementById('cancel-form').submit();" class="block w-full bg-red-600 hover:bg-red-700 text-white text-center px-4 py-3 rounded-lg font-semibold transition duration-150">
                                <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Cancel Booking
                            </button>
                            <form id="cancel-form" action="{{ route('mcu.bookings.cancel', $booking) }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                            @endif

                            <a href="{{ route('mcu.marketplace.provider', $booking->provider) }}" class="block w-full bg-gray-600 hover:bg-gray-700 text-white text-center px-4 py-3 rounded-lg font-semibold transition duration-150">
                                <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                                View Provider
                            </a>
                        </div>
                    </div>

                    <!-- Contact Provider -->
                    <div class="bg-white rounded-lg shadow-lg p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Need Help?</h3>
                        <div class="space-y-3 text-sm">
                            <div>
                                <p class="text-gray-600 mb-1">Provider Phone</p>
                                <a href="tel:{{ $booking->provider->phone }}" class="text-blue-600 hover:text-blue-800 font-semibold">
                                    {{ $booking->provider->phone }}
                                </a>
                            </div>
                            <div>
                                <p class="text-gray-600 mb-1">Provider Email</p>
                                <a href="mailto:{{ $booking->provider->email }}" class="text-blue-600 hover:text-blue-800 font-semibold">
                                    {{ $booking->provider->email }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    @if($booking->status === 'confirmed')
    <script>
        // Generate QR Code
        const qrCanvas = document.getElementById('qrcode');
        const bookingData = {
            booking_number: '{{ $booking->booking_number }}',
            patient_name: '{{ $booking->patient_name }}',
            booking_date: '{{ $booking->booking_date->format('Y-m-d') }}',
            booking_time: '{{ $booking->booking_time }}'
        };

        QRCode.toCanvas(qrCanvas, JSON.stringify(bookingData), {
            width: 200,
            margin: 2,
            color: {
                dark: '#1F2937',
                light: '#F9FAFB'
            }
        });
    </script>
    @endif
</body>
</html>
