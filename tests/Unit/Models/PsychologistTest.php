<?php

use App\Models\Psychologist;
use App\Models\PsychologySession;

test('psychologist can calculate completion rate correctly', function () {
    $psychologist = Psychologist::factory()->create([
        'total_sessions' => 100,
        'completed_sessions' => 85,
    ]);

    expect($psychologist->completion_rate)->toBe(85.0);
});

test('psychologist returns zero completion rate when no sessions', function () {
    $psychologist = Psychologist::factory()->create([
        'total_sessions' => 0,
        'completed_sessions' => 0,
    ]);

    expect($psychologist->completion_rate)->toBe(0.0);
});

test('psychologist can check if STR is expired', function () {
    $psychologist = Psychologist::factory()->create([
        'str_valid_until' => now()->subDays(1),
    ]);

    expect($psychologist->isStrExpired())->toBeTrue();
});

test('psychologist can check if STR is not expired', function () {
    $psychologist = Psychologist::factory()->create([
        'str_valid_until' => now()->addDays(30),
    ]);

    expect($psychologist->isStrExpired())->toBeFalse();
});

test('psychologist can get price for video session type', function () {
    $psychologist = Psychologist::factory()->create([
        'price_per_session' => 200000,
        'price_video' => 250000,
    ]);

    expect($psychologist->getPriceForSessionType('video'))->toBe(250000.0);
});

test('psychologist falls back to base price when specific price not set', function () {
    $psychologist = Psychologist::factory()->create([
        'price_per_session' => 200000,
        'price_audio' => null,
    ]);

    expect($psychologist->getPriceForSessionType('audio'))->toBe(200000.0);
});

test('psychologist can check if available on specific day', function () {
    $psychologist = Psychologist::factory()->create([
        'available_days' => ['monday', 'wednesday', 'friday'],
    ]);

    expect($psychologist->isAvailableOn('monday'))->toBeTrue();
    expect($psychologist->isAvailableOn('tuesday'))->toBeFalse();
});

test('psychologist can check if offers session type', function () {
    $psychologist = Psychologist::factory()->create([
        'offers_video' => true,
        'offers_audio' => true,
        'offers_chat' => false,
    ]);

    expect($psychologist->offersSessionType('video'))->toBeTrue();
    expect($psychologist->offersSessionType('audio'))->toBeTrue();
    expect($psychologist->offersSessionType('chat'))->toBeFalse();
});

test('psychologist can add earnings correctly', function () {
    $psychologist = Psychologist::factory()->create([
        'commission_percentage' => 30,
        'total_earnings' => 0,
        'pending_payout' => 0,
    ]);

    $sessionPrice = 200000;
    $psychologist->addEarnings($sessionPrice);

    // Platform takes 30%, psychologist gets 70%
    $expectedEarning = 200000 * 0.70; // 140000

    expect($psychologist->fresh()->total_earnings)->toBe($expectedEarning);
    expect($psychologist->fresh()->pending_payout)->toBe($expectedEarning);
});

test('psychologist scope returns only verified psychologists', function () {
    Psychologist::factory()->create(['is_verified' => true]);
    Psychologist::factory()->create(['is_verified' => false]);

    $verified = Psychologist::verified()->get();

    expect($verified)->toHaveCount(1);
    expect($verified->first()->is_verified)->toBeTrue();
});

test('psychologist scope returns only available psychologists', function () {
    Psychologist::factory()->create([
        'is_available' => true,
        'is_active' => true,
        'is_verified' => true,
    ]);

    Psychologist::factory()->create([
        'is_available' => false,
        'is_active' => true,
        'is_verified' => true,
    ]);

    $available = Psychologist::available()->get();

    expect($available)->toHaveCount(1);
});

test('psychologist can filter by expertise', function () {
    Psychologist::factory()->create(['expertise' => ['anxiety', 'depression']]);
    Psychologist::factory()->create(['expertise' => ['burnout']]);

    $anxietyExperts = Psychologist::byExpertise('anxiety')->get();

    expect($anxietyExperts)->toHaveCount(1);
});
