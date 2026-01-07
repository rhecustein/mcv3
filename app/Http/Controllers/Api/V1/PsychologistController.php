<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\PsychologistResource;
use App\Models\Psychologist;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PsychologistController extends Controller
{
    /**
     * Display a listing of psychologists
     *
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $request->validate([
            'expertise' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'specialization' => 'nullable|string|max:100',
            'accepts_emergency' => 'nullable|boolean',
            'per_page' => 'nullable|integer|min:1|max:100',
        ]);

        $psychologists = Psychologist::query()
            ->when(app()->bound('tenant'), function ($q) {
                $q->where('tenant_id', app('tenant')->id);
            })
            ->verified()
            ->active()
            ->when($request->expertise, function ($q, $expertise) {
                $q->byExpertise($expertise);
            })
            ->when($request->city, function ($q, $city) {
                $q->byCity($city);
            })
            ->when($request->specialization, function ($q, $specialization) {
                $q->bySpecialization($specialization);
            })
            ->when($request->accepts_emergency, function ($q) {
                $q->acceptsEmergency();
            })
            ->with('reviews')
            ->orderByDesc('is_featured')
            ->orderByDesc('rating')
            ->paginate($request->per_page ?? 15);

        return PsychologistResource::collection($psychologists);
    }

    /**
     * Display the specified psychologist
     *
     * @param Psychologist $psychologist
     * @return PsychologistResource
     */
    public function show(Psychologist $psychologist): PsychologistResource
    {
        $psychologist->load('reviews');

        return new PsychologistResource($psychologist);
    }

    /**
     * Get psychologist availability
     *
     * @param Psychologist $psychologist
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function availability(Psychologist $psychologist, Request $request)
    {
        $request->validate([
            'date' => 'required|date|after_or_equal:today',
        ]);

        $date = new \DateTime($request->date);
        $dayOfWeek = strtolower($date->format('l')); // monday, tuesday, etc.

        // Check if psychologist is available on this day
        $isAvailableOnDay = $psychologist->isAvailableOn($dayOfWeek);

        if (!$isAvailableOnDay) {
            return response()->json([
                'available' => false,
                'message' => 'Psychologist is not available on this day',
            ]);
        }

        // Get booked slots for this date
        $bookedSlots = $psychologist->sessions()
            ->whereDate('scheduled_at', $date->format('Y-m-d'))
            ->whereIn('status', ['scheduled', 'confirmed', 'in_progress'])
            ->pluck('scheduled_at')
            ->map(fn($dt) => $dt->format('H:i'))
            ->toArray();

        // Generate available slots
        $availableSlots = $this->generateTimeSlots(
            $psychologist->available_from,
            $psychologist->available_until,
            $psychologist->session_duration_minutes ?? 60,
            $bookedSlots
        );

        return response()->json([
            'available' => true,
            'date' => $date->format('Y-m-d'),
            'day' => $dayOfWeek,
            'available_from' => $psychologist->available_from,
            'available_until' => $psychologist->available_until,
            'session_duration' => $psychologist->session_duration_minutes,
            'available_slots' => $availableSlots,
        ]);
    }

    /**
     * Generate time slots
     */
    private function generateTimeSlots(string $from, string $until, int $duration, array $booked): array
    {
        $slots = [];
        $current = new \DateTime($from);
        $end = new \DateTime($until);
        $interval = new \DateInterval("PT{$duration}M");

        while ($current < $end) {
            $slotTime = $current->format('H:i');

            if (!in_array($slotTime, $booked)) {
                $slots[] = [
                    'time' => $slotTime,
                    'available' => true,
                ];
            }

            $current->add($interval);
        }

        return $slots;
    }
}
