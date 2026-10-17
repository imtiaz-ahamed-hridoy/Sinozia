<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    use ApiResponse;

    public function profile()
    {
        $user = User::with(['credits:id,user_id,credits'])->find(auth()->id());

        if (! $user) {
            return $this->error([], 'User not found', 404);
        }

        return $this->success($user, 'User Profile', 200);
    }

    public function updateProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:5120',
            'name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors(), $validator->errors()->first(), 422);
        }

        try {
            // Find the user by ID
            $user = auth()->user();

            // If user is not found, return an error response
            if (! $user) {
                return $this->error([], 'User Not Found', 404);
            }

            if ($request->hasFile('avatar')) {

                if ($user->avatar) {
                    $previousImagePath = public_path($user->avatar);
                    if (file_exists($previousImagePath)) {
                        unlink($previousImagePath);
                    }
                }

                $image = $request->file('avatar');
                $imageName = uploadImage($image, 'user/avatar');
            } else {
                $imageName = $user->avatar;
            }

            $user->name = $request->name;
            $user->avatar = $imageName;

            $user->save();

            return $this->success($user, 'User updated successfully', 200);
        } catch (\Exception $e) {
            return $this->error([], $e->getMessage(), 500);
        }
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return $this->success([], 'Logout successful', 200);
    }

    public function deleteAccount()
    {
        try {
            $user = auth()->user();

            if (! $user) {
                return $this->error([], 'User not found', 404);
            }

            // Delete the user's avatar if it exists
            if ($user->avatar) {
                $previousImagePath = public_path($user->avatar);
                if (file_exists($previousImagePath)) {
                    unlink($previousImagePath);
                }
            }

            // Delete the user
            $user->delete();

            return $this->success([], 'Account deleted successfully', 200);
        } catch (\Exception $e) {
            return $this->error([], $e->getMessage(), 500);
        }
    }

    /**
     * Change Login User Password
     *
     * @return JsonResponse JSON response with success or error.
     */
    public function passwordChange(Request $request)
    {
        // Get the authenticated user
        $user = auth()->user();

        // If user is not found, return an error response
        if (! $user) {
            return $this->error([], 'User Not Found', 404);
        }

        // Validate request inputs
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors(), $validator->errors()->first(), 422);
        }

        // Check if the current password matches
        if (! Hash::check($request->current_password, $user->password)) {
            return $this->error([], 'Current password is incorrect', 400);
        }

        // Update the password securely
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return $this->success($user->fresh(), 'Password changed successfully', 200);
    }
}
