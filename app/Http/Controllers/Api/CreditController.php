<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Credit;
use App\Services\CreditService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class CreditController extends Controller
{
    use ApiResponse;

    public function __construct(private CreditService $credits) {}

    public function balance(Request $request)
    {
        $user = $request->user();

        return $this->success([
            'balance' => $this->credits->getBalance($user),
            'plan_type' => $user->plan_type,
            'is_premium' => $user->is_premium,
            'can_generate' => $user->canGenerate(),
            'video_cost' => $user->getPlanConfig()['video_cost'],
        ]);

    }

    public function index(Request $request)
    {

        $data = Credit::all();

        if ($data->isEmpty()) {
            return $this->success([], 'No Credits Found', 200);
        }

        return $this->success($data, 'Credits List', 200);
    }

    public function history(Request $request)
    {
        $perPage = (int) $request->query('per_page', 10);
        $history = $request->user()
            ->creditLedgers()
            ->latest()
            ->paginate($perPage);

        return $this->success($history, 'Credit history retrieved successfully.', 200);
    }
}
