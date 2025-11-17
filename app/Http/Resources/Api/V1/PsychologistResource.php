<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PsychologistResource extends JsonResource
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
            'name' => $this->name,
            'slug' => $this->slug,
            'email' => $this->email,
            'phone' => $this->phone,
            'bio' => $this->bio,
            'photo' => $this->photo ? url($this->photo) : null,

            // Credentials
            'license_number' => $this->license_number,
            'str_number' => $this->str_number,
            'str_valid_until' => $this->str_valid_until?->format('Y-m-d'),
            'degree' => $this->degree,
            'specialization' => $this->specialization,
            'years_of_experience' => $this->years_of_experience,

            // Location
            'city' => $this->city,
            'province' => $this->province,

            // Expertise
            'expertise' => $this->expertise,
            'approaches' => $this->approaches,
            'languages' => $this->languages,

            // Availability
            'available_days' => $this->available_days,
            'available_from' => $this->available_from,
            'available_until' => $this->available_until,

            // Services Offered
            'accepts_emergency' => $this->accepts_emergency,
            'offers_video' => $this->offers_video,
            'offers_audio' => $this->offers_audio,
            'offers_chat' => $this->offers_chat,
            'offers_onsite' => $this->offers_onsite,

            // Pricing
            'pricing' => [
                'per_session' => (float) $this->price_per_session,
                'video' => (float) $this->price_video,
                'audio' => (float) $this->price_audio,
                'chat' => (float) $this->price_chat,
            ],
            'session_duration_minutes' => $this->session_duration_minutes,

            // Statistics
            'statistics' => [
                'total_sessions' => $this->total_sessions,
                'completed_sessions' => $this->completed_sessions,
                'completion_rate' => round($this->completion_rate, 2),
                'rating' => (float) $this->rating,
                'total_reviews' => $this->total_reviews,
            ],

            // Status
            'is_verified' => $this->is_verified,
            'is_active' => $this->is_active,
            'is_available' => $this->is_available,
            'is_featured' => $this->is_featured,

            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
