<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DynamicPage;
use App\Traits\ApiResponse;

class DynamicPageController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $data = DynamicPage::where('status', 'active')->get();

        if (empty($data)) {
            return $this->success([], 'No Dynamic Page Found', 200);
        }

        return $this->success($data, 'Dynamic Pages List', 200);
    }

    public function show($slug)
    {
        $data = DynamicPage::where('page_slug', $slug)->where('status', 'active')->first();

        if (empty($data)) {
            return $this->success([], 'No Dynamic Page Found', 200);
        }

        return $this->success($data, 'Dynamic Page Details', 200);
    }
}
