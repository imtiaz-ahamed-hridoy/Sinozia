<?php

use App\Models\TargetVoice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

test('unauthenticated users cannot access target voices', function () {
    $response = $this->getJson('/api/target-voices');
    $response->assertStatus(401);
});

test('user can list global and personal target voices', function () {
    $user = User::factory()->create(['is_active' => true]);

    TargetVoice::create([
        'user_id' => null,
        'title' => 'Global Studio Narrator',
        'audio' => 'uploads/target-voices/global.mp3',
        'is_active' => true,
    ]);

    TargetVoice::create([
        'user_id' => $user->id,
        'title' => 'My Personal Clone',
        'audio' => 'uploads/target-voices/mine.mp3',
        'is_active' => true,
    ]);

    Sanctum::actingAs($user);

    $response = $this->getJson('/api/target-voices');
    $response->assertStatus(200)
        ->assertJson([
            'status' => true,
            'message' => 'Target Voices List',
        ]);

    expect($response->json('data.total'))->toBe(2);
});

test('user can upload a custom target voice', function () {
    Storage::fake('public');
    $user = User::factory()->create(['is_active' => true]);
    Sanctum::actingAs($user);

    $file = UploadedFile::fake()->create('voice.mp3', 500, 'audio/mpeg');

    $response = $this->postJson('/api/target-voices', [
        'title' => 'Futuristic Cyborg',
        'audio' => $file,
    ]);

    $response->assertStatus(201)
        ->assertJson([
            'status' => true,
            'message' => 'Target Voice Created',
            'data' => [
                'title' => 'Futuristic Cyborg',
            ],
        ]);

    $this->assertDatabaseHas('target_voices', [
        'user_id' => $user->id,
        'title' => 'Futuristic Cyborg',
    ]);
});

test('user cannot update another users target voice', function () {
    $user1 = User::factory()->create(['is_active' => true]);
    $user2 = User::factory()->create(['is_active' => true]);

    $voice = TargetVoice::create([
        'user_id' => $user1->id,
        'title' => 'User1 Voice',
        'audio' => 'uploads/target-voices/u1.mp3',
        'is_active' => true,
    ]);

    Sanctum::actingAs($user2);

    $response = $this->putJson("/api/target-voices/{$voice->id}", [
        'title' => 'Hacked Title',
    ]);

    $response->assertStatus(403);
});
