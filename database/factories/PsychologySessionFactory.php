<?php

namespace Database\Factories;

use App\Models\Psychologist;
use App\Models\PsychologySession;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PsychologySessionFactory extends Factory
{
    protected $model = PsychologySession::class;

    public function definition(): array
    {
        $sessionType = fake()->randomElement(['video', 'audio', 'chat']);
        $psychologist = Psychologist::factory()->create();

        return [
            'tenant_id' => $psychologist->tenant_id,
            'psychologist_id' => $psychologist->id,
            'user_id' => User::factory(),
            'subscription_id' => null,

            'session_number' => PsychologySession::generateSessionNumber(),
            'session_type' => $sessionType,
            'category' => fake()->randomElement(['first_session', 'follow_up', 'consultation']),

            'scheduled_at' => now()->addDays(fake()->numberBetween(1, 30)),
            'started_at' => null,
            'ended_at' => null,
            'duration_minutes' => 60,
            'actual_duration_minutes' => null,

            'client_concern' => fake()->sentence(),
            'urgency_level' => 'normal',
            'is_anonymous' => false,
            'is_emergency' => false,

            'room_id' => null,
            'room_token' => null,
            'join_url' => null,
            'call_metadata' => null,

            'status' => 'scheduled',

            'price' => $psychologist->getPriceForSessionType($sessionType),
            'payment_method' => 'pay_per_session',
            'payment_id' => null,
            'is_paid' => false,

            'client_rating' => null,
            'client_feedback' => null,
            'psychologist_rating' => null,

            'cancellation_reason' => null,
            'cancelled_at' => null,
            'cancelled_by' => null,
            'cancelled_by_role' => null,

            'rescheduled_from_id' => null,
            'rescheduled_to_id' => null,

            'reminder_sent_24h' => false,
            'reminder_sent_1h' => false,
            'feedback_requested' => false,

            'metadata' => null,
        ];
    }

    /**
     * Indicate that the session is completed.
     */
    public function completed(): static
    {
        return $this->state(function (array $attributes) {
            $scheduledAt = now()->subDays(fake()->numberBetween(1, 30));

            return [
                'scheduled_at' => $scheduledAt,
                'started_at' => $scheduledAt,
                'ended_at' => $scheduledAt->copy()->addHour(),
                'actual_duration_minutes' => 60,
                'status' => 'completed',
                'is_paid' => true,
            ];
        });
    }

    /**
     * Indicate that the session is cancelled.
     */
    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'cancelled',
            'cancellation_reason' => fake()->sentence(),
            'cancelled_at' => now(),
            'cancelled_by_role' => fake()->randomElement(['client', 'psychologist']),
        ]);
    }

    /**
     * Indicate that the session is an emergency.
     */
    public function emergency(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_emergency' => true,
            'category' => 'emergency',
            'urgency_level' => 'emergency',
            'scheduled_at' => now()->addHours(fake()->numberBetween(1, 4)),
        ]);
    }

    /**
     * Indicate that the session is in progress.
     */
    public function inProgress(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'in_progress',
            'started_at' => now()->subMinutes(fake()->numberBetween(10, 50)),
            'scheduled_at' => now()->subMinutes(fake()->numberBetween(10, 50)),
        ]);
    }
}
