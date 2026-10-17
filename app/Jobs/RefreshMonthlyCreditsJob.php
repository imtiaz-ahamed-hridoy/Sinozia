<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\CreditService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class RefreshMonthlyCreditsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $timeout = 300;

    public function handle(CreditService $credits): void
    {
        // Only refresh premium users with active subscriptions
        User::where('is_premium', true)
            ->where('is_active', true)
        // ->whereHas('activeSubscription')
            ->chunkById(100, function ($users) use ($credits) {
                foreach ($users as $user) {
                    try {
                        $monthlyCredits = $user->getPlanConfig()['monthly_credits'] ?? 0;
                        $credits->grant($user, $monthlyCredits, 'monthly_subscription', 'Monthly subscription credits');
                        Log::info("Monthly credits refreshed for user {$user->id}");
                    } catch (\Throwable $e) {
                        Log::error("Failed to refresh credits for user {$user->id}: ".$e->getMessage());
                    }
                }
            });
    }
}
