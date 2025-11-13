<?php

namespace App\Http\Controllers;

use App\Models\McuBooking;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    /**
     * Display reviews for a package
     */
    public function index(Request $request)
    {
        $packageId = $request->get('package_id');
        $providerId = $request->get('provider_id');
        $rating = $request->get('rating');

        $query = Review::with(['user', 'booking', 'package', 'provider'])
            ->approved()
            ->latest();

        if ($packageId) {
            $query->where('package_id', $packageId);
        }

        if ($providerId) {
            $query->where('provider_id', $providerId);
        }

        if ($rating) {
            $query->byRating($rating);
        }

        $reviews = $query->paginate(20);

        // Calculate statistics
        $stats = [
            'total' => Review::approved()->when($packageId, fn($q) => $q->where('package_id', $packageId))->count(),
            'average_rating' => Review::approved()->when($packageId, fn($q) => $q->where('package_id', $packageId))->avg('rating'),
            'rating_distribution' => $this->getRatingDistribution($packageId, $providerId),
        ];

        return view('reviews.index', compact('reviews', 'stats'));
    }

    /**
     * Show form to create a review
     */
    public function create(McuBooking $booking)
    {
        // Check if booking belongs to current user/tenant
        if ($booking->tenant_id !== app('tenant')->id) {
            abort(403, 'Unauthorized');
        }

        // Check if booking is completed
        if ($booking->status !== 'completed') {
            return redirect()->back()->with('error', 'You can only review completed bookings.');
        }

        // Check if review already exists
        if ($booking->review) {
            return redirect()->route('reviews.edit', $booking->review)
                ->with('info', 'You have already reviewed this booking.');
        }

        return view('reviews.create', compact('booking'));
    }

    /**
     * Store a newly created review
     */
    public function store(Request $request, McuBooking $booking)
    {
        // Check if booking belongs to current user/tenant
        if ($booking->tenant_id !== app('tenant')->id) {
            abort(403, 'Unauthorized');
        }

        // Check if booking is completed
        if ($booking->status !== 'completed') {
            return redirect()->back()->with('error', 'You can only review completed bookings.');
        }

        // Check if review already exists
        if ($booking->review) {
            return redirect()->route('reviews.show', $booking->review)
                ->with('error', 'You have already reviewed this booking.');
        }

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'title' => 'nullable|string|max:255',
            'comment' => 'nullable|string|max:2000',
            'service_rating' => 'nullable|integer|min:1|max:5',
            'cleanliness_rating' => 'nullable|integer|min:1|max:5',
            'staff_rating' => 'nullable|integer|min:1|max:5',
            'value_rating' => 'nullable|integer|min:1|max:5',
            'is_anonymous' => 'boolean',
        ]);

        $review = Review::create([
            'tenant_id' => app('tenant')->id,
            'booking_id' => $booking->id,
            'package_id' => $booking->package_id,
            'provider_id' => $booking->provider_id,
            'user_id' => Auth::id(),
            'rating' => $validated['rating'],
            'title' => $validated['title'] ?? null,
            'comment' => $validated['comment'] ?? null,
            'service_rating' => $validated['service_rating'] ?? null,
            'cleanliness_rating' => $validated['cleanliness_rating'] ?? null,
            'staff_rating' => $validated['staff_rating'] ?? null,
            'value_rating' => $validated['value_rating'] ?? null,
            'is_anonymous' => $validated['is_anonymous'] ?? false,
            'is_verified' => true,
            'status' => 'approved', // Auto-approve verified bookings
        ]);

        // Update package and provider ratings
        $this->updateRatings($booking->package_id, $booking->provider_id);

        return redirect()->route('mcu.bookings.show', $booking)
            ->with('success', 'Thank you for your review!');
    }

    /**
     * Display the specified review
     */
    public function show(Review $review)
    {
        $review->load(['user', 'booking', 'package', 'provider', 'votes']);

        return view('reviews.show', compact('review'));
    }

    /**
     * Show the form for editing the review
     */
    public function edit(Review $review)
    {
        // Check ownership
        if ($review->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        return view('reviews.edit', compact('review'));
    }

    /**
     * Update the specified review
     */
    public function update(Request $request, Review $review)
    {
        // Check ownership
        if ($review->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'title' => 'nullable|string|max:255',
            'comment' => 'nullable|string|max:2000',
            'service_rating' => 'nullable|integer|min:1|max:5',
            'cleanliness_rating' => 'nullable|integer|min:1|max:5',
            'staff_rating' => 'nullable|integer|min:1|max:5',
            'value_rating' => 'nullable|integer|min:1|max:5',
            'is_anonymous' => 'boolean',
        ]);

        $review->update([
            'rating' => $validated['rating'],
            'title' => $validated['title'] ?? null,
            'comment' => $validated['comment'] ?? null,
            'service_rating' => $validated['service_rating'] ?? null,
            'cleanliness_rating' => $validated['cleanliness_rating'] ?? null,
            'staff_rating' => $validated['staff_rating'] ?? null,
            'value_rating' => $validated['value_rating'] ?? null,
            'is_anonymous' => $validated['is_anonymous'] ?? false,
        ]);

        // Update package and provider ratings
        $this->updateRatings($review->package_id, $review->provider_id);

        return redirect()->route('reviews.show', $review)
            ->with('success', 'Review updated successfully!');
    }

    /**
     * Remove the specified review
     */
    public function destroy(Review $review)
    {
        // Check ownership
        if ($review->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $packageId = $review->package_id;
        $providerId = $review->provider_id;

        $review->delete();

        // Update package and provider ratings
        $this->updateRatings($packageId, $providerId);

        return redirect()->route('mcu.bookings.index')
            ->with('success', 'Review deleted successfully.');
    }

    /**
     * Mark review as helpful
     */
    public function markHelpful(Review $review)
    {
        $review->markHelpful(Auth::user());

        return back()->with('success', 'Thank you for your feedback!');
    }

    /**
     * Mark review as not helpful
     */
    public function markNotHelpful(Review $review)
    {
        $review->markNotHelpful(Auth::user());

        return back()->with('success', 'Thank you for your feedback!');
    }

    /**
     * Add provider response
     */
    public function addResponse(Request $request, Review $review)
    {
        // Check if user is provider admin
        // (This should be handled by middleware or policy)

        $validated = $request->validate([
            'response' => 'required|string|max:1000',
        ]);

        $review->addProviderResponse($validated['response']);

        return back()->with('success', 'Response added successfully!');
    }

    /**
     * Get rating distribution for package or provider
     */
    private function getRatingDistribution($packageId = null, $providerId = null): array
    {
        $query = Review::approved();

        if ($packageId) {
            $query->where('package_id', $packageId);
        } elseif ($providerId) {
            $query->where('provider_id', $providerId);
        }

        $distribution = [];
        for ($i = 5; $i >= 1; $i--) {
            $distribution[$i] = $query->clone()->where('rating', $i)->count();
        }

        return $distribution;
    }

    /**
     * Update package and provider average ratings
     */
    private function updateRatings($packageId, $providerId): void
    {
        // Update package rating
        $package = \App\Models\McuPackage::find($packageId);
        if ($package) {
            $avgRating = Review::approved()
                ->where('package_id', $packageId)
                ->avg('rating');
            $totalReviews = Review::approved()
                ->where('package_id', $packageId)
                ->count();

            $package->update([
                'rating' => round($avgRating, 1),
                'total_reviews' => $totalReviews,
            ]);
        }

        // Update provider rating
        $provider = \App\Models\McuProvider::find($providerId);
        if ($provider) {
            $avgRating = Review::approved()
                ->where('provider_id', $providerId)
                ->avg('rating');
            $totalReviews = Review::approved()
                ->where('provider_id', $providerId)
                ->count();

            $provider->update([
                'rating' => round($avgRating, 1),
                'total_reviews' => $totalReviews,
            ]);
        }
    }
}
