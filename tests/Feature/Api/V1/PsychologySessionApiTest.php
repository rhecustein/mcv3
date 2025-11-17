<?php

use App\Models\Psychologist;
use App\Models\PsychologySession;
use App\Models\Tenant;
use App\Models\User;

beforeEach(function () {
    $this->tenant = Tenant::factory()->create();
    $this->user = User::factory()->create(['tenant_id' => $this->tenant->id]);
});

test('authenticated user can list their psychology sessions', function () {
    // Create sessions for this user
    PsychologySession::factory()->count(3)->create(['user_id' => $this->user->id]);

    // Create sessions for other users (should not be returned)
    PsychologySession::factory()->count(2)->create();

    $response = $this->actingAs($this->user, 'sanctum')
        ->getJson('/api/v1/psychology/sessions');

    $response->assertOk()
        ->assertJsonCount(3, 'data');
});

test('guest cannot access psychology sessions', function () {
    $response = $this->getJson('/api/v1/psychology/sessions');

    $response->assertUnauthorized();
});

test('user can book a psychology session with valid data', function () {
    $psychologist = Psychologist::factory()->create([
        'tenant_id' => $this->tenant->id,
        'offers_video' => true,
        'price_video' => 250000,
    ]);

    $response = $this->actingAs($this->user, 'sanctum')
        ->postJson('/api/v1/psychology/sessions', [
            'psychologist_id' => $psychologist->id,
            'session_type' => 'video',
            'scheduled_at' => now()->addDays(3)->toISOString(),
            'client_concern' => 'Stress management',
        ]);

    $response->assertCreated()
        ->assertJsonStructure([
            'data' => [
                'id',
                'session_number',
                'session_type',
                'scheduled_at',
                'price',
                'status',
            ],
        ]);

    $this->assertDatabaseHas('psychology_sessions', [
        'user_id' => $this->user->id,
        'psychologist_id' => $psychologist->id,
        'session_type' => 'video',
        'status' => 'scheduled',
    ]);
});

test('booking fails when psychologist does not offer session type', function () {
    $psychologist = Psychologist::factory()->create([
        'tenant_id' => $this->tenant->id,
        'offers_video' => true,
        'offers_onsite' => false,
    ]);

    $response = $this->actingAs($this->user, 'sanctum')
        ->postJson('/api/v1/psychology/sessions', [
            'psychologist_id' => $psychologist->id,
            'session_type' => 'onsite',
            'scheduled_at' => now()->addDays(3)->toISOString(),
        ]);

    $response->assertUnprocessable();
});

test('booking fails when time slot is already taken', function () {
    $psychologist = Psychologist::factory()->create([
        'tenant_id' => $this->tenant->id,
    ]);

    $scheduledTime = now()->addDays(3);

    // Create existing booking
    PsychologySession::factory()->create([
        'psychologist_id' => $psychologist->id,
        'scheduled_at' => $scheduledTime,
        'status' => 'scheduled',
    ]);

    // Try to book same time
    $response = $this->actingAs($this->user, 'sanctum')
        ->postJson('/api/v1/psychology/sessions', [
            'psychologist_id' => $psychologist->id,
            'session_type' => 'video',
            'scheduled_at' => $scheduledTime->toISOString(),
        ]);

    $response->assertUnprocessable();
});

test('user can view their own session', function () {
    $session = PsychologySession::factory()->create([
        'user_id' => $this->user->id,
    ]);

    $response = $this->actingAs($this->user, 'sanctum')
        ->getJson("/api/v1/psychology/sessions/{$session->id}");

    $response->assertOk()
        ->assertJson([
            'data' => [
                'id' => $session->id,
                'session_number' => $session->session_number,
            ],
        ]);
});

test('user cannot view other users session', function () {
    $otherUser = User::factory()->create();
    $session = PsychologySession::factory()->create([
        'user_id' => $otherUser->id,
    ]);

    $response = $this->actingAs($this->user, 'sanctum')
        ->getJson("/api/v1/psychology/sessions/{$session->id}");

    $response->assertForbidden();
});

test('user can cancel their scheduled session', function () {
    $session = PsychologySession::factory()->create([
        'user_id' => $this->user->id,
        'status' => 'scheduled',
    ]);

    $response = $this->actingAs($this->user, 'sanctum')
        ->postJson("/api/v1/psychology/sessions/{$session->id}/cancel", [
            'reason' => 'Personal emergency',
        ]);

    $response->assertOk();

    $this->assertDatabaseHas('psychology_sessions', [
        'id' => $session->id,
        'status' => 'cancelled',
        'cancellation_reason' => 'Personal emergency',
    ]);
});

test('user cannot cancel completed session', function () {
    $session = PsychologySession::factory()->completed()->create([
        'user_id' => $this->user->id,
    ]);

    $response = $this->actingAs($this->user, 'sanctum')
        ->postJson("/api/v1/psychology/sessions/{$session->id}/cancel", [
            'reason' => 'Test',
        ]);

    $response->assertUnprocessable();
});

test('user can rate completed session', function () {
    $session = PsychologySession::factory()->completed()->create([
        'user_id' => $this->user->id,
    ]);

    $response = $this->actingAs($this->user, 'sanctum')
        ->postJson("/api/v1/psychology/sessions/{$session->id}/rate", [
            'rating' => 5,
            'feedback' => 'Excellent session!',
        ]);

    $response->assertOk();

    $this->assertDatabaseHas('psychology_sessions', [
        'id' => $session->id,
        'client_rating' => 5,
        'client_feedback' => 'Excellent session!',
    ]);
});

test('user cannot rate non-completed session', function () {
    $session = PsychologySession::factory()->create([
        'user_id' => $this->user->id,
        'status' => 'scheduled',
    ]);

    $response = $this->actingAs($this->user, 'sanctum')
        ->postJson("/api/v1/psychology/sessions/{$session->id}/rate", [
            'rating' => 5,
            'feedback' => 'Great!',
        ]);

    $response->assertUnprocessable();
});

test('booking validation fails with invalid data', function () {
    $response = $this->actingAs($this->user, 'sanctum')
        ->postJson('/api/v1/psychology/sessions', [
            'psychologist_id' => 999999,
            'session_type' => 'invalid',
            'scheduled_at' => 'invalid-date',
        ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['psychologist_id', 'session_type', 'scheduled_at']);
});
