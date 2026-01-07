<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PsychologySessionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'session_number' => $this->session_number,

            // Psychologist info
            'psychologist' => new PsychologistResource($this->whenLoaded('psychologist')),

            // Session details
            'session_type' => $this->session_type,
            'category' => $this->category,
            'status' => $this->status,

            // Scheduling
            'scheduled_at' => $this->scheduled_at?->toISOString(),
            'started_at' => $this->started_at?->toISOString(),
            'ended_at' => $this->ended_at?->toISOString(),
            'duration_minutes' => $this->duration_minutes,
            'actual_duration_minutes' => $this->actual_duration_minutes,

            // Client concern
            'client_concern' => $this->client_concern,
            'urgency_level' => $this->urgency_level,
            'is_emergency' => $this->is_emergency,
            'is_anonymous' => $this->is_anonymous,

            // Video call (only show if user is participant)
            'join_url' => $this->when(
                $this->canViewJoinUrl($request->user()),
                $this->join_url
            ),

            // Payment
            'price' => (float) $this->price,
            'payment_method' => $this->payment_method,
            'is_paid' => $this->is_paid,

            // Rating
            'client_rating' => $this->client_rating,
            'client_feedback' => $this->client_feedback,

            // Cancellation
            'cancellation_reason' => $this->when($this->status === 'cancelled', $this->cancellation_reason),
            'cancelled_at' => $this->when($this->status === 'cancelled', $this->cancelled_at?->toISOString()),

            // Timestamps
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }

    /**
     * Check if user can view join URL
     */
    private function canViewJoinUrl($user): bool
    {
        if (!$user) {
            return false;
        }

        // Only participants can see join URL
        return $this->user_id === $user->id || $this->psychologist_id === $user->id;
    }
}
