<?php

namespace App\Http\Controllers\Web\Backend;

use App\Http\Controllers\Controller;
use App\Models\TargetCharacter;
use App\Models\User;
use App\Traits\ApiResponse;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class TargetCharacterController extends Controller
{
    use ApiResponse;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            try {
                $data = TargetCharacter::latest();

                return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('description', function (TargetCharacter $targetCharacter) {
                        return e(Str::limit(strip_tags($targetCharacter->description), 100));
                    })
                    ->addColumn('image', function (TargetCharacter $targetCharacter) {
                        if (! $targetCharacter->image) {
                            return 'no image';
                        }

                        return '<img src="'.asset('storage/'.$targetCharacter->image).'"                 width="100" height="100"                 style="object-fit: cover;" />';
                    })
                    ->addColumn('is_active', function (TargetCharacter $targetCharacter) {
                        $checked = $targetCharacter->is_active ? 'checked="checked"' : '';

                        return '
                            <a href="#" class="change_status d-inline-block"
                            data-id="'.$targetCharacter->id.'"
                            data-enabled="'.($targetCharacter->is_active ? '0' : '1').'"
                            data-title="Do you want to '.($targetCharacter->is_active ? 'Deactivate' : 'Activate').' it?"
                            data-description="This will change the Target Character status."
                            data-bs-toggle="modal" data-bs-target="#statusModal" style="text-decoration: none;">
                                <label class="switch mb-0" style="transform: scale(0.65); transform-origin: center center; margin: 0; vertical-align: middle;">
                                    <input type="checkbox" '.$checked.' onclick="return false;">
                                    <span class="switch-state" style="transition: background-color 0.3s ease;"></span>
                                </label>
                            </a>';
                    })
                    ->addColumn('action', function (TargetCharacter $targetCharacter) {
                        return view('components.action-buttons', [
                            'id' => $targetCharacter->id,
                            'show' => 'admin.target-characters.show',
                            'edit' => 'admin.target-characters.edit',
                            'delete' => true,
                        ])->render();
                    })
                    ->rawColumns(['description', 'image', 'is_active', 'action'])
                    ->make(true);
            } catch (Exception $e) {
                return $this->error([
                    'message' => 'Failed to fetch data.',
                    'error' => $e->getMessage(),
                ], 'Error fetching data.', 500);
            }
        }

        return view('backend.layouts.target-characters.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (User::find(auth()->user()->id)) {
            return view('backend.layouts.target-characters.create');
        }

        return redirect()->route('admin.target-characters.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            if (User::find(auth()->user()->id)) {
                $validator = Validator::make($request->all(), [
                    'title' => 'required|string|max:255',
                    'description' => 'required|string|max:10000',
                    'image' => 'nullable|file|mimes:jpeg,png,jpg,gif|max:10240', // 10 MB Maximum file size
                ]);

                if ($validator->fails()) {
                    return redirect()->back()->withErrors($validator)->withInput();
                }

                $image = null;
                if ($request->hasFile('image')) {
                    $image = $request->file('image')->store('uploads/target-characters', 'public');
                    // $image = uploadFile($request->file('image'), 'uploads/target-characters');
                }

                $data = new TargetCharacter;
                $data->title = $request->title;
                $data->description = $request->description;
                $data->image = $image;

                $data->save();
            }
            flashMessage('Target Character created successfully.', 'success');

            return redirect()->route('admin.target-characters.index');
        } catch (Exception $e) {
            flashMessage($e->getMessage(), 'error');

            return redirect()->route('admin.target-characters.index');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        if (User::find(auth()->user()->id)) {
            $targetCharacter = TargetCharacter::find($id);

            return view('backend.layouts.target-characters.show', compact('targetCharacter'));
        }

        return redirect()->route('admin.target-characters.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        if (User::find(auth()->user()->id)) {
            $targetCharacter = TargetCharacter::find($id);

            return view('backend.layouts.target-characters.create', compact('targetCharacter'));
        }

        return redirect()->route('admin.target-characters.index');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            if (User::find(auth()->user()->id)) {
                $validator = Validator::make($request->all(), [
                    'title' => 'required|string|max:255',
                    'description' => 'required|string|max:10000',
                    'image' => 'nullable|file|mimes:jpeg,png,jpg,gif|max:10240', // 10 MB Maximum file size
                ]);

                if ($validator->fails()) {
                    return redirect()->back()->withErrors($validator)->withInput();
                }

                $targetCharacter = TargetCharacter::find($id);
                if (! $targetCharacter) {
                    flashMessage('Target Character not found.', 'error');

                    return redirect()->route('admin.target-characters.index');
                }

                $image = $targetCharacter->image;
                if ($request->hasFile('image')) {
                    if ($targetCharacter->image) {
                        Storage::disk('public')->delete($targetCharacter->image);
                    }
                    $image = $request->file('image')->store('uploads/target-characters', 'public');
                }

                $targetCharacter->title = $request->title;
                $targetCharacter->description = $request->description;
                $targetCharacter->image = $image;

                $targetCharacter->save();
            }
            flashMessage('Target Character updated successfully.', 'success');

            return redirect()->route('admin.target-characters.index');
        } catch (Exception $e) {
            flashMessage($e->getMessage(), 'error');

            return redirect()->route('admin.target-characters.index');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $targetCharacter = TargetCharacter::findOrFail($id);

        if (! $targetCharacter) {
            return response()->json([
                'success' => false,
                'message' => 'Target Character not found.',
            ]);
        }

        if ($targetCharacter->image) {
            Storage::disk('public')->delete($targetCharacter->image);
        }

        $targetCharacter->delete();

        return response()->json([
            'success' => true,
            'message' => 'Deleted successfully.',
        ]);
    }

    public function status(Request $request, TargetCharacter $targetCharacter)
    {
        try {
            $targetCharacter->is_active = ! $targetCharacter->is_active;
            $targetCharacter->save();

            return response()->json([
                'success' => true,
                'message' => $targetCharacter->is_active ? 'Activated Successfully.' : 'Deactivated Successfully.',
                'data' => $targetCharacter,
            ]);
        } catch (Exception $e) {
            return $this->error([
                'message' => 'Failed to update target character status.',
                'error' => $e->getMessage(),
            ], 'Error updating status', 500);
        }
    }
}
