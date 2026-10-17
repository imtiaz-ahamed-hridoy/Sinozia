<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RevenueCatEvent;
use App\Models\Subscription;
use App\Models\User;
use App\Traits\ApiResponse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RevenueCatWebhookController extends Controller
{
    use ApiResponse;

    public function handle(Request $request)
    {
        if (! $this->isAuthorized($request)) {
            Log::warning('RevenueCat webhook unauthorized request');

            return $this->error([], 'Unauthorized', 401);
        }

        $payload = $request->all();
        $event = $payload['event'] ?? null;

        if (! $event || empty($event['id'])) {
            return $this->error([], 'Invalid event payload', 400);
        }

        Log::info('RevenueCat webhook received', [
            'event_id' => $event['id'],
            'event_type' => $event['type'] ?? null,
        ]);

        if ($this->isDuplicateEvent($event['id'])) {
            Log::info('Duplicate RevenueCat event ignored', ['event_id' => $event['id']]);

            return $this->success([], 'Duplicate ignored', 200);
        }

        DB::beginTransaction();
        try {
            $this->storeEvent($event, $payload);
            $user = $this->resolveUser($event);

            if (! $user) {
                Log::warning('RevenueCat webhook user not found', [
                    'app_user_id' => $event['app_user_id'] ?? null,
                ]);
                DB::rollBack();

                return $this->error([], 'User not found', 404);
            }

            $this->updateSubscription($user, $event);
            $this->updateUserPremiumStatus($user, $event);

            DB::commit();

            return $this->success([], 'Webhook processed successfully');

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('RevenueCat webhook processing failed', [
                'error' => $e->getMessage(),
                'event' => $event,
            ]);

            return $this->error([], 'Webhook processing failed', 500);
        }
    }

    private function isAuthorized(Request $request): bool
    {
        $authHeader = $request->header('Authorization');
        if (! $authHeader) {
            return false;
        }

        $receivedSecret = str_starts_with($authHeader, 'Bearer ') ? trim(str_replace('Bearer', '', $authHeader)) : $authHeader;
        $expectedSecret = config('services.revenuecat.webhook_secret');

        if (! is_string($expectedSecret) || $expectedSecret === '') {
            return false;
        }

        return hash_equals($expectedSecret, $receivedSecret);
    }

    private function isDuplicateEvent(string $eventId): bool
    {
        return RevenueCatEvent::where('rc_event_id', $eventId)->exists();
    }

    private function storeEvent(array $event, array $payload): void
    {
        RevenueCatEvent::create([
            'rc_event_id' => $event['id'],
            'app_id' => $event['app_id'] ?? null,
            'event_type' => $event['type'] ?? null,
            'payload' => $payload,
        ]);
    }

    private function resolveUser(array $event): ?User
    {
        $userId = $event['app_user_id'] ?? null;
        if (! $userId) {
            return null;
        }

        if (is_numeric($userId)) {
            return User::find($userId) ?? User::where('email', $userId)->first();
        }

        return User::where('email', $userId)->first() ?? User::find($userId);
    }

    private function updateSubscription(User $user, array $event): void
    {
        $purchaseDate = $this->parseDate($event['purchased_at_ms'] ?? null);
        $expiresDate = $this->parseDate($event['expiration_at_ms'] ?? null);
        $transactionId = $event['original_transaction_id'] ?? $event['transaction_id'] ?? null;

        Subscription::updateOrCreate(
            ['original_transaction_id' => $transactionId],
            [
                'user_id' => $user->id,
                'product_id' => $event['product_id'] ?? null,
                'entitlement' => $event['entitlement_id'] ?? null,
                'status' => $this->mapStatus($event['type'] ?? ''),
                'price' => $event['price'] ?? null,
                'currency' => $event['currency'] ?? null,
                'price_in_purchased_currency' => $event['price_in_purchased_currency'] ?? null,
                'purchase_date' => $purchaseDate,
                'expires_date' => $expiresDate,
                'environment' => $event['environment'] ?? null,
            ]
        );
    }

    private function updateUserPremiumStatus(User $user, array $event): void
    {
        $status = $this->mapStatus($event['type'] ?? '');

        $planType = $this->mapProductIdToPlan($event['product_id'] ?? '');
        if ($status === 'active') {
            $user->update(['is_premium' => true, 'plan_type' => $planType]);
        }

        if (in_array($status, ['expired', 'cancelled'])) {
            $user->update(['is_premium' => false, 'plan_type' => 'free']);
        }
    }

    private function parseDate(?int $timestamp): ?Carbon
    {
        return $timestamp ? Carbon::createFromTimestampMs($timestamp) : null;
    }

    private function mapStatus(string $eventType): string
    {
        return match ($eventType) {
            'INITIAL_PURCHASE', 'RENEWAL' => 'active',
            'CANCELLATION' => 'cancelled',
            'EXPIRATION' => 'expired',
            default => 'unknown',
        };
    }

    private function mapProductIdToPlan(string $productId): string
    {
        // Map your RevenueCat product IDs to plan_type values
        // Adjust these to match your actual RevenueCat product IDs
        return match (true) {
            str_contains($productId, 'standard') => 'standard',
            str_contains($productId, 'pro') => 'pro',
            str_contains($productId, 'unlimited') => 'unlimited',
            default => 'free',
        };
    }
}
