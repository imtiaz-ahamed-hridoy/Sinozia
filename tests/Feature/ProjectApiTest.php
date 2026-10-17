<?php

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

test('unauthenticated users cannot view projects', function () {
    $response = $this->getJson('/api/projects');
    $response->assertStatus(401);
});

test('user cannot access project details of another user', function () {
    $user1 = User::factory()->create(['is_active' => true]);
    $user2 = User::factory()->create(['is_active' => true]);

    $project = Project::create([
        'user_id' => $user1->id,
        'original_video_path' => 'projects/videos/sample.mp4',
        'status' => 'pending',
    ]);

    Sanctum::actingAs($user2);

    $response = $this->getJson("/api/projects/{$project->id}");
    $response->assertStatus(403);
});

test('project creation validation requires video and target character', function () {
    $user = User::factory()->create(['is_active' => true]);
    Sanctum::actingAs($user);

    $response = $this->postJson('/api/projects', []);
    $response->assertStatus(422);
});

test('confirm generation requires awaiting_user_validation status', function () {
    $user = User::factory()->create(['is_active' => true]);

    $project = Project::create([
        'user_id' => $user->id,
        'original_video_path' => 'projects/videos/sample.mp4',
        'status' => 'pending',
    ]);

    Sanctum::actingAs($user);

    $response = $this->postJson("/api/projects/{$project->id}/confirm");
    $response->assertStatus(422);
});
