<?php

namespace App\Http\Controllers\Web\Backend\Settings;

use App\Http\Controllers\Controller;
use App\Models\DynamicPage;
use App\Models\User;
use App\Traits\ApiResponse;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Yajra\DataTables\DataTables;

class DynamicPageController extends Controller
{
    use ApiResponse;

    /**
     * Display a listing of the dynamic pages.
     *
     * @throws Exception
     */
    public function index(Request $request): View|JsonResponse
    {
        if ($request->ajax()) {
            try {
                $data = DynamicPage::latest();

                return DataTables::of($data)
                    ->addIndexColumn()
                    ->editColumn('page_content', function (DynamicPage $page) {
                        return e(Str::limit(strip_tags($page->page_content), 100));
                    })
                    ->addColumn('status', function (DynamicPage $page) {
                        $checked = $page->status === 'active' ? 'checked' : '';

                        return '
                            <a href="#" class="change_status d-inline-block"
                            data-id="'.$page->id.'"
                            data-enabled="'.($page->status === 'active' ? 'inactive' : 'active').'"
                            data-title="Do you want to '.($page->status === 'active' ? 'Deactivate' : 'Activate').' it?"
                            data-description="This will change the page visibility."
                            data-bs-toggle="modal" data-bs-target="#statusModal" style="text-decoration: none;">
                                <label class="switch mb-0" style="transform: scale(0.65); transform-origin: center center; margin: 0; vertical-align: middle;">
                                    <input type="checkbox" '.$checked.' onclick="return false;">
                                    <span class="switch-state" style="transition: background-color 0.3s ease;"></span>
                                </label>
                            </a>';
                    })
                    ->addColumn('action', function (DynamicPage $page) {
                        return view('components.action-buttons', [
                            'id' => $page->id,
                            'show' => 'dynamic_page.show',
                            'edit' => 'dynamic_page.edit',
                            'delete' => true,
                        ])->render();
                    })
                    ->rawColumns(['page_content', 'status', 'action'])
                    ->make(true);
            } catch (Exception $e) {
                return $this->error([
                    'message' => 'Failed to fetch data.',
                    'error' => $e->getMessage(),
                ], 'Error fetching data.', 500);
            }
        }

        return view('backend.layouts.settings.dynamic-pages.index');
    }

    /**
     * Show the form for creating a new dynamic page.
     */
    public function create(): View|RedirectResponse
    {
        if (User::find(auth()->user()->id)) {
            return view('backend.layouts.settings.dynamic-pages.create');
        }

        return redirect()->route('dynamic_page.index');
    }

    /**
     * Display the specified dynamic page.
     */
    public function show(int $id): View|RedirectResponse
    {
        if (User::find(auth()->user()->id)) {
            $page = DynamicPage::findOrFail($id);

            return view('backend.layouts.settings.dynamic-pages.show', compact('page'));
        }

        return redirect()->route('dynamic_page.index');
    }

    /**
     * Store a newly created dynamic page in the database.
     */
    public function store(Request $request): RedirectResponse
    {
        try {
            if (User::find(auth()->user()->id)) {
                $validator = Validator::make($request->all(), [
                    'page_title' => 'required|string',
                    'banner' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:20480',
                    'page_content' => 'required|string',
                ]);

                if ($validator->fails()) {
                    return redirect()->back()->withErrors($validator)->withInput();
                }

                $banner = null;
                if ($request->hasFile('banner')) {
                    $banner = uploadFile($request->file('banner'), 'uploads/dynamic_page');
                }

                $data = new DynamicPage;
                $data->page_title = $request->page_title;
                $data->page_slug = Str::slug($request->page_title);
                $data->page_content = $request->page_content;
                $data->banner = $banner;
                $data->save();
            }
            flashMessage('Dynamic Page created successfully.', 'success');

            return redirect()->route('dynamic_page.index');
        } catch (Exception $e) {
            flashMessage($e->getMessage(), 'error');

            return redirect()->route('dynamic_page.index');
        }
    }

    /**
     * Show the form for editing the specified dynamic page.
     */
    public function edit(int $id): View|RedirectResponse
    {
        if (User::find(auth()->user()->id)) {
            $page = DynamicPage::find($id);

            return view('backend.layouts.settings.dynamic-pages.create', compact('page'));
        }

        return redirect()->route('dynamic_page.index');
    }

    /**
     * Update the specified dynamic page in the database.
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        try {
            if (User::find(auth()->user()->id)) {
                $validator = Validator::make($request->all(), [
                    'page_title' => 'nullable|string',
                    'banner' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:20480',
                    'page_content' => 'nullable|string',
                ]);

                if ($validator->fails()) {
                    return redirect()->back()->withErrors($validator)->withInput();
                }

                $data = DynamicPage::findOrFail($id);

                $banner = $data->banner; // ← keep old banner by default

                if ($request->hasFile('banner')) {
                    deleteFile($data->banner);
                    $banner = uploadFile($request->file('banner'), 'uploads/dynamic_page');
                }

                $data->update([
                    'page_title' => $request->page_title,
                    'banner' => $banner,
                    'page_content' => $request->page_content,
                ]);

                flashMessage('Dynamic Page updated successfully.', 'success');

                return redirect()->route('dynamic_page.index');
            }
        } catch (Exception $e) {
            flashMessage('Dynamic Page failed updated.', 'error');

            return redirect()->route('dynamic_page.index');
        }

        return redirect()->route('dynamic_page.index');
    }

    /**
     * Change the status of the specified dynamic page.
     */
    public function status(int $id): JsonResponse
    {
        $data = DynamicPage::findOrFail($id);
        if ($data->status == 'active') {
            $data->status = 'inactive';
            $data->save();

            return response()->json([
                'success' => false,
                'message' => 'Unpublished Successfully.',
                'data' => $data,
            ]);
        } else {
            $data->status = 'active';
            $data->save();

            return response()->json([
                'success' => true,
                'message' => 'Published Successfully.',
                'data' => $data,
            ]);
        }
    }

    /**
     * Remove the specified dynamic page from the database.
     */
    public function destroy(int $id): JsonResponse
    {
        $page = DynamicPage::findOrFail($id);

        deleteFile($page->banner);
        $page->delete();

        return response()->json([
            'success' => true,
            'message' => 'Deleted successfully.',
        ]);
    }

    public function dynamicPageforApp($slug)
    {
        $page = DynamicPage::select(['page_title', 'page_slug', 'banner', 'page_content'])
            ->where('status', 'active')
            ->where('page_slug', $slug)
            ->first();

        if (! $page) {
            return $this->error(null, 'Page not found.', 404);
        }

        return $this->success([
            'banner' => $page->banner,
            'title' => $page->page_title,
            'content' => $page->page_content,
        ], 'Page retrieved successfully.');

    }
}
