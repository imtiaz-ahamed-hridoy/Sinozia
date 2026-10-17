<?php

namespace App\Http\Controllers\Api\System;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use App\Traits\ApiResponse;

class SystemSettingController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $setting = SystemSetting::latest('id')->first();

        if (! $setting) {
            return $this->success(null, 'No System Setting Found', 200);
        }

        return $this->success($setting, 'System Settings', 200);
    }
}
