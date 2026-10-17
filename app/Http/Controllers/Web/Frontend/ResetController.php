<?php

namespace App\Http\Controllers\Web\Frontend;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Artisan;

class ResetController extends Controller
{
    use ApiResponse;

    /**
     * Ensure development utility endpoints can only ever execute in local environment.
     */
    private function ensureLocal(): void
    {
        if (! app()->environment('local')) {
            abort(403, 'Development maintenance endpoints are restricted to local environments.');
        }
    }

    /**
     * Reset Database and Optimize Clear (Local dev only)
     */
    public function RunMigrations(): JsonResponse
    {
        $this->ensureLocal();

        Artisan::call('migrate:fresh --seed');
        Artisan::call('optimize:clear');

        return $this->success([], 'Migrations have been Refreshed, Seeded, and Cache Cleared!', 200);
    }

    /**
     * Run Composer Update (Local dev only)
     */
    public function composer(): JsonResponse
    {
        $this->ensureLocal();

        $output = [];
        $resultCode = 0;

        $composerCommand = 'cd '.base_path().' && composer update';

        exec($composerCommand, $output, $resultCode);

        if ($resultCode === 0) {
            return $this->success($output, 'Composer update completed successfully', 200);
        }

        return $this->error($output, 'Failed to run composer update', 500);
    }

    /**
     * Run Migrations and Optimize Clear (Local dev only)
     */
    public function migrate(): JsonResponse
    {
        $this->ensureLocal();

        Artisan::call('migrate');
        Artisan::call('optimize:clear');

        return $this->success([], 'Migrations Run and Cache Cleared!', 200);
    }

    /**
     * Create a storage link and clear the cache (Local dev only)
     */
    public function storage(): JsonResponse
    {
        $this->ensureLocal();

        $linkPath = public_path('storage');

        if (file_exists($linkPath) && is_link($linkPath)) {
            unlink($linkPath);
        }

        Artisan::call('storage:link');
        Artisan::call('cache:clear');

        return $this->success([], 'Storage Link Successful and Cache Cleared!', 200);
    }
}
