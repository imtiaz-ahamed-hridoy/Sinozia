<?php

namespace App\Http\Controllers\Api\System;

use App\Http\Controllers\Controller;
use App\Models\SocialMedia;
use App\Traits\ApiResponse;

class SocialMediaController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $data = SocialMedia::all();

        if (empty($data)) {
            return $this->success('No Social Media Links Found', 200);
        }

        return $this->success($data, 'Social Media Links', 200);
    }
}
