<?php

namespace App\Http\Controllers\Web\Backend;

use App\Http\Controllers\Controller;
use App\Models\TargetVoice;
use App\Models\User;
use App\Traits\ApiResponse;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class TargetVoiceController extends Controller
{
    use ApiResponse;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            try {
                $data = TargetVoice::query()->adminVoice()->latest();

                return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('audio', function (TargetVoice $targetVoice) {
                        if (! $targetVoice->audio) {
                            return 'No audio';
                        }

                        return '<audio width="200" controls>
                                    <source src="'.asset('storage/'.$targetVoice->audio).'" type="audio/mpeg">
                                    Your browser does not support the audio element.
                                </audio>';
                    })
                    ->addColumn('is_active', function (TargetVoice $targetVoice) {
                        $checked = $targetVoice->is_active ? 'checked="checked"' : '';

                        return '
                            <a href="#" class="change_status d-inline-block"
                            data-id="'.$targetVoice->id.'"
                            data-enabled="'.($targetVoice->is_active ? '0' : '1').'"
                            data-title="Do you want to '.($targetVoice->is_active ? 'Deactivate' : 'Activate').' it?"
                            data-description="This will change the Target Voice status."
                            data-bs-toggle="modal" data-bs-target="#statusModal" style="text-decoration: none;">
                                <label class="switch mb-0" style="transform: scale(0.65); transform-origin: center center; margin: 0; vertical-align: middle;">
                                    <input type="checkbox" '.$checked.' onclick="return false;">
                                    <span class="switch-state" style="transition: background-color 0.3s ease;"></span>
                                </label>
                            </a>';
                    })
                    ->addColumn('action', function (TargetVoice $targetVoice) {
                        return view('components.action-buttons', [
                            'id' => $targetVoice->id,
                            'show' => 'admin.target-voices.show',
                            'edit' => 'admin.target-voices.edit',
                            'delete' => true,
                        ])->render();
                    })
                    ->rawColumns(['audio', 'is_active', 'action'])
                    ->make(true);
            } catch (Exception $e) {
                return $this->error([
                    'message' => 'Failed to fetch data.',
                    'error' => $e->getMessage(),
                ], 'Error fetching data.', 500);
            }
        }

        return view('backend.layouts.target-voices.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (User::find(auth()->user()->id)) {
            return view('backend.layouts.target-voices.create');
        }

        return redirect()->route('admin.target-voices.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:255',
                'elevenlabs_voice_id' => 'required|string|max:255',
                'audio' => 'nullable|file|mimes:mp3,wav,ogg,m4a|max:102400',
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $audio = null;
            if ($request->hasFile('audio')) {
                $audio = $request->file('audio')->store('uploads/target-voices', 'public');
            }

            TargetVoice::create([
                'title' => $request->title,
                'elevenlabs_voice_id' => $request->elevenlabs_voice_id,
                'audio' => $audio,
            ]);

            flashMessage('Target Voice created successfully.', 'success');

            return redirect()->route('admin.target-voices.index');

        } catch (Exception $e) {
            flashMessage($e->getMessage(), 'error');

            return redirect()->route('admin.target-voices.index');
        }
    }

    public function update(Request $request, string $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:255',
                'elevenlabs_voice_id' => 'required|string|max:255',
                'audio' => 'nullable|file|mimes:mp3,wav,ogg,m4a|max:102400',
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $targetVoice = TargetVoice::findOrFail($id);

            $audio = $targetVoice->audio;
            if ($request->hasFile('audio')) {
                Storage::disk('public')->delete($targetVoice->audio);
                $audio = $request->file('audio')->store('uploads/target-voices', 'public');
            }

            $targetVoice->update([
                'title' => $request->title,
                'elevenlabs_voice_id' => $request->elevenlabs_voice_id,
                'audio' => $audio,
            ]);

            flashMessage('Target Voice updated successfully.', 'success');

            return redirect()->route('admin.target-voices.index');

        } catch (Exception $e) {
            flashMessage($e->getMessage(), 'error');

            return redirect()->route('admin.target-voices.index');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        if (User::find(auth()->user()->id)) {
            $targetVoice = TargetVoice::find($id);

            return view('backend.layouts.target-voices.show', compact('targetVoice'));
        }

        return redirect()->route('admin.target-voices.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        if (User::find(auth()->user()->id)) {
            $targetVoice = TargetVoice::find($id);

            return view('backend.layouts.target-voices.create', compact('targetVoice'));
        }

        return redirect()->route('admin.target-voices.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $targetVoice = TargetVoice::findOrFail($id);

        if (! $targetVoice) {
            return response()->json([
                'success' => false,
                'message' => 'Target Voice not found.',
            ]);
        }

        if ($targetVoice->audio) {
            Storage::disk('public')->delete($targetVoice->audio);
        }

        $targetVoice->delete();

        return response()->json([
            'success' => true,
            'message' => 'Deleted successfully.',
        ]);
    }

    public function status(Request $request, TargetVoice $targetVoice)
    {
        try {
            $targetVoice->is_active = ! $targetVoice->is_active;
            $targetVoice->save();

            return response()->json([
                'success' => true,
                'message' => $targetVoice->is_active ? 'Activated Successfully.' : 'Deactivated Successfully.',
                'data' => $targetVoice,
            ]);
        } catch (Exception $e) {
            return $this->error([
                'message' => 'Failed to update target voice status.',
                'error' => $e->getMessage(),
            ], 'Error updating status', 500);
        }
    }
}
