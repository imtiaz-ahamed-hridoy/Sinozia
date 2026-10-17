<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use App\Traits\ApiResponse;

class FaqController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $faqs = Faq::where('is_active', true)->latest()->get();

        if ($faqs->isEmpty()) {
            return $this->success([], 'No FAQs Found', 200);
        }

        return $this->success($faqs, 'FAQs List', 200);
    }
}
