<?php

use App\Models\PsychologySession;
use App\Models\Psychologist;
use App\Models\User;

test('session generates unique session number', function () {
    $number1 = PsychologySession::generateSessionNumber();
    $number2 = PsychologySession::generateSessionNumber();

    expect($number1)->toStartWith('PSY-');
    expect($number2)->toStartWith('PSY-');
    expect($number1)->not()->toBe($number2);
});

test('session can check if upcoming', function () {
    $upcomingSession = PsychologySession::factory()->create([
        'scheduled_at' => now()->addDays(3),
        'status' => 'scheduled',
    ]);

    $pastSession = PsychologySession::factory()->create([
        'scheduled_at' => now()->subDays(3),
        'status' => 'completed',
    ]);

    expect($upcomingSession->isUpcoming())->toBeTrue();
    expect($pastSession->isUpcoming())->toBeFalse();
});

test('session can check if in progress', function () {
    $inProgressSession = PsychologySession::factory()->create([
        'status' => 'in_progress',
    ]);

    $scheduledSession = PsychologySession::factory()->create([
        'status' => 'scheduled',
    ]);

    expect($inProgressSession->isInProgress())->toBeTrue();
    expect($scheduledSession->isInProgress())->toBeFalse();
});

test('session can check if completed', function () {
    $completedSession = PsychologySession::factory()->create([
        'status' => 'completed',
    ]);

    $scheduledSession = PsychologySession::factory()->create([
        'status' => 'scheduled',
    ]);

    expect($completedSession->isCompleted())->toBeTrue();
    expect($scheduledSession->isCompleted())->toBeFalse();
});

test('session can be started', function () {
    $session = PsychologySession::factory()->create([
        'status' => 'scheduled',
        'started_at' => null,
    ]);

    $session->start();

    expect($session->fresh()->status)->toBe('in_progress');
    expect($session->fresh()->started_at)->not()->toBeNull();
});

test('session calculates actual duration when completed', function () {
    $session = PsychologySession::factory()->create([
        'status' => 'in_progress',
        'started_at' => now()->subMinutes(65),
    ]);

    $session->complete();

    $fresh = $session->fresh();
    expect($fresh->status)->toBe('completed');
    expect($fresh->ended_at)->not()->toBeNull();
    expect($fresh->actual_duration_minutes)->toBeGreaterThanOrEqual(64);
    expect($fresh->actual_duration_minutes)->toBeLessThanOrEqual(66);
});

test('session can be cancelled with reason', function () {
    $user = User::factory()->create();
    $session = PsychologySession::factory()->create([
        'status' => 'scheduled',
    ]);

    $session->cancel('Client emergency', $user, 'client');

    $fresh = $session->fresh();
    expect($fresh->status)->toBe('cancelled');
    expect($fresh->cancellation_reason)->toBe('Client emergency');
    expect($fresh->cancelled_by)->toBe($user->id);
    expect($fresh->cancelled_by_role)->toBe('client');
    expect($fresh->cancelled_at)->not()->toBeNull();
});

test('session scope returns only upcoming sessions', function () {
    PsychologySession::factory()->create([
        'scheduled_at' => now()->addDays(2),
        'status' => 'scheduled',
    ]);

    PsychologySession::factory()->create([
        'scheduled_at' => now()->subDays(2),
        'status' => 'completed',
    ]);

    $upcoming = PsychologySession::upcoming()->get();

    expect($upcoming)->toHaveCount(1);
});

test('session scope returns only completed sessions', function () {
    PsychologySession::factory()->create(['status' => 'completed']);
    PsychologySession::factory()->create(['status' => 'completed']);
    PsychologySession::factory()->create(['status' => 'scheduled']);

    $completed = PsychologySession::completed()->get();

    expect($completed)->toHaveCount(2);
});

test('session scope returns only emergency sessions', function () {
    PsychologySession::factory()->create(['is_emergency' => true]);
    PsychologySession::factory()->create(['is_emergency' => false]);

    $emergency = PsychologySession::emergency()->get();

    expect($emergency)->toHaveCount(1);
    expect($emergency->first()->is_emergency)->toBeTrue();
});
