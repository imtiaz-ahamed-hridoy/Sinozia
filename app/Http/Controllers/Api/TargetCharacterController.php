<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TargetCharacterResource;
use App\Models\GeneratedCharacter;
use App\Models\TargetCharacter;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class TargetCharacterController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $user = $request->user('sanctum') ?? $request->user();

        // 1. Fetch preset target characters
        $presetCharacters = TargetCharacter::query()->active()->latest()->get();

        // 2. Fetch completed generated AI characters
        $generatedCharacters = collect();
        if ($user) {
            $generatedCharacters = $user->generatedCharacters()
                ->whereNotNull('generated_image_path')
                ->latest()
                ->get();
        } else {
            $generatedCharacters = GeneratedCharacter::query()
                ->whereNotNull('generated_image_path')
                ->latest()
                ->take(10)
                ->get();
        }

        // Combine both into a single collection
        $combined = $presetCharacters->concat($generatedCharacters);

        // Paginate preserving existing API response structure
        $perPage = (int) $request->query('per_page', 10);
        $page = (int) $request->query('page', 1);
        $paginated = new LengthAwarePaginator(
            $combined->forPage($page, $perPage)->values(),
            $combined->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return $this->success(
            TargetCharacterResource::collection($paginated)->withQueryString(),
            'Target Characters List',
            200
        );
    }

    public function show(Request $request, int|string $id)
    {
        // 1. Check preset TargetCharacter first
        $targetCharacter = TargetCharacter::find($id);

        if ($targetCharacter && $targetCharacter->is_active) {
            return $this->success(new TargetCharacterResource($targetCharacter), 'Target Character Details', 200);
        }

        // 2. Check GeneratedCharacter for user or fallback
        $user = $request->user('sanctum') ?? $request->user();
        if ($user) {
            $generatedCharacter = $user->generatedCharacters()->find($id);
            if ($generatedCharacter) {
                return $this->success(new TargetCharacterResource($generatedCharacter), 'Target Character Details', 200);
            }
        }

        $genChar = GeneratedCharacter::whereNotNull('generated_image_path')->find($id);
        if ($genChar) {
            return $this->success(new TargetCharacterResource($genChar), 'Target Character Details', 200);
        }

        return $this->error(null, 'Target Character Not Found', 404);
    }
}
