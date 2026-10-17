<?php

namespace App\Http\Controllers\Web\Backend\Settings;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ProfileSettingController extends Controller
{
    /**
     * Display the profile settings page.
     */
    public function showProfile()
    {
        $userDetails = Auth::user();

        return view('backend.layouts.settings.profile_settings', compact('userDetails'));
    }

    public function UpdateProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'nullable|max:100|min:2',
            'email' => 'nullable|email|unique:users,email,'.auth()->user()->id,
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        try {
            $user = User::find(auth()->user()->id);
            $user->name = $request->name;
            $user->email = $request->email;

            $user->save();
            flashMessage('Profile updated successfully.', 'success');

            return redirect()->back();
        } catch (Exception $e) {
            flashMessage('An error occurred while updating the profile.', 'error');

            return redirect()->back();
        }
    }

    public function UpdateProfilePicture(Request $request)
    {
        try {
            $request->validate([
                'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg|max:5120', // Max size 5MB
            ]);

            $user = Auth::user();

            if ($user->avatar) {
                deleteFile($user->avatar);
            }

            if ($request->hasFile('profile_picture')) {
                $image = $request->file('profile_picture');
                $imageName = uploadImage($image, 'users');
                $user->avatar = $imageName;

                $user->save();
            }

            return response()->json([
                'success' => true,
                'image_url' => asset($user->avatar),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while uploading the profile picture.',
            ]);
        }
    }

    /**
     * Update the user's password.
     *
     * @return RedirectResponse
     */
    public function UpdatePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required',
            'new_password' => 'required|confirmed|min:8',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        try {
            $user = Auth::user();
            if (Hash::check($request->password, $user->password)) {
                $user->password = Hash::make($request->new_password);
                $user->save();

                flashMessage('Password updated successfully.', 'success');

                return redirect()->back();
            } else {
                flashMessage('Current password is incorrect.', 'error');

                return redirect()->back();
            }
        } catch (Exception $e) {
            flashMessage('Something went wrong.', 'error');

            return redirect()->back();
        }
    }
}
