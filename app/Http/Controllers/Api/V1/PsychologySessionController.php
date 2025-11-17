<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\BookPsychologySessionRequest;
use App\Http\Resources\Api\V1\PsychologySessionResource;
use App\Models\Psychologist;
use App\Models\PsychologySession;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;

class PsychologySessionController extends Controller
{
    /**
     * Display a listing of user's sessions
     *
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $request->validate([
            'status' => 'nullable|in:scheduled,confirmed,in_progress,completed,cancelled',
            'per_page' => 'nullable|integer|min:1|max:100',
        ]);

        $sessions = PsychologySession::query()
            ->where('user_id', $request->user()->id)
            ->when($request->status, function ($q, $status) {
                $q->where('status', $status);
            })
            ->with(['psychologist'])
            ->orderByDesc('scheduled_at')
            ->paginate($request->per_page ?? 15);

        return PsychologySessionResource::collection($sessions);
    }

    /**
     * Book a new psychology session
     *
     * @param BookPsychologySessionRequest $request
     * @return PsychologySessionResource
     */
    public function store(BookPsychologySessionRequest $request): PsychologySessionResource
    {
        $validated = $request->validated();

        // Get psychologist
        $psychologist = Psychologist::findOrFail($validated['psychologist_id']);

        // Verify psychologist offers this session type
        if (!$psychologist->offersSessionType($validated['session_type'])) {
            abort(422, "Psychologist does not offer {$validated['session_type']} sessions");
        }

        // Check availability
        $scheduledAt = new \DateTime($validated['scheduled_at']);
        $existingSession = $psychologist->sessions()
            ->where('scheduled_at', $scheduledAt)
            ->whereIn('status', ['scheduled', 'confirmed', 'in_progress'])
            ->exists();

        if ($existingSession) {
            abort(422, 'This time slot is already booked');
        }

        // Calculate price
        $price = $psychologist->getPriceForSessionType($validated['session_type']);

        // Create session
        $session = DB::transaction(function () use ($request, $validated, $psychologist, $price, $scheduledAt) {
            $session = PsychologySession::create([
                'tenant_id' => app()->bound('tenant') ? app('tenant')->id : $psychologist->tenant_id,
                'psychologist_id' => $psychologist->id,
                'user_id' => $request->user()->id,
                'session_number' => PsychologySession::generateSessionNumber(),
                'session_type' => $validated['session_type'],
                'category' => $validated['is_emergency'] ?? false ? 'emergency' : 'first_session',
                'scheduled_at' => $scheduledAt,
                'duration_minutes' => $psychologist->session_duration_minutes ?? 60,
                'client_concern' => $validated['client_concern'] ?? null,
                'is_emergency' => $validated['is_emergency'] ?? false,
                'urgency_level' => $validated['is_emergency'] ?? false ? 'emergency' : 'normal',
                'status' => 'scheduled',
                'price' => $price,
                'payment_method' => 'pay_per_session',
                'is_paid' => false,
            ]);

            // TODO: Send booking confirmation email/notification
            // TODO: Create payment invoice

            return $session;
        });

        return new PsychologySessionResource($session->load('psychologist'));
    }

    /**
     * Display the specified session
     *
     * @param PsychologySession $session
     * @return PsychologySessionResource
     */
    public function show(PsychologySession $session): PsychologySessionResource
    {
        // Check authorization
        if ($session->user_id !== auth()->id()) {
            abort(403, 'Unauthorized to view this session');
        }

        return new PsychologySessionResource($session->load('psychologist'));
    }

    /**
     * Cancel a session
     *
     * @param PsychologySession $session
     * @param Request $request
     * @return PsychologySessionResource
     */
    public function cancel(PsychologySession $session, Request $request): PsychologySessionResource
    {
        // Check authorization
        if ($session->user_id !== auth()->id()) {
            abort(403, 'Unauthorized to cancel this session');
        }

        // Check if session can be cancelled
        if (!in_array($session->status, ['scheduled', 'confirmed'])) {
            abort(422, 'Only scheduled or confirmed sessions can be cancelled');
        }

        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $session->cancel($request->reason, $request->user(), 'client');

        // TODO: Send cancellation notification
        // TODO: Process refund if applicable

        return new PsychologySessionResource($session->load('psychologist'));
    }

    /**
     * Rate a completed session
     *
     * @param PsychologySession $session
     * @param Request $request
     * @return PsychologySessionResource
     */
    public function rate(PsychologySession $session, Request $request): PsychologySessionResource
    {
        // Check authorization
        if ($session->user_id !== auth()->id()) {
            abort(403, 'Unauthorized to rate this session');
        }

        // Check if session is completed
        if ($session->status !== 'completed') {
            abort(422, 'Only completed sessions can be rated');
        }

        // Check if already rated
        if ($session->client_rating !== null) {
            abort(422, 'Session has already been rated');
        }

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'feedback' => 'nullable|string|max:1000',
        ]);

        $session->update([
            'client_rating' => $validated['rating'],
            'client_feedback' => $validated['feedback'] ?? null,
        ]);

        // Update psychologist statistics
        $session->psychologist->updateStatistics();

        return new PsychologySessionResource($session->load('psychologist'));
    }
}
