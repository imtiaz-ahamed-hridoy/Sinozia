<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProjectResource;
use App\Jobs\ExtractVideoFrameJob;
use App\Jobs\GenerateKlingVideoJob;
use App\Models\GeneratedCharacter;
use App\Models\Project;
use App\Models\TargetCharacter;
use App\Services\CreditService;
use App\Traits\ApiResponse;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    use ApiResponse;

    public function __construct(private CreditService $credits) {}

    public function store(Request $request): JsonResponse
    {
        $user = $request->user();

        $rules = [
            'video' => 'sometimes|file|mimes:mp4,mov,avi|max:204800',
            'target_character' => 'nullable|file|mimes:jpg,jpeg,png,gif,webp|max:10240',
            'target_character_id' => 'nullable|exists:target_characters,id',
            'generated_character_id' => 'nullable|exists:generated_characters,id',
            'target_voice_id' => $request->boolean('voice_enabled') ? 'required|exists:target_voices,id' : 'nullable|exists:target_voices,id',

            'transformation_type' => 'sometimes|array|min:1',
            'transformation_type.*' => 'in:face,body,background',

            'voice_enabled' => 'sometimes|boolean',
            'quality' => 'sometimes|in:std,pro',
        ];

        $request->validate($rules);

        if (! $request->hasFile('video')) {
            return $this->error(null, 'Video file is required.', 422);
        }

        if (! $request->hasFile('target_character') && ! $request->filled('target_character_id') && ! $request->filled('generated_character_id')) {
            return $this->error(null, 'Target character is required (upload image, or select target_character_id / generated_character_id).', 422);
        }

        $videoPath = $request->file('video')->store('projects/videos', 'public');

        // Resolve target character path from one of three input modes:
        // 1. Admin-uploaded character (target_character_id)
        // 2. User-generated AI character (generated_character_id)
        // 3. Direct file upload (target_character file)
        if ($request->filled('target_character_id')) {
            $characterPath = TargetCharacter::find($request->target_character_id)?->image;
        } elseif ($request->filled('generated_character_id')) {
            $generatedCharacter = GeneratedCharacter::findOrFail($request->generated_character_id);
            $characterPath = $generatedCharacter->generated_image_path;
        } elseif ($request->hasFile('target_character')) {
            $characterPath = $request->file('target_character')->store('projects/characters', 'public');
        } else {
            $characterPath = null;
        }

        $project = Project::create([
            'user_id' => $user->id,
            'target_character_id' => $request->target_character_id ?? null,
            'target_voice_id' => $request->target_voice_id ?? null,
            'original_video_path' => $videoPath,
            'target_character_path' => $characterPath,
            'transformation_type' => $request->transformation_type,
            'voice_enabled' => $request->boolean('voice_enabled'),
            'status' => 'pending',
            'quality' => $request->input('quality', 'std'),
        ]);

        ExtractVideoFrameJob::dispatch($project);

        return $this->success(['project_id' => $project->id, 'status' => $project->status], 'Project created and processing started.', 201);
    }

    // update
    public function update(Request $request, Project $project)
    {
        $user = $request->user();
        $this->authorizeUser($user->id, $project->user_id);

        $rules = [
            'target_character' => 'nullable|file|mimes:jpg,jpeg,png,gif,webp|max:10240',
            'target_character_id' => 'nullable|exists:target_characters,id',
            'generated_character_id' => 'nullable|exists:generated_characters,id',
            'target_voice_id' => 'nullable|exists:target_voices,id',

            'transformation_type' => 'nullable|array|min:1',
            'transformation_type.*' => 'in:face,body,background,voice',

            'voice_enabled' => 'nullable|boolean',
            'quality' => 'nullable|in:std,pro',
        ];

        $validated = $request->validate($rules);

        // Upload new target character image (direct file)
        if ($request->hasFile('target_character')) {
            $validated['target_character_path'] = $request->file('target_character')->store('projects/characters', 'public');
            $validated['target_character_id'] = null;
        }

        // Admin-uploaded character selected
        if ($request->filled('target_character_id')) {
            $character = TargetCharacter::find($request->target_character_id);
            $validated['target_character_path'] = $character?->image;
        }

        // User's own generated character selected
        if ($request->filled('generated_character_id')) {
            $generatedCharacter = GeneratedCharacter::findOrFail($request->generated_character_id);
            $validated['target_character_path'] = $generatedCharacter->generated_image_path;
            $validated['target_character_id'] = null;
        }

        unset($validated['generated_character_id']);

        $project->update($validated);
        $project->load(['targetCharacter', 'targetVoice']);

        ExtractVideoFrameJob::dispatch($project);

        return $this->success(new ProjectResource($project), 'Project updated successfully.');
    }

    public function show(Request $request, Project $project)
    {
        $user = $request->user();
        $this->authorizeUser($user->id, $project->user_id);
        $project->load(['targetCharacter', 'targetVoice']);

        return $this->success(new ProjectResource($project), 'Project details', 200);
    }

    public function confirmGeneration(Request $request, Project $project): JsonResponse
    {
        $user = $request->user();
        $this->authorizeUser($user->id, $project->user_id);

        if ($project->status !== 'awaiting_user_validation') {
            return $this->error(null, 'Project not ready for confirmation.', 422);
        }

        if (! $user->canGenerate()) {
            return $this->error(null, 'Upgrade your plan to generate videos.', 403);
        }

        $cost = $user->getPlanConfig()['video_cost'];

        if (! $this->credits->hasEnough($user, $cost)) {
            return $this->error(null, 'Insufficient credits.', 402);
        }

        $this->credits->deduct($user, $cost, 'Video generation for project '.$project->id, $project);

        GenerateKlingVideoJob::dispatch($project);

        return $this->success([
            'message' => 'Video generation started.',
            'credits_deducted' => $cost,
            'credits_remaining' => $this->credits->getBalance($user),
        ]);
    }

    public function index(Request $request)
    {
        $user = $request->user();
        $query = $user->projects()->with(['targetCharacter', 'targetVoice']);

        if ($request->filled('status') && $request->query('status') !== 'all') {
            $query->where('status', $request->query('status'));
        } elseif (! $request->filled('status')) {
            // Default Flutter app behavior: return completed projects
            $query->whereNotNull('final_video_path');
        }

        $projects = $query->latest()->paginate((int) $request->query('per_page', 10));

        return $this->success(ProjectResource::collection($projects)->withQueryString(), 'User Projects', 200);
    }

    private function authorizeUser(int|string $authId, int|string $ownerId): void
    {
        if ($authId !== $ownerId) {
            throw new HttpResponseException($this->error(null, 'You are not allowed to perform this action.', 403));
        }
    }
}
