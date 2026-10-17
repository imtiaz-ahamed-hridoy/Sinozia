<?php

namespace App\Http\Controllers\Web\Backend\Faq;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use App\Models\User;
use App\Traits\ApiResponse;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class FaqController extends Controller
{
    use ApiResponse;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            try {
                $data = Faq::latest();

                return DataTables::of($data)
                    ->addIndexColumn()
                    ->editColumn('answer', function (Faq $faq) {
                        return e(Str::limit(strip_tags($faq->answer), 100));
                    })
                    ->addColumn('is_active', function (Faq $faq) {
                        $checked = $faq->is_active ? 'checked="checked"' : '';

                        return '
                            <a href="#" class="change_status d-inline-block"
                            data-id="'.$faq->id.'"
                            data-enabled="'.($faq->is_active ? '0' : '1').'"
                            data-title="Do you want to '.($faq->is_active ? 'Deactivate' : 'Activate').' it?"
                            data-description="This will change the FAQ status."
                            data-bs-toggle="modal" data-bs-target="#statusModal" style="text-decoration: none;">
                                <label class="switch mb-0" style="transform: scale(0.65); transform-origin: center center; margin: 0; vertical-align: middle;">
                                    <input type="checkbox" '.$checked.' onclick="return false;">
                                    <span class="switch-state" style="transition: background-color 0.3s ease;"></span>
                                </label>
                            </a>';
                    })
                    ->addColumn('action', function (Faq $faq) {
                        return view('components.action-buttons', [
                            'id' => $faq->id,
                            'show' => 'admin.faqs.show',
                            'edit' => 'admin.faqs.edit',
                            'delete' => true,
                        ])->render();
                    })
                    ->rawColumns(['is_active', 'answer', 'status', 'action'])
                    ->make(true);
            } catch (Exception $e) {
                return $this->error([
                    'message' => 'Failed to fetch data.',
                    'error' => $e->getMessage(),
                ], 'Error fetching data.', 500);
            }
        }

        return view('backend.layouts.faqs.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (User::find(auth()->user()->id)) {
            return view('backend.layouts.faqs.create');
        }

        return redirect()->route('admin.faqs.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            if (User::find(auth()->user()->id)) {
                $validator = Validator::make($request->all(), [
                    'question' => 'required|string|max:255',
                    'answer' => 'required|string|max:10000',
                ]);

                if ($validator->fails()) {
                    return redirect()->back()->withErrors($validator)->withInput();
                }

                $data = new Faq;
                $data->question = $request->question;
                $data->answer = $request->answer;

                $data->save();
            }
            flashMessage('FAQ created successfully.', 'success');

            return redirect()->route('admin.faqs.index');
        } catch (Exception $e) {
            flashMessage($e->getMessage(), 'error');

            return redirect()->route('admin.faqs.index');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        if (User::find(auth()->user()->id)) {
            $faq = Faq::find($id);

            return view('backend.layouts.faqs.show', compact('faq'));
        }

        return redirect()->route('admin.faqs.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        if (User::find(auth()->user()->id)) {
            $faq = Faq::find($id);

            return view('backend.layouts.faqs.create', compact('faq'));
        }

        return redirect()->route('admin.faqs.index');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            if (User::find(auth()->user()->id)) {
                $validator = Validator::make($request->all(), [
                    'question' => 'required|string|max:255',
                    'answer' => 'required|string|max:10000',
                ]);

                if ($validator->fails()) {
                    return redirect()->back()->withErrors($validator)->withInput();
                }

                $data = Faq::find($id);
                $data->question = $request->question;
                $data->answer = $request->answer;

                $data->save();
            }
            flashMessage('FAQ updated successfully.', 'success');

            return redirect()->route('admin.faqs.index');
        } catch (Exception $e) {
            flashMessage($e->getMessage(), 'error');

            return redirect()->route('admin.faqs.index');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $data = Faq::findOrFail($id);

        if (! $data) {
            return response()->json([
                'success' => false,
                'message' => 'FAQ not found.',
            ], 404);
        }

        $data->delete();

        return response()->json([
            'success' => true,
            'message' => 'Deleted successfully.',
        ]);
    }

    public function status(Request $request, Faq $faq)
    {
        try {
            $faq->is_active = ! $faq->is_active;
            $faq->save();

            return response()->json([
                'success' => true,
                'message' => $faq->is_active ? 'Activated Successfully.' : 'Deactivated Successfully.',
                'data' => $faq,
            ]);
        } catch (Exception $e) {
            return $this->error([
                'message' => 'Failed to update FAQ status.',
                'error' => $e->getMessage(),
            ], 'Error updating status', 500);
        }
    }
}
