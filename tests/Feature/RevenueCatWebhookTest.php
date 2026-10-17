<?php

use App\Models\RevenueCatEvent;
use App\Models\User;
use Illuminate\Support\Facades\Config;

beforeEach(function () {
    Config::set('services.revenuecat.webhook_secret', 'test_webhook_secret_key');
});

test('unauthorized revenuecat webhook request is rejected', function () {
    $response = $this->postJson('/api/revenuecat/webhook', [
        'event' => [
            'id' => 'evt_123',
        ],
    ], [
        'Authorization' => 'Bearer wrong_secret',
    ]);

    $response->assertStatus(401);
});

test('valid revenuecat initial purchase webhook activates user premium status', function () {
    $user = User::factory()->create([
        'is_premium' => false,
        'plan_type' => 'free',
    ]);

    $payload = [
        'event' => [
            'id' => 'rc_evt_test_001',
            'app_id' => 'app_test_id',
            'type' => 'INITIAL_PURCHASE',
            'app_user_id' => (string) $user->id,
            'product_id' => 'sinozia_pro_monthly',
            'entitlement_id' => 'pro_access',
            'price' => 19.99,
            'currency' => 'EUR',
            'purchased_at_ms' => now()->timestamp * 1000,
            'expiration_at_ms' => now()->addMonth()->timestamp * 1000,
            'original_transaction_id' => 'tx_origin_1001',
            'environment' => 'SANDBOX',
        ],
    ];

    $response = $this->postJson('/api/revenuecat/webhook', $payload, [
        'Authorization' => 'Bearer test_webhook_secret_key',
    ]);

    $response->assertStatus(200)
        ->assertJsonPath('status', true);

    $user->refresh();
    expect($user->is_premium)->toBeTrue()
        ->and($user->plan_type)->toBe('pro');

    $this->assertDatabaseHas('revenue_cat_events', [
        'rc_event_id' => 'rc_evt_test_001',
    ]);

    $this->assertDatabaseHas('subscriptions', [
        'user_id' => $user->id,
        'original_transaction_id' => 'tx_origin_1001',
        'status' => 'active',
    ]);
});

test('duplicate revenuecat event is gracefully ignored', function () {
    RevenueCatEvent::create([
        'rc_event_id' => 'rc_evt_dup_001',
        'app_id' => 'app_test_id',
        'event_type' => 'INITIAL_PURCHASE',
        'payload' => ['test' => true],
    ]);

    $response = $this->postJson('/api/revenuecat/webhook', [
        'event' => [
            'id' => 'rc_evt_dup_001',
            'type' => 'INITIAL_PURCHASE',
        ],
    ], [
        'Authorization' => 'Bearer test_webhook_secret_key',
    ]);

    $response->assertStatus(200)
        ->assertJsonPath('status', true)
        ->assertJsonPath('message', 'Duplicate ignored');
});
