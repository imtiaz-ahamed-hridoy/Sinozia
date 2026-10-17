<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Credit;
use App\Services\StripeService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CreditPurchaseController extends Controller
{
    use ApiResponse;

    protected $stripeService;

    public function __construct(StripeService $stripeService)
    {
        $this->stripeService = $stripeService;
    }

    public function purchaseCredits(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:credits,id',
        ]);

        if ($validator->fails()) {
            return $this->error([], $validator->errors()->first(), 422);
        }

        $user = auth()->user();

        if (! $user) {
            return $this->error([], 'Unauthorized', 401);
        }

        $credit = Credit::find($request->id);

        try {
            $session = $this->stripeService->createCheckoutSession($user, $credit);

            return response()->json([
                'success' => true,
                'url' => $session->url,
            ]);

        } catch (\Exception $e) {
            return $this->error([], $e->getMessage(), 500);
        }
    }

    public function success(Request $request)
    {
        try {
            $sessionId = $request->get('session_id');

            if (! $sessionId) {
                return $this->error([], 'Session ID missing', 400);
            }

            $this->stripeService->handleSuccess($sessionId);

            return redirect('/payment/success');

        } catch (\Exception $e) {
            return $this->error([], $e->getMessage(), 500);
        }
    }

    public function cancel(Request $request)
    {
        $redirectUrl = $request->get('redirect_url');

        if ($redirectUrl) {
            return redirect($redirectUrl);
        }

        return redirect('/payment/cancel');
    }
}
