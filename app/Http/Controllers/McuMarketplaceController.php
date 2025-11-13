<?php

namespace App\Http\Controllers;

use App\Models\McuProvider;
use App\Models\McuPackage;
use App\Models\McuBooking;
use App\Models\Payment;
use Illuminate\Http\Request;

class McuMarketplaceController extends Controller
{
    /**
     * Display MCU marketplace homepage
     */
    public function index(Request $request)
    {
        // Featured packages
        $featuredPackages = McuPackage::with('provider')
            ->active()
            ->featured()
            ->limit(8)
            ->get();

        // All packages with filters
        $query = McuPackage::with('provider')->active();

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('city')) {
            $query->whereHas('provider', function($q) use ($request) {
                $q->where('city', $request->city);
            });
        }

        if ($request->filled('price_min')) {
            $query->where('price', '>=', $request->price_min);
        }

        if ($request->filled('price_max')) {
            $query->where('price', '<=', $request->price_max);
        }

        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        $packages = $query->paginate(12);

        return view('mcu.marketplace.index', compact('featuredPackages', 'packages'));
    }

    /**
     * Show package details
     */
    public function showPackage(McuPackage $package)
    {
        $package->load('provider');

        // Related packages
        $relatedPackages = McuPackage::with('provider')
            ->where('provider_id', $package->provider_id)
            ->where('id', '!=', $package->id)
            ->active()
            ->limit(4)
            ->get();

        return view('mcu.marketplace.show', compact('package', 'relatedPackages'));
    }

    /**
     * Show provider details
     */
    public function showProvider(McuProvider $provider)
    {
        $provider->load('activePackages');

        return view('mcu.marketplace.provider', compact('provider'));
    }

    /**
     * Show booking form
     */
    public function showBookingForm(McuPackage $package)
    {
        $package->load('provider');

        return view('mcu.bookings.create', compact('package'));
    }

    /**
     * Store booking
     */
    public function storeBooking(Request $request, McuPackage $package)
    {
        $validated = $request->validate([
            'patient_name' => 'required|string|max:255',
            'patient_email' => 'required|email',
            'patient_phone' => 'required|string|max:20',
            'patient_birth_date' => 'required|date',
            'patient_gender' => 'required|in:male,female',
            'patient_nik' => 'nullable|string|max:20',
            'booking_date' => 'required|date|after_or_equal:today',
            'booking_time' => 'required',
            'special_notes' => 'nullable|string',
            'payment_method' => 'required|in:bank_transfer,credit_card,e_wallet,qris',
        ]);

        $tenant = app('tenant');

        // Create booking
        $booking = McuBooking::create([
            'tenant_id' => $tenant->id,
            'package_id' => $package->id,
            'provider_id' => $package->provider_id,
            'booking_number' => McuBooking::generateBookingNumber(),
            'patient_name' => $validated['patient_name'],
            'patient_email' => $validated['patient_email'],
            'patient_phone' => $validated['patient_phone'],
            'patient_birth_date' => $validated['patient_birth_date'],
            'patient_gender' => $validated['patient_gender'],
            'patient_nik' => $validated['patient_nik'] ?? null,
            'booking_date' => $validated['booking_date'],
            'booking_time' => $validated['booking_time'],
            'original_price' => $package->price,
            'final_price' => $package->final_price,
            'discount_amount' => $package->discount_amount ?? 0,
            'status' => 'pending',
            'payment_status' => 'pending',
            'special_notes' => $validated['special_notes'] ?? null,
        ]);

        // Create payment
        $payment = Payment::create([
            'tenant_id' => $tenant->id,
            'payment_number' => Payment::generatePaymentNumber(),
            'payable_type' => McuBooking::class,
            'payable_id' => $booking->id,
            'amount' => $booking->final_price,
            'currency' => 'IDR',
            'payment_method' => $validated['payment_method'],
            'payment_gateway' => 'manual', // Will be updated when integrated
            'status' => 'pending',
            'expired_at' => now()->addHours(24),
        ]);

        // Update booking with payment_id
        $booking->update(['payment_id' => $payment->id]);

        // Increment package booking count
        $package->incrementBookingCount();

        return redirect()
            ->route('mcu.bookings.show', $booking)
            ->with('success', 'Booking created successfully! Please complete the payment.');
    }

    /**
     * Show booking details
     */
    public function showBooking(McuBooking $booking)
    {
        $booking->load(['package', 'provider', 'payment']);

        return view('mcu.bookings.show', compact('booking'));
    }

    /**
     * List user's bookings
     */
    public function myBookings()
    {
        $tenant = app('tenant');

        $bookings = McuBooking::where('tenant_id', $tenant->id)
            ->with(['package', 'provider', 'payment'])
            ->latest()
            ->paginate(10);

        return view('mcu.bookings.index', compact('bookings'));
    }

    /**
     * Cancel booking
     */
    public function cancelBooking(Request $request, McuBooking $booking)
    {
        $validated = $request->validate([
            'cancellation_reason' => 'required|string|max:500',
        ]);

        $booking->update([
            'status' => 'cancelled',
            'cancellation_reason' => $validated['cancellation_reason'],
            'cancelled_at' => now(),
            'cancelled_by' => auth()->id(),
        ]);

        // Refund payment if paid
        if ($booking->isPaid() && $booking->payment) {
            $booking->payment->refund();
        }

        return back()->with('success', 'Booking cancelled successfully!');
    }
}
