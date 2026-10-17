<?php

namespace App\Services;

use App\Models\CreditLedger;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class CreditService
{
    public function getBalance(User $user): int
    {
        return $user->getCreditBalance();
    }

    public function deduct(User $user, int $amount, ?string $description = null, $reference = null): void
    {
        DB::transaction(function () use ($user, $amount, $description, $reference) {
            $fresh = $user?->credits()->lockForUpdate()->first() ?? $user->credits()->create(['credits' => 0]);

            if (($fresh->credits ?? 0) < $amount) {
                throw new RuntimeException('Insufficient credits.');
            }

            $fresh->decrement('credits', $amount);

            CreditLedger::create([
                'user_id' => $user->id,
                'amount' => -$amount,
                'type' => 'video_generation',
                'description' => $description,
                'reference_type' => $reference ? get_class($reference) : null,
                'reference_id' => $reference?->id,
            ]);
        });
    }

    public function grant(User $user, int $amount, string $type, string $description): void
    {
        DB::transaction(function () use ($user, $amount, $type, $description) {
            $fresh = $user?->credits()->lockForUpdate()->first() ?? $user->credits()->create(['credits' => 0]);
            $fresh->increment('credits', $amount);

            CreditLedger::create([
                'user_id' => $user->id,
                'amount' => $amount,
                'type' => $type,
                'description' => $description,
            ]);
        });
    }

    public function hasEnough(User $user, int $required): bool
    {
        return $user->getCreditBalance() >= $required;
    }
}
