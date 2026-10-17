<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TargetVoiceResource;
use App\Models\TargetVoice;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TargetVoiceController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $authId = $request->user()?->id;

        $data = TargetVoice::query()
            ->where(function ($q) use ($authId) {
                $q->where('user_id', $authId)
                    ->orWhereNull('user_id');
            })
            ->active()->latest()->paginate((int) $request->query('per_page', 10));

        return $this->success(TargetVoiceResource::collection($data)->withQueryString(), 'Target Voices List', 200);
    }

    public function show(TargetVoice $targetVoice)
    {
        if (! $targetVoice->is_active) {
            return $this->error(null, 'Target Voice Not Found', 404);
        }

        return $this->success(new TargetVoiceResource($targetVoice), 'Target Voice Details', 200);
    }

    public function store(Request $request)
    {
        $user = $request->user();
        $request->validate([
            'title' => 'required|string|max:255',
            'audio' => 'required|file|mimes:mp3,wav,ogg,m4a|max:102400', // 100 MB Maximum file size
        ]);

        $audio = null;
        if ($request->hasFile('audio')) {
            $audio = $request->file('audio')->store('uploads/target-voices', 'public');
        }

        $targetVoice = $user->targetVoices()->create([
            'title' => $request->title,
            'audio' => $audio,
        ]);

        return $this->success(new TargetVoiceResource($targetVoice), 'Target Voice Created', 201);
    }

    public function update(Request $request, TargetVoice $targetVoice)
    {
        // Check if the target voice belongs to the authenticated user or is public (user_id null)
        if ($targetVoice->user_id !== $request->user()->id) {
            return $this->error(null, 'Unauthorized', 403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'audio' => 'nullable|file|mimes:mp3,wav,ogg,m4a|max:102400', // 100 MB Maximum file size
        ]);

        $audio = $targetVoice->audio; // Keep existing audio
        if ($request->hasFile('audio')) {
            Storage::disk('public')->delete($targetVoice->audio);
            $audio = $request->file('audio')->store('uploads/target-voices', 'public');
        }

        $targetVoice->update([
            'title' => $request->title,
            'audio' => $audio,
        ]);

        return $this->success(new TargetVoiceResource($targetVoice), 'Target Voice Updated', 200);
    }

    public function destroy(Request $request, TargetVoice $targetVoice)
    {
        // Check if the target voice belongs to the authenticated user
        if ($targetVoice->user_id !== $request->user()->id) {
            return $this->error(null, 'Unauthorized', 403);
        }

        // Delete audio
        if ($targetVoice->audio) {
            Storage::disk('public')->delete($targetVoice->audio);
        }

        $targetVoice->delete();

        return $this->success(null, 'Target Voice Deleted', 200);
    }
}
