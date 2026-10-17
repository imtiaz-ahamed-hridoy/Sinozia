<?php

namespace App\Http\Controllers\Web\Backend\Settings;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SystemSettingController extends Controller
{
    public function index()
    {

        $setting = SystemSetting::latest('id')->first();

        return view('backend.layouts.settings.system_settings', compact('setting'));
    }

    /**
     * Update the system settings.
     *
     * @return RedirectResponse
     */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'system_name' => 'nullable|string',
            'email' => 'required|email',
            'copyright_text' => 'nullable|string',
            'logo' => 'nullable|file|mimes:png,jpg,jpeg,svg|max:5120',
            'logo_dark' => 'nullable|file|mimes:png,jpg,jpeg,svg|max:5120',
            'favicon' => 'nullable|file|mimes:png,jpg,jpeg,svg|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $data = SystemSetting::first();
        try {
            $setting = SystemSetting::firstOrNew();
            $setting->system_name = $request->system_name;
            $setting->email = $request->email;
            $setting->copyright_text = $request->copyright_text;
            $setting->logo = $request->logo;
            $setting->logo_dark = $request->logo_dark;
            $setting->favicon = $request->favicon;

            if ($request->hasFile('logo')) {
                $setting->logo = uploadImage($request->file('logo'), 'logos');

                if ($data?->logo) {
                    $previousImagePath = public_path($data->logo);
                    if (file_exists($previousImagePath)) {
                        unlink($previousImagePath);
                    }
                }
            } else {
                $setting->logo = $data?->logo;
            }

            if ($request->hasFile('logo_dark')) {
                $setting->logo_dark = uploadImage($request->file('logo_dark'), 'logos');

                if ($data?->logo_dark) {
                    $previousImagePath = public_path($data->logo_dark);
                    if (file_exists($previousImagePath)) {
                        unlink($previousImagePath);
                    }
                }
            } else {
                $setting->logo_dark = $data?->logo_dark;
            }

            if ($request->hasFile('favicon')) {
                $setting->favicon = uploadImage($request->file('favicon'), 'favicons');

                if ($data?->favicon) {
                    $previousImagePath = public_path($data->favicon);
                    if (file_exists($previousImagePath)) {
                        unlink($previousImagePath);
                    }
                }
            } else {
                $setting->favicon = $data?->favicon;
            }

            $setting->save();

            flashMessage('System settings updated successfully.', 'success');

            return back();
        } catch (Exception) {
            flashMessage('An error occurred while updating system settings.', 'error');

            return back();
        }
    }
}
