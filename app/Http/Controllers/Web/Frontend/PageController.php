<?php

namespace App\Http\Controllers\Web\Frontend;

use App\Http\Controllers\Controller;
use App\Models\DynamicPage;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function privacyAndPolicy()
    {
        $dynamicPage = DynamicPage::query()
            ->where('status', 'active')
            ->where(function ($q) {
                $q->where('page_slug', 'like', 'privacy%')
                    ->orWhere('id', 2);
            })
            ->firstOrFail();

        return view('frontend.layouts.pages.singleDynamicPage', compact('dynamicPage'));
    }

    public function termsService(Request $request)
    {
        $dynamicPage = DynamicPage::query()
            ->where('status', 'active')
            ->where(function ($q) {
                $q->where('page_slug', 'like', 'terms%')
                    ->orWhere('id', 1);
            })
            ->firstOrFail();

        return view('frontend.layouts.pages.singleDynamicPage', compact('dynamicPage'));
    }

    public function aboutTheApp(Request $request)
    {
        $dynamicPage = DynamicPage::query()
            ->where('status', 'active')
            ->where(function ($q) {
                $q->where('page_slug', 'like', 'about%')
                    ->orWhere('id', 3);
            })
            ->firstOrFail();

        return view('frontend.layouts.pages.singleDynamicPage', compact('dynamicPage'));
    }
}
