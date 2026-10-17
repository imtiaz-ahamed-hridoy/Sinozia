<?php

namespace App\Http\Controllers\Web\Backend;

use App\Http\Controllers\Controller;
use App\Models\Tutorial;
use App\Models\User;
use App\Traits\ApiResponse;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class TutorialController extends Controller
{
    use ApiResponse;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            try {
                $data = Tutorial::latest();

                return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('description', function (Tutorial $tutorial) {
                        return e(Str::limit(strip_tags($tutorial->description), 100));
                    })
                    ->addColumn('image_url', function (Tutorial $tutorial) {
                        $url = asset($tutorial->image_url);

                        if (empty($tutorial->image_url)) {
                            $url = asset('backend/assets/images/avtar/18.jpg');
                        }

                        return '<img src="'.$url.'" alt="'.$tutorial->title.'" class="img-50 img-fluid" style="max-height: 50px;">';
                    })
                    ->addColumn('is_active', function (Tutorial $tutorial) {
                        $checked = $tutorial->is_active ? 'checked="checked"' : '';

                        return '
                            <a href="#" class="change_status d-inline-block"
                            data-id="'.$tutorial->id.'"
                            data-enabled="'.($tutorial->is_active ? '0' : '1').'"
                            data-title="Do you want to '.($tutorial->is_active ? 'Deactivate' : 'Activate').' it?"
                            data-description="This will change the Tutorial status."
                            data-bs-toggle="modal" data-bs-target="#statusModal" style="text-decoration: none;">
                                <label class="switch mb-0" style="transform: scale(0.65); transform-origin: center center; margin: 0; vertical-align: middle;">
                                    <input type="checkbox" '.$checked.' onclick="return false;">
                                    <span class="switch-state" style="transition: background-color 0.3s ease;"></span>
                                </label>
                            </a>';
                    })
                    ->addColumn('action', function (Tutorial $tutorial) {
                        return view('components.action-buttons', [
                            'id' => $tutorial->id,
                            'show' => 'admin.tutorials.show',
                            'edit' => 'admin.tutorials.edit',
                            'delete' => true,
                        ])->render();
                    })
                    ->rawColumns(['description', 'image_url', 'is_active', 'action'])
                    ->make(true);
            } catch (Exception $e) {
                return $this->error([
                    'message' => 'Failed to fetch data.',
                    'error' => $e->getMessage(),
                ], 'Error fetching data.', 500);
            }
        }

        return view('backend.layouts.tutorials.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (User::find(auth()->user()->id)) {
            return view('backend.layouts.tutorials.create');
        }

        return redirect()->route('admin.tutorials.index');
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
                    'image_url' => 'nullable|file|mimes:jpg,jpeg,png|max:51200', // 50 MB Maximum file size
                ]);

                if ($validator->fails()) {
                    return redirect()->back()->withErrors($validator)->withInput();
                }

                $image_url = null;
                if ($request->hasFile('image_url')) {
                    $image_url = uploadFile($request->file('image_url'), 'uploads/tutorials');
                }

                $data = new Tutorial;
                $data->title = $request->title;
                $data->description = $request->description;
                $data->image_url = $image_url;

                $data->save();
            }
            flashMessage('Tutorial created successfully.', 'success');

            return redirect()->route('admin.tutorials.index');
        } catch (Exception $e) {
            flashMessage($e->getMessage(), 'error');

            return redirect()->route('admin.tutorials.index');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        if (User::find(auth()->user()->id)) {
            $tutorial = Tutorial::find($id);

            return view('backend.layouts.tutorials.show', compact('tutorial'));
        }

        return redirect()->route('admin.tutorials.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        if (User::find(auth()->user()->id)) {
            $tutorial = Tutorial::find($id);

            return view('backend.layouts.tutorials.create', compact('tutorial'));
        }

        return redirect()->route('admin.tutorials.index');
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
                    'image_url' => 'nullable|file|mimes:jpg,jpeg,png|max:51200', // 50 MB Maximum file size
                ]);

                if ($validator->fails()) {
                    return redirect()->back()->withErrors($validator)->withInput();
                }

                $tutorial = Tutorial::find($id);
                if (! $tutorial) {
                    flashMessage('Tutorial not found.', 'error');

                    return redirect()->route('admin.tutorials.index');
                }

                $image_url = $tutorial->image_url;
                if ($request->hasFile('image_url')) {
                    $image_url = uploadFile($request->file('image_url'), 'uploads/tutorials');
                }

                $tutorial->title = $request->title;
                $tutorial->description = $request->description;
                $tutorial->image_url = $image_url;

                $tutorial->save();
            }
            flashMessage('Tutorial updated successfully.', 'success');

            return redirect()->route('admin.tutorials.index');
        } catch (Exception $e) {
            flashMessage($e->getMessage(), 'error');

            return redirect()->route('admin.tutorials.index');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $tutorial = Tutorial::findOrFail($id);

        if (! $tutorial) {
            return response()->json([
                'success' => false,
                'message' => 'Tutorial not found.',
            ]);
        }

        if ($tutorial->image_url) {
            deleteFile($tutorial->image_url);
        }

        $tutorial->delete();

        return response()->json([
            'success' => true,
            'message' => 'Deleted successfully.',
        ]);
    }

    public function status(Request $request, Tutorial $tutorial)
    {
        try {
            $tutorial->is_active = ! $tutorial->is_active;
            $tutorial->save();

            return response()->json([
                'success' => true,
                'message' => $tutorial->is_active ? 'Activated Successfully.' : 'Deactivated Successfully.',
                'data' => $tutorial,
            ]);
        } catch (Exception $e) {
            return $this->error([
                'message' => 'Failed to update tutorial status.',
                'error' => $e->getMessage(),
            ], 'Error updating status', 500);
        }
    }
}
