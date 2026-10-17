<?php

use App\Models\GeneratedCharacter;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

test('unauthenticated users cannot access generated characters', function () {
    $response = $this->getJson('/api/generated-characters');
    $response->assertStatus(401);
});

test('authenticated user can list only their completed generated characters', function () {
    $user1 = User::factory()->create(['is_active' => true]);
    $user2 = User::factory()->create(['is_active' => true]);

    GeneratedCharacter::create([
        'user_id' => $user1->id,
        'title' => 'User 1 Character',
        'generated_image_path' => 'projects/characters/test1.png',
        'status' => 'completed',
    ]);

    GeneratedCharacter::create([
        'user_id' => $user2->id,
        'title' => 'User 2 Character',
        'generated_image_path' => 'projects/characters/test2.png',
        'status' => 'completed',
    ]);

    Sanctum::actingAs($user1);

    $response = $this->getJson('/api/generated-characters');
    $response->assertStatus(200);
    $response->assertJsonCount(1, 'data.data');
    $response->assertJsonFragment(['title' => 'User 1 Character']);
    $response->assertJsonMissing(['title' => 'User 2 Character']);
});

test('user cannot view a generated character belonging to another user', function () {
    $user1 = User::factory()->create(['is_active' => true]);
    $user2 = User::factory()->create(['is_active' => true]);

    $character = GeneratedCharacter::create([
        'user_id' => $user1->id,
        'title' => 'Private Character',
        'generated_image_path' => 'projects/characters/private.png',
        'status' => 'completed',
    ]);

    Sanctum::actingAs($user2);

    $response = $this->getJson("/api/generated-characters/{$character->id}");
    $response->assertStatus(403);
});

test('user can delete their own generated character', function () {
    $user = User::factory()->create(['is_active' => true]);

    $character = GeneratedCharacter::create([
        'user_id' => $user->id,
        'title' => 'To Delete',
        'generated_image_path' => 'projects/characters/delete_me.png',
        'status' => 'completed',
    ]);

    Sanctum::actingAs($user);

    $response = $this->deleteJson("/api/generated-characters/{$character->id}");
    $response->assertStatus(200);

    $this->assertDatabaseMissing('generated_characters', [
        'id' => $character->id,
    ]);
});
