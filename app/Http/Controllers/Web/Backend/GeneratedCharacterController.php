<?php

namespace App\Http\Controllers\Web\Backend;

use App\Http\Controllers\Controller;
use App\Models\GeneratedCharacter;
use App\Traits\ApiResponse;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class GeneratedCharacterController extends Controller
{
    use ApiResponse;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            try {
                $data = GeneratedCharacter::with('user')->latest();

                return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('user', function (GeneratedCharacter $generatedCharacter) {
                        return $generatedCharacter->user?->name ?? 'N/A';
                    })
                    ->addColumn('attributes', function (GeneratedCharacter $generatedCharacter) {
                        $attrs = array_filter([
                            $generatedCharacter->age ? "Age: {$generatedCharacter->age}" : null,
                            $generatedCharacter->gender ? ucfirst($generatedCharacter->gender) : null,
                            $generatedCharacter->ethnicity ? ucfirst($generatedCharacter->ethnicity) : null,
                        ]);

                        return implode(', ', $attrs) ?: 'N/A';
                    })
                    ->addColumn('generated_image', function (GeneratedCharacter $generatedCharacter) {
                        if (! $generatedCharacter->generated_image_path) {
                            return 'No image';
                        }

                        return '<img src="'.asset('storage/'.$generatedCharacter->generated_image_path).'"
                                    width="80" height="80"
                                    style="object-fit: cover; border-radius: 6px;" />';
                    })
                    ->addColumn('status', function (GeneratedCharacter $generatedCharacter) {
                        $colors = [
                            'pending' => 'secondary',
                            'generating' => 'warning',
                            'completed' => 'success',
                            'failed' => 'danger',
                        ];
                        $color = $colors[$generatedCharacter->status] ?? 'secondary';

                        return '<span class="badge badge-'.$color.'">'.ucfirst($generatedCharacter->status).'</span>';
                    })
                    ->addColumn('is_active', function (GeneratedCharacter $generatedCharacter) {
                        $checked = $generatedCharacter->is_active ? 'checked="checked"' : '';

                        return '
                            <a href="#" class="change_status d-inline-block"
                            data-id="'.$generatedCharacter->id.'"
                            data-enabled="'.($generatedCharacter->is_active ? '0' : '1').'"
                            data-title="Do you want to '.($generatedCharacter->is_active ? 'Deactivate' : 'Activate').' it?"
                            data-description="This will change the Generated Character status."
                            data-bs-toggle="modal" data-bs-target="#statusModal" style="text-decoration: none;">
                                <label class="switch mb-0" style="transform: scale(0.65); transform-origin: center center; margin: 0; vertical-align: middle;">
                                    <input type="checkbox" '.$checked.' onclick="return false;">
                                    <span class="switch-state" style="transition: background-color 0.3s ease;"></span>
                                </label>
                            </a>';
                    })
                    ->addColumn('action', function (GeneratedCharacter $generatedCharacter) {
                        return view('components.action-buttons', [
                            'id' => $generatedCharacter->id,
                            'show' => 'admin.generated-characters.show',
                            'delete' => true,
                        ])->render();
                    })
                    ->rawColumns(['generated_image', 'status', 'is_active', 'action'])
                    ->make(true);
            } catch (Exception $e) {
                return $this->error([
                    'message' => 'Failed to fetch data.',
                    'error' => $e->getMessage(),
                ], 'Error fetching data.', 500);
            }
        }

        return view('backend.layouts.generated-characters.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $generatedCharacter = GeneratedCharacter::with('user')->findOrFail($id);

        return view('backend.layouts.generated-characters.show', compact('generatedCharacter'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $generatedCharacter = GeneratedCharacter::findOrFail($id);

        if ($generatedCharacter->generated_image_path) {
            Storage::disk('public')->delete($generatedCharacter->generated_image_path);
        }

        $generatedCharacter->delete();

        return response()->json([
            'success' => true,
            'message' => 'Deleted successfully.',
        ]);
    }

    /**
     * Toggle is_active status — same AJAX pattern as TargetCharacterController.
     */
    public function status(Request $request, GeneratedCharacter $generatedCharacter)
    {
        try {
            $generatedCharacter->is_active = ! $generatedCharacter->is_active;
            $generatedCharacter->save();

            return response()->json([
                'success' => true,
                'message' => $generatedCharacter->is_active ? 'Activated Successfully.' : 'Deactivated Successfully.',
                'data' => $generatedCharacter,
            ]);
        } catch (Exception $e) {
            return $this->error([
                'message' => 'Failed to update status.',
                'error' => $e->getMessage(),
            ], 'Error updating status', 500);
        }
    }
}
