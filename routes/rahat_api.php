<?php

use App\Events\PreviewReady;
use App\Http\Controllers\Api\CreditController;
use App\Http\Controllers\Api\GeneratedCharacterController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\TargetCharacterController;
use App\Http\Controllers\Api\TargetVoiceController;
use App\Models\Project;
use Illuminate\Support\Facades\Route;

// User Profile
Route::middleware(['auth:sanctum', 'enabled'])->group(function () {
    // Projects / Pipeline
    Route::apiResource('projects', ProjectController::class)->only(['index', 'show', 'store']);
    Route::post('/projects/{project}/confirm', [ProjectController::class, 'confirmGeneration']);
    Route::post('/projects/{project}/update', [ProjectController::class, 'update']);

    // Credits
    Route::get('/credits/balance', [CreditController::class, 'balance']);
    Route::get('/credits/history', [CreditController::class, 'history']);

    // Target Voices
    Route::apiResource('target-voices', TargetVoiceController::class);

    // Generated Characters (User AI Character Creation)
    Route::apiResource('generated-characters', GeneratedCharacterController::class)->only(['index', 'show', 'store', 'destroy']);
    Route::post('/generated-characters/{generatedCharacter}/apply-modification', [GeneratedCharacterController::class, 'applyModification']);

    Route::get('/test-broadcast', function () {
        $project = Project::first();
        if (! $project) {
            return response()->json(['message' => 'No project found to test broadcast'], 404);
        }
        event(new PreviewReady($project));

        return 'sent';
    });

});
// Target Characters
Route::apiResource('target-characters', TargetCharacterController::class)->only(['index', 'show']);
