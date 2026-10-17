<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

test('unauthenticated user cannot access user profile api', function () {
    $response = $this->getJson('/api/user/profile');
    $response->assertStatus(401);
});

test('authenticated user can fetch their profile', function () {
    $user = User::factory()->create([
        'name' => 'Alex Mercer',
        'email' => 'alex@example.com',
        'is_active' => true,
    ]);

    Sanctum::actingAs($user);

    $response = $this->getJson('/api/user/profile');
    $response->assertStatus(200)
        ->assertJson([
            'status' => true,
            'data' => [
                'name' => 'Alex Mercer',
                'email' => 'alex@example.com',
            ],
        ]);
});

test('authenticated user can change password with correct current password', function () {
    $user = User::factory()->create([
        'password' => Hash::make('OldPassword123!'),
        'is_active' => true,
    ]);

    Sanctum::actingAs($user);

    $response = $this->postJson('/api/user/change-password', [
        'current_password' => 'OldPassword123!',
        'password' => 'NewPassword456!',
        'password_confirmation' => 'NewPassword456!',
    ]);

    $response->assertStatus(200);

    expect(Hash::check('NewPassword456!', $user->fresh()->password))->toBeTrue();
});

test('password change fails when current password is wrong', function () {
    $user = User::factory()->create([
        'password' => Hash::make('CorrectPassword123!'),
        'is_active' => true,
    ]);

    Sanctum::actingAs($user);

    $response = $this->postJson('/api/user/change-password', [
        'current_password' => 'WrongPassword123!',
        'password' => 'NewPassword456!',
        'password_confirmation' => 'NewPassword456!',
    ]);

    $response->assertStatus(400);
});
