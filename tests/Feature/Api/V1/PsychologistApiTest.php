<?php

use App\Models\Psychologist;
use App\Models\PsychologySession;
use App\Models\Tenant;

beforeEach(function () {
    $this->tenant = Tenant::factory()->create();
});

test('guests can list psychologists', function () {
    Psychologist::factory()->count(5)->create([
        'tenant_id' => $this->tenant->id,
    ]);

    $response = $this->getJson('/api/v1/psychologists');

    $response->assertOk()
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'specialization',
                    'expertise',
                    'pricing',
                    'statistics',
                ],
            ],
            'links',
            'meta',
        ]);
});

test('psychologist list can be filtered by expertise', function () {
    Psychologist::factory()->create([
        'tenant_id' => $this->tenant->id,
        'expertise' => ['anxiety', 'depression'],
    ]);

    Psychologist::factory()->create([
        'tenant_id' => $this->tenant->id,
        'expertise' => ['burnout'],
    ]);

    $response = $this->getJson('/api/v1/psychologists?expertise=anxiety');

    $response->assertOk()
        ->assertJsonCount(1, 'data');
});

test('psychologist list can be filtered by city', function () {
    Psychologist::factory()->create([
        'tenant_id' => $this->tenant->id,
        'city' => 'Jakarta',
    ]);

    Psychologist::factory()->create([
        'tenant_id' => $this->tenant->id,
        'city' => 'Bandung',
    ]);

    $response = $this->getJson('/api/v1/psychologists?city=Jakarta');

    $response->assertOk()
        ->assertJsonCount(1, 'data');
});

test('psychologist list shows only verified and active psychologists by default', function () {
    // Create verified & active
    Psychologist::factory()->count(3)->create([
        'tenant_id' => $this->tenant->id,
        'is_verified' => true,
        'is_active' => true,
    ]);

    // Create unverified
    Psychologist::factory()->create([
        'tenant_id' => $this->tenant->id,
        'is_verified' => false,
        'is_active' => true,
    ]);

    // Create inactive
    Psychologist::factory()->create([
        'tenant_id' => $this->tenant->id,
        'is_verified' => true,
        'is_active' => false,
    ]);

    $response = $this->getJson('/api/v1/psychologists');

    $response->assertOk()
        ->assertJsonCount(3, 'data');
});

test('guests can view psychologist details', function () {
    $psychologist = Psychologist::factory()->create([
        'tenant_id' => $this->tenant->id,
    ]);

    $response = $this->getJson("/api/v1/psychologists/{$psychologist->id}");

    $response->assertOk()
        ->assertJson([
            'data' => [
                'id' => $psychologist->id,
                'name' => $psychologist->name,
                'email' => $psychologist->email,
            ],
        ]);
});

test('psychologist availability returns available slots', function () {
    $psychologist = Psychologist::factory()->create([
        'tenant_id' => $this->tenant->id,
        'available_days' => ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'],
        'available_from' => '09:00',
        'available_until' => '12:00',
        'session_duration_minutes' => 60,
    ]);

    // Get next Monday
    $nextMonday = now()->next('Monday');

    $response = $this->getJson("/api/v1/psychologists/{$psychologist->id}/availability?date={$nextMonday->format('Y-m-d')}");

    $response->assertOk()
        ->assertJsonStructure([
            'available',
            'date',
            'day',
            'available_slots' => [
                '*' => [
                    'time',
                    'available',
                ],
            ],
        ]);
});

test('psychologist availability shows unavailable when not working that day', function () {
    $psychologist = Psychologist::factory()->create([
        'tenant_id' => $this->tenant->id,
        'available_days' => ['monday', 'wednesday', 'friday'],
    ]);

    // Get next Tuesday (not available)
    $nextTuesday = now()->next('Tuesday');

    $response = $this->getJson("/api/v1/psychologists/{$psychologist->id}/availability?date={$nextTuesday->format('Y-m-d')}");

    $response->assertOk()
        ->assertJson([
            'available' => false,
        ]);
});

test('psychologist availability excludes booked slots', function () {
    $psychologist = Psychologist::factory()->create([
        'tenant_id' => $this->tenant->id,
        'available_days' => ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'],
        'available_from' => '09:00',
        'available_until' => '12:00',
        'session_duration_minutes' => 60,
    ]);

    $nextMonday = now()->next('Monday');
    $bookedTime = $nextMonday->copy()->setTime(10, 0);

    // Create booked session
    PsychologySession::factory()->create([
        'psychologist_id' => $psychologist->id,
        'scheduled_at' => $bookedTime,
        'status' => 'scheduled',
    ]);

    $response = $this->getJson("/api/v1/psychologists/{$psychologist->id}/availability?date={$nextMonday->format('Y-m-d')}");

    $response->assertOk();

    $slots = $response->json('available_slots');
    $bookedSlot = collect($slots)->firstWhere('time', '10:00');

    // The 10:00 slot should not be in available slots
    expect($bookedSlot)->toBeNull();
});

test('psychologist list pagination works', function () {
    Psychologist::factory()->count(25)->create([
        'tenant_id' => $this->tenant->id,
    ]);

    $response = $this->getJson('/api/v1/psychologists?per_page=10');

    $response->assertOk()
        ->assertJsonCount(10, 'data')
        ->assertJsonPath('meta.total', 25);
});
