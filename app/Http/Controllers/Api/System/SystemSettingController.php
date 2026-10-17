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

        if (empty($setting)) {
            return $this->success('No System Setting Found', 200);
        }

        return $this->success($setting, 'System Settings', 200);
    }
}
