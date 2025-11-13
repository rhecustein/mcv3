<?php

namespace App\Http\Controllers;

use App\Models\PromoCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PromoCodeController extends Controller
{
    /**
     * Validate promo code (AJAX endpoint)
     */
    public function validate(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'package_id' => 'nullable|exists:mcu_packages,id',
        ]);

        $promoCode = PromoCode::where('code', strtoupper($request->code))
            ->where('tenant_id', app('tenant')->id)
            ->first();

        if (!$promoCode) {
            return response()->json([
                'valid' => false,
                'message' => 'Invalid promo code.',
            ], 404);
        }

        $validation = $promoCode->isValid(
            Auth::user(),
            $request->amount,
            $request->package_id
        );

        if (!$validation['valid']) {
            return response()->json($validation, 400);
        }

        $discountAmount = $promoCode->calculateDiscount($request->amount);
        $finalAmount = max(0, $request->amount - $discountAmount);

        return response()->json([
            'valid' => true,
            'message' => 'Promo code is valid!',
            'promo_code' => [
                'id' => $promoCode->id,
                'code' => $promoCode->code,
                'name' => $promoCode->name,
                'description' => $promoCode->description,
                'discount_type' => $promoCode->discount_type,
                'discount_description' => $promoCode->discount_description,
            ],
            'discount_amount' => $discountAmount,
            'final_amount' => $finalAmount,
            'savings' => sprintf('You save Rp %s', number_format($discountAmount, 0, ',', '.')),
        ]);
    }

    /**
     * Apply promo code to booking
     */
    public function apply(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
            'booking_id' => 'required|exists:mcu_bookings,id',
        ]);

        $promoCode = PromoCode::where('code', strtoupper($request->code))
            ->where('tenant_id', app('tenant')->id)
            ->first();

        if (!$promoCode) {
            return back()->with('error', 'Invalid promo code.');
        }

        $booking = \App\Models\McuBooking::findOrFail($request->booking_id);

        // Check if booking belongs to current tenant
        if ($booking->tenant_id !== app('tenant')->id) {
            abort(403, 'Unauthorized');
        }

        // Check if promo code already applied
        if ($booking->promo_code) {
            return back()->with('error', 'A promo code has already been applied to this booking.');
        }

        $result = $promoCode->apply(Auth::user(), $booking, $booking->original_price);

        if (!$result['success']) {
            return back()->with('error', $result['message']);
        }

        // Update booking
        $booking->update([
            'promo_code' => $promoCode->code,
            'discount_amount' => $result['discount_amount'],
            'final_price' => $result['final_amount'],
        ]);

        return back()->with('success', $result['message']);
    }

    /**
     * Remove promo code from booking
     */
    public function remove(Request $request)
    {
        $request->validate([
            'booking_id' => 'required|exists:mcu_bookings,id',
        ]);

        $booking = \App\Models\McuBooking::findOrFail($request->booking_id);

        // Check if booking belongs to current tenant
        if ($booking->tenant_id !== app('tenant')->id) {
            abort(403, 'Unauthorized');
        }

        // Reset prices
        $booking->update([
            'promo_code' => null,
            'discount_amount' => 0,
            'final_price' => $booking->original_price,
        ]);

        // Note: We don't delete the usage record for tracking purposes

        return back()->with('success', 'Promo code removed.');
    }

    /**
     * List available promo codes (for public display)
     */
    public function index()
    {
        $promoCodes = PromoCode::where('tenant_id', app('tenant')->id)
            ->public()
            ->valid()
            ->latest()
            ->paginate(20);

        return view('promo-codes.index', compact('promoCodes'));
    }

    /**
     * Admin: List all promo codes
     */
    public function adminIndex()
    {
        $promoCodes = PromoCode::where('tenant_id', app('tenant')->id)
            ->withCount('usages')
            ->latest()
            ->paginate(20);

        return view('admin.promo-codes.index', compact('promoCodes'));
    }

    /**
     * Admin: Create promo code
     */
    public function create()
    {
        return view('admin.promo-codes.create');
    }

    /**
     * Admin: Store new promo code
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:promo_codes,code',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'discount_type' => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:0',
            'max_discount_amount' => 'nullable|numeric|min:0',
            'min_purchase_amount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'usage_limit_per_user' => 'required|integer|min:1',
            'valid_from' => 'nullable|date',
            'valid_until' => 'nullable|date|after:valid_from',
            'applicable_to' => 'required|in:all,packages,providers,categories',
            'applicable_ids' => 'nullable|array',
            'user_eligibility' => 'required|in:all,new,existing',
            'is_active' => 'boolean',
            'is_public' => 'boolean',
        ]);

        $validated['tenant_id'] = app('tenant')->id;
        $validated['code'] = strtoupper($validated['code']);
        $validated['created_by'] = Auth::id();

        PromoCode::create($validated);

        return redirect()->route('admin.promo-codes.index')
            ->with('success', 'Promo code created successfully!');
    }

    /**
     * Admin: Show promo code details
     */
    public function show(PromoCode $promoCode)
    {
        $promoCode->load(['usages.user', 'usages.booking']);

        $stats = [
            'total_usage' => $promoCode->usage_count,
            'total_discount' => $promoCode->usages()->sum('discount_amount'),
            'unique_users' => $promoCode->usages()->distinct('user_id')->count('user_id'),
        ];

        return view('admin.promo-codes.show', compact('promoCode', 'stats'));
    }

    /**
     * Admin: Edit promo code
     */
    public function edit(PromoCode $promoCode)
    {
        return view('admin.promo-codes.edit', compact('promoCode'));
    }

    /**
     * Admin: Update promo code
     */
    public function update(Request $request, PromoCode $promoCode)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'discount_type' => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:0',
            'max_discount_amount' => 'nullable|numeric|min:0',
            'min_purchase_amount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'usage_limit_per_user' => 'required|integer|min:1',
            'valid_from' => 'nullable|date',
            'valid_until' => 'nullable|date|after:valid_from',
            'applicable_to' => 'required|in:all,packages,providers,categories',
            'applicable_ids' => 'nullable|array',
            'user_eligibility' => 'required|in:all,new,existing',
            'is_active' => 'boolean',
            'is_public' => 'boolean',
        ]);

        $promoCode->update($validated);

        return redirect()->route('admin.promo-codes.show', $promoCode)
            ->with('success', 'Promo code updated successfully!');
    }

    /**
     * Admin: Delete promo code
     */
    public function destroy(PromoCode $promoCode)
    {
        $promoCode->delete();

        return redirect()->route('admin.promo-codes.index')
            ->with('success', 'Promo code deleted successfully!');
    }

    /**
     * Admin: Toggle promo code active status
     */
    public function toggleActive(PromoCode $promoCode)
    {
        $promoCode->update(['is_active' => !$promoCode->is_active]);

        $status = $promoCode->is_active ? 'activated' : 'deactivated';

        return back()->with('success', "Promo code {$status} successfully!");
    }
}
