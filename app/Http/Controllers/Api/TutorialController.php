<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tutorial;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class TutorialController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $data = Tutorial::where('is_active', true)->latest()->get();

        return $this->success($data, 'Tutorials List', 200);
    }

    public function show($id)
    {
        $tutorial = Tutorial::where('id', $id)->where('is_active', true)->first();

        if (! $tutorial) {
            return $this->success([], 'Tutorial Not Found', 200);
        }

        return $this->success($tutorial, 'Tutorial Details', 200);
    }
}
