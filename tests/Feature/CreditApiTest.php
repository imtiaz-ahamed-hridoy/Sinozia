<?php

use App\Models\CreditLedger;
use App\Models\User;
use App\Models\UserCredit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

test('unauthenticated user cannot check credit balance', function () {
    $response = $this->getJson('/api/credits/balance');
    $response->assertStatus(401);
});

test('authenticated free user can view credit balance and plan attributes', function () {
    $user = User::factory()->create([
        'is_active' => true,
        'plan_type' => 'free',
        'is_premium' => false,
    ]);

    UserCredit::create([
        'user_id' => $user->id,
        'credits' => 15,
    ]);

    CreditLedger::create([
        'user_id' => $user->id,
        'amount' => 15,
        'type' => 'subscription_grant',
        'description' => 'Initial free grant',
    ]);

    Sanctum::actingAs($user);

    $response = $this->getJson('/api/credits/balance');
    $response->assertStatus(200)
        ->assertJson([
            'status' => true,
            'data' => [
                'balance' => 15,
                'plan_type' => 'free',
                'is_premium' => false,
                'can_generate' => false,
                'video_cost' => 0,
            ],
        ]);
});

test('authenticated pro user has can_generate enabled', function () {
    $user = User::factory()->create([
        'is_active' => true,
        'plan_type' => 'pro',
        'is_premium' => true,
    ]);

    UserCredit::create([
        'user_id' => $user->id,
        'credits' => 100,
    ]);

    Sanctum::actingAs($user);

    $response = $this->getJson('/api/credits/balance');
    $response->assertStatus(200)
        ->assertJson([
            'status' => true,
            'data' => [
                'balance' => 100,
                'plan_type' => 'pro',
                'is_premium' => true,
                'can_generate' => true,
            ],
        ]);
});

test('authenticated user can view paginated credit history', function () {
    $user = User::factory()->create(['is_active' => true]);

    CreditLedger::create([
        'user_id' => $user->id,
        'amount' => 50,
        'type' => 'pack_purchase',
        'description' => 'Stripe 50-credit pack purchase',
    ]);

    CreditLedger::create([
        'user_id' => $user->id,
        'amount' => -10,
        'type' => 'video_generation',
        'description' => 'Processed video project #1',
    ]);

    Sanctum::actingAs($user);

    $response = $this->getJson('/api/credits/history');
    $response->assertStatus(200)
        ->assertJson([
            'status' => true,
            'message' => 'Credit history retrieved successfully.',
        ])
        ->assertJsonStructure([
            'data' => [
                'data' => [
                    '*' => ['id', 'user_id', 'amount', 'type', 'description'],
                ],
                'current_page',
                'total',
            ],
        ]);

    expect($response->json('data.total'))->toBe(2);
});
