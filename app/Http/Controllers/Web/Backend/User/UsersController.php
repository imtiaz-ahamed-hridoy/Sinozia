<?php

namespace App\Http\Controllers\Web\Backend\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\ApiResponse;
use Exception;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class UsersController extends Controller
{
    use ApiResponse;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            try {
                $data = User::where('role', '!=', 'admin')->latest();

                return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('email_verified_at', function (User $user) {
                        return $user->email_verified_at ? $user->email_verified_at->format('d M Y, h:i A') : 'Not Verified';
                    })
                    ->addColumn('created_at', function (User $user) {
                        return $user->created_at->format('d M Y, h:i A');
                    })
                    ->addColumn('credits', function (User $user) {
                        $credits = optional($user->credits)->credits ?? 0;
                        $badgeClass = $credits < 1 ? 'bg-danger' : 'bg-success';

                        return '<span class="badge '.$badgeClass.'">'.$credits.' Credit</span>';
                    })
                    ->addColumn('role', function (User $user) {
                        if ($user->role === 'admin') {
                            return '<span class="badge bg-primary">Admin</span>';
                        } else {
                            return '<span class="badge bg-secondary">User</span>';
                        }
                    })
                    ->addColumn('avatar', function (User $user) {
                        if ($user->avatar) {
                            $url = asset($user->avatar);

                            return '<img src="'.$url.'" alt="'.$user->name.'" class="b-r-25 img-50 img-fluid profile-picture">';
                        }

                        $initial = $user->name ? mb_substr(trim($user->name), 0, 1) : '';

                        return '<div class="profile-picture d-flex align-items-center justify-content-center" style="background: #8A60FF; border-radius: 50%; color: #fff; font-weight: 700; font-size: 18px; width: 50px; height: 50px;">'.e($initial).'</div>';
                    })
                    ->addColumn('is_active', function (User $user) {
                        $checked = $user->is_active ? 'checked="checked"' : '';

                        return '
                            <a href="#" class="change_status d-inline-block"
                            data-id="'.$user->id.'"
                            data-enabled="'.($user->is_active ? '0' : '1').'"
                            data-title="Do you want to '.($user->is_active ? 'Deactivate' : 'Activate').' it?"
                            data-description="This will change the user status."
                            data-bs-toggle="modal" data-bs-target="#statusModal" style="text-decoration: none;">
                                <label class="switch mb-0" style="transform: scale(0.65); transform-origin: center center; margin: 0; vertical-align: middle;">
                                    <input type="checkbox" '.$checked.' onclick="return false;">
                                    <span class="switch-state" style="transition: background-color 0.3s ease;"></span>
                                </label>
                            </a>';
                    })
                    ->addColumn('action', function (User $user) {
                        return view('components.action-buttons', [
                            'id' => $user->id,
                            'show' => 'admin.users.show',
                            'delete' => true,
                        ])->render();
                    })
                    ->rawColumns(['email_verified_at', 'created_at', 'role', 'avatar', 'is_active', 'action', 'credits'])
                    ->make(true);
            } catch (Exception $e) {
                return $this->error([
                    'message' => 'Failed to fetch data.',
                    'error' => $e->getMessage(),
                ], 'Error fetching data.', 500);
            }
        }

        return view('backend.layouts.users.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::with(['credits', 'paymentHistories'])->findOrFail($id);

        return view('backend.layouts.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);

        deleteFile($user->avatar);
        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'Deleted successfully.',
        ]);
    }

    public function changeStatus(Request $request, User $user)
    {
        try {
            $user->is_active = ! $user->is_active;
            $user->save();

            return response()->json([
                'success' => true,
                'message' => $user->is_active ? 'Activated Successfully.' : 'Deactivated Successfully.',
                'data' => $user,
            ]);
        } catch (Exception $e) {
            return $this->error([
                'message' => 'Failed to update user status.',
                'error' => $e->getMessage(),
            ], 'Error updating status', 500);
        }
    }
}
