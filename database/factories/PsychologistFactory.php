<?php

namespace Database\Factories;

use App\Models\Psychologist;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PsychologistFactory extends Factory
{
    protected $model = Psychologist::class;

    public function definition(): array
    {
        return [
            'tenant_id' => Tenant::factory(),
            'user_id' => User::factory(),
            'name' => fake()->name(),
            'slug' => fake()->unique()->slug(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'bio' => fake()->paragraph(),
            'photo' => null,

            // Credentials
            'license_number' => 'SIPP-' . fake()->numerify('######'),
            'str_number' => 'STR-' . fake()->numerify('########'),
            'str_valid_until' => now()->addYears(2),
            'degree' => fake()->randomElement(['S.Psi', 'M.Psi', 'Ph.D']),
            'specialization' => fake()->randomElement(['Clinical Psychology', 'Industrial Psychology', 'Developmental Psychology']),
            'certifications' => ['CBT Certified', 'Trauma Therapy'],
            'years_of_experience' => fake()->numberBetween(2, 20),

            // Location
            'practice_address' => fake()->address(),
            'city' => fake()->city(),
            'province' => fake()->randomElement(['DKI Jakarta', 'Jawa Barat', 'Jawa Timur']),

            // Expertise
            'languages' => ['Indonesian', 'English'],
            'expertise' => fake()->randomElements(['anxiety', 'depression', 'burnout', 'trauma', 'relationship'], 3),
            'approaches' => fake()->randomElements(['CBT', 'Psychodynamic', 'Humanistic'], 2),

            // Availability
            'available_days' => ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'],
            'available_from' => '09:00',
            'available_until' => '17:00',

            // Services
            'accepts_emergency' => fake()->boolean(30),
            'offers_video' => true,
            'offers_audio' => true,
            'offers_chat' => fake()->boolean(70),
            'offers_onsite' => fake()->boolean(30),

            // Pricing
            'price_per_session' => fake()->numberBetween(150000, 500000),
            'price_video' => fake()->numberBetween(200000, 600000),
            'price_audio' => fake()->numberBetween(150000, 450000),
            'price_chat' => fake()->numberBetween(100000, 300000),
            'session_duration_minutes' => 60,

            // Commission
            'commission_percentage' => 30,
            'minimum_payout' => 500000,
            'bank_name' => fake()->randomElement(['BCA', 'Mandiri', 'BNI', 'BRI']),
            'bank_account_number' => fake()->numerify('##########'),
            'bank_account_name' => fake()->name(),

            // Statistics
            'total_sessions' => 0,
            'completed_sessions' => 0,
            'cancelled_sessions' => 0,
            'rating' => 0,
            'total_reviews' => 0,
            'total_earnings' => 0,
            'pending_payout' => 0,

            // Status
            'is_verified' => true,
            'is_active' => true,
            'is_available' => true,
            'is_featured' => false,
            'verified_at' => now(),
            'verified_by' => null,
        ];
    }

    /**
     * Indicate that the psychologist is not verified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_verified' => false,
            'verified_at' => null,
        ]);
    }

    /**
     * Indicate that the psychologist is featured.
     */
    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_featured' => true,
            'rating' => fake()->randomFloat(2, 4.5, 5.0),
            'total_reviews' => fake()->numberBetween(50, 200),
        ]);
    }

    /**
     * Indicate that the psychologist is unavailable.
     */
    public function unavailable(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_available' => false,
        ]);
    }
}
