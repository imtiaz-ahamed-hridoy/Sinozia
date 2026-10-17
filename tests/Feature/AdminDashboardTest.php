<?php

use App\Models\Faq;
use App\Models\TargetCharacter;
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
