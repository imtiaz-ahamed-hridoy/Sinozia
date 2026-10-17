<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\GeneratedCharacterResource;
use App\Jobs\GenerateAiCharacterJob;
use App\Models\GeneratedCharacter;
use App\Traits\ApiResponse;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GeneratedCharacterController extends Controller
{
    use ApiResponse;

    /**
     * List the authenticated user's completed generated characters.
     */
    public function index(Request $request): JsonResponse
    {
        $data = $request->user()
            ->generatedCharacters()
            ->whereNotNull('generated_image_path')
            ->latest()
            ->paginate((int) $request->query('per_page', 10));

        return $this->success(
            GeneratedCharacterResource::collection($data)->withQueryString(),
            'Generated Characters List',
            200
        );
    }

    /**
     * Create a new generated character and dispatch the AI generation job.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'title' => 'nullable|string|max:255',
            'age' => 'nullable|integer|min:18|max:80',
            'gender' => 'nullable|in:male,female',
            'ethnicity' => 'nullable|in:asian,black,latino,white',
            'eye_color' => 'nullable|in:brown,gray,blue,green,hazel',
            'build' => 'nullable|in:slim,athletic,muscular,curvy',
            'style' => 'nullable|in:casual,luxury,streetwear,futuristic',
            'expression' => 'nullable|in:confident,serious,happy,futuristic',
            'custom_modification' => 'nullable|string|max:1000',
        ]);

        $generatedCharacter = $request->user()->generatedCharacters()->create([
            'title' => $request->title,
            'age' => $request->age,
            'gender' => $request->gender,
            'ethnicity' => $request->ethnicity,
            'eye_color' => $request->eye_color,
            'build' => $request->build,
            'style' => $request->style,
            'expression' => $request->expression,
            'custom_modification' => $request->custom_modification,
            'status' => 'pending',
        ]);

        GenerateAiCharacterJob::dispatch($generatedCharacter);

        return $this->success(
            ['id' => $generatedCharacter->id, 'status' => $generatedCharacter->status],
            'Character generation started.',
            201
        );
    }

    /**
     * Show a single generated character.
     */
    public function show(Request $request, GeneratedCharacter $generatedCharacter): JsonResponse
    {
        $this->authorizeUser($request->user()->id, $generatedCharacter->user_id);

        return $this->success(
            new GeneratedCharacterResource($generatedCharacter),
            'Generated Character Details',
            200
        );
    }

    /**
     * Apply a custom modification text and re-generate the character image.
     */
    public function applyModification(Request $request, GeneratedCharacter $generatedCharacter): JsonResponse
    {
        $this->authorizeUser($request->user()->id, $generatedCharacter->user_id);

        $request->validate([
            'custom_modification' => 'required|string|max:1000',
        ]);

        $generatedCharacter->update([
            'custom_modification' => $request->custom_modification,
            'status' => 'pending',
            'generated_image_path' => null,
            'error_message' => null,
        ]);

        GenerateAiCharacterJob::dispatch($generatedCharacter);

        return $this->success(
            new GeneratedCharacterResource($generatedCharacter->fresh()),
            'Custom modification applied. Regenerating character.',
            200
        );
    }

    /**
     * Delete a generated character and its associated storage file.
     */
    public function destroy(Request $request, GeneratedCharacter $generatedCharacter): JsonResponse
    {
        $this->authorizeUser($request->user()->id, $generatedCharacter->user_id);

        if ($generatedCharacter->generated_image_path) {
            Storage::disk('public')->delete($generatedCharacter->generated_image_path);
        }

        $generatedCharacter->delete();

        return $this->success(null, 'Generated Character deleted.', 200);
    }

    /**
     * Ensure the authenticated user owns the resource.
     */
    private function authorizeUser($authId, $ownerId): void
    {
        if ($authId !== $ownerId) {
            throw new HttpResponseException(
                $this->error(null, 'You are not allowed to perform this action.', 403)
            );
        }
    }
}
