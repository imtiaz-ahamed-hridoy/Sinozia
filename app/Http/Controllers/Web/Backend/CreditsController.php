<?php

namespace App\Http\Controllers\Web\Backend;

use App\Http\Controllers\Controller;
use App\Models\Credit;
use App\Models\User;
use App\Traits\ApiResponse;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

class CreditsController extends Controller
{
    use ApiResponse;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            try {
                $data = Credit::latest();

                return DataTables::of($data)
                    ->addIndexColumn()
                    ->editColumn('amount', function (Credit $credit) {
                        return '€'.$credit->amount;
                    })
                    ->addColumn('most_popular', function (Credit $credit) {
                        $checked = $credit->most_popular ? 'checked="checked"' : '';

                        return '
                            <a href="#" class="change_status d-inline-block"
                            data-id="'.$credit->id.'"
                            data-enabled="'.($credit->most_popular ? '0' : '1').'"
                            data-title="Do you want to '.($credit->most_popular ? 'Remove from Popular' : 'Mark as Popular').' it?"
                            data-description="This will change the credit status."
                            data-bs-toggle="modal" data-bs-target="#statusModal" style="text-decoration: none;">
                                <label class="switch mb-0" style="transform: scale(0.65); transform-origin: center center; margin: 0; vertical-align: middle;">
                                    <input type="checkbox" '.$checked.' onclick="return false;">
                                    <span class="switch-state" style="transition: background-color 0.3s ease;"></span>
                                </label>
                            </a>';
                    })
                    ->addColumn('action', function (Credit $credit) {
                        return view('components.action-buttons', [
                            'id' => $credit->id,
                            'edit' => 'admin.credits.edit',
                            'delete' => true,
                        ])->render();
                    })
                    ->rawColumns(['most_popular', 'amount', 'action'])
                    ->make(true);
            } catch (Exception $e) {
                return $this->error([
                    'message' => 'Failed to fetch data.',
                    'error' => $e->getMessage(),
                ], 'Error fetching data.', 500);
            }
        }

        return view('backend.layouts.credits.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('backend.layouts.credits.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            if (User::find(auth()->user()->id)) {
                $validator = Validator::make($request->all(), [
                    'credit' => 'required|integer|min:1',
                    'amount' => 'required|numeric|min:0.01',
                ]);

                if ($validator->fails()) {
                    return redirect()->back()->withErrors($validator)->withInput();
                }

                $data = new Credit;
                $data->credit = $request->credit;
                $data->amount = $request->amount;

                $data->save();
            }
            flashMessage('Credit created successfully.', 'success');

            return redirect()->route('admin.credits.index');
        } catch (Exception $e) {
            flashMessage($e->getMessage(), 'error');

            return redirect()->route('admin.credits.index');
        }

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $credit = Credit::findOrFail($id);

        return view('backend.layouts.credits.create', compact('credit'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            if (User::find(auth()->user()->id)) {
                $validator = Validator::make($request->all(), [
                    'credit' => 'required|integer|min:1',
                    'amount' => 'required|numeric|min:0.01',
                ]);

                if ($validator->fails()) {
                    return redirect()->back()->withErrors($validator)->withInput();
                }

                $data = Credit::findOrFail($id);
                $data->credit = $request->credit;
                $data->amount = $request->amount;

                $data->save();
            }
            flashMessage('Credit updated successfully.', 'success');

            return redirect()->route('admin.credits.index');
        } catch (Exception $e) {
            flashMessage($e->getMessage(), 'error');

            return redirect()->route('admin.credits.index');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $data = Credit::findOrFail($id);

        if (! $data) {
            return response()->json([
                'success' => false,
                'message' => 'Credit not found.',
            ]);
        }

        $data->delete();

        return response()->json([
            'success' => true,
            'message' => 'Deleted successfully.',
        ]);
    }

    public function mostPopular(Request $request, Credit $credit)
    {
        try {

            if (! $credit->most_popular) {
                Credit::where('most_popular', true)
                    ->update(['most_popular' => false]);
                $credit->most_popular = true;

            } else {
                $credit->most_popular = false;
            }

            $credit->save();

            return response()->json([
                'success' => true,
                'message' => $credit->most_popular
                    ? 'Marked as most popular.'
                    : 'Removed from most popular.',
                'data' => $credit,
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update credit status.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
