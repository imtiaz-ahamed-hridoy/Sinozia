<?php

use App\Models\Credit;
use App\Models\Faq;
use App\Models\TargetCharacter;
use App\Models\TargetVoice;
use App\Models\Tutorial;
use App\Models\User;

test('guest or non-admin cannot access admin dashboard', function () {
    $guestResponse = $this->get('/admin/dashboard');
    $guestResponse->assertRedirect('/login');

    $regularUser = User::factory()->create(['role' => 'user']);
    $userResponse = $this->actingAs($regularUser)->get('/admin/dashboard');
    expect($userResponse->status())->toBe(401);
});

test('authenticated admin can access admin dashboard view', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
        'is_active' => true,
    ]);

    $response = $this->actingAs($admin)->get('/admin/dashboard');
    $response->assertStatus(200);
});

test('admin can fetch users datatable list and toggle user active status', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
        'is_active' => true,
    ]);

    $clientUser = User::factory()->create([
        'role' => 'user',
        'is_active' => true,
    ]);

    // DataTables AJAX
    $dtResponse = $this->actingAs($admin)
        ->get('/admin/users', ['X-Requested-With' => 'XMLHttpRequest']);

    $dtResponse->assertStatus(200);

    // Status toggle
    $statusResponse = $this->actingAs($admin)
        ->patchJson("/admin/users/{$clientUser->id}/status");

    $statusResponse->assertStatus(200)
        ->assertJsonPath('success', true);

    expect($clientUser->fresh()->is_active)->toBeFalsy();
});

test('admin can create and toggle target character status', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
        'is_active' => true,
    ]);

    $character = TargetCharacter::create([
        'title' => 'Cyber Samurai',
        'description' => 'A futuristic warrior character',
        'is_active' => true,
    ]);

    $response = $this->actingAs($admin)
        ->patchJson("/admin/target-characters/status/{$character->id}");

    $response->assertStatus(200)
        ->assertJsonPath('success', true);

    expect($character->fresh()->is_active)->toBeFalsy();
});

test('admin can create and toggle faq status', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
        'is_active' => true,
    ]);

    $faq = Faq::create([
        'question' => 'How does video character swap work?',
        'answer' => 'Our AI pipeline extracts frames and applies generative diffusion models.',
        'is_active' => true,
    ]);

    $response = $this->actingAs($admin)
        ->patchJson("/admin/faqs/status/{$faq->id}");

    $response->assertStatus(200)
        ->assertJsonPath('success', true);

    expect($faq->fresh()->is_active)->toBeFalsy();
});

test('admin can access settings management pages', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
        'is_active' => true,
    ]);

    $this->actingAs($admin)->get('/admin/system-setting')->assertStatus(200);
    $this->actingAs($admin)->get('/admin/profile')->assertStatus(200);
    $this->actingAs($admin)->get('/admin/mail-setting')->assertStatus(200);
    $this->actingAs($admin)->get('/admin/social-media')->assertStatus(200);
    $this->actingAs($admin)->get('/admin/dynamic-page')->assertStatus(200);
});

test('admin can toggle credit most popular flag', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
        'is_active' => true,
    ]);

    $credit = Credit::create([
        'credit' => 500,
        'amount' => 19.99,
        'most_popular' => false,
    ]);

    $response = $this->actingAs($admin)
        ->patchJson("/admin/credits/most-popular/{$credit->id}");

    $response->assertStatus(200)
        ->assertJsonPath('success', true);

    expect($credit->fresh()->most_popular)->toBeTruthy();
});

test('admin can toggle tutorial status', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
        'is_active' => true,
    ]);

    $tutorial = Tutorial::create([
        'title' => 'Getting Started with Video Swap',
        'description' => 'Guide to uploading videos and selecting characters',
        'is_active' => true,
    ]);

    $response = $this->actingAs($admin)
        ->patchJson("/admin/tutorials/status/{$tutorial->id}");

    $response->assertStatus(200)
        ->assertJsonPath('success', true);

    expect($tutorial->fresh()->is_active)->toBeFalsy();
});

test('admin can toggle target voice status', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
        'is_active' => true,
    ]);

    $voice = TargetVoice::create([
        'title' => 'Futuristic Narrator',
        'elevenlabs_voice_id' => '21m00Tcm4TlvDq8ikWAM',
        'is_active' => true,
    ]);

    $response = $this->actingAs($admin)
        ->patchJson("/admin/target-voices/status/{$voice->id}");

    $response->assertStatus(200)
        ->assertJsonPath('success', true);

    expect($voice->fresh()->is_active)->toBeFalsy();
});

test('admin can view generated characters index and datatable', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
        'is_active' => true,
    ]);

    $response = $this->actingAs($admin)->get('/admin/generated-characters');
    $response->assertStatus(200);

    $dtResponse = $this->actingAs($admin)->get('/admin/generated-characters', [
        'X-Requested-With' => 'XMLHttpRequest',
    ]);
    $dtResponse->assertStatus(200);
});
