<?php

namespace App\Http\Controllers\Web\Backend\Settings;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class EnvSettingController extends Controller
{
    /**
     * Sensitive keys that must be masked when displayed in UI.
     */
    protected array $sensitiveKeys = [
        'STRIPE_KEY',
        'STRIPE_PUBLIC_KEY',
        'STRIPE_SECRET',
        'STRIPE_SECRET_KEY',
        'STRIPE_WEBHOOK_SECRET',
        'MAIL_PASSWORD',
        'REVENUECAT_WEBHOOK_SECRET',
        'REVENUECAT_API_KEY',
        'GEMINI_API_KEY',
        'OPENAI_API_KEY',
        'ANTHROPIC_API_KEY',
        'NANOBANANA_API_KEY',
        'KLING_ACCESS_KEY',
        'KLING_SECRET_KEY',
        'ELEVENLABS_API_KEY',
        'PUSHER_APP_SECRET',
        'PUSHER_APP_KEY',
        'CLIENT_SECRET',
        'AWS_SECRET_ACCESS_KEY',
    ];

    /**
     * Display the ENV credentials and settings form.
     */
    public function index()
    {
        $envData = $this->readEnv();
        $maskedData = [];

        foreach ($envData as $key => $value) {
            if (in_array($key, $this->sensitiveKeys)) {
                $maskedData[$key] = $this->maskSecret($value);
            } else {
                $maskedData[$key] = $value;
            }
        }

        return view('backend.layouts.settings.env_settings', [
            'env' => $maskedData,
            'rawEnv' => $envData,
        ]);
    }

    /**
     * Update the ENV credentials.
     */
    public function update(Request $request)
    {
        $fields = $request->except(['_token']);
        $currentEnv = $this->readEnv();
        $updates = [];

        foreach ($fields as $key => $submittedValue) {
            // Check if this key is sensitive
            if (in_array($key, $this->sensitiveKeys)) {
                $originalValue = $currentEnv[$key] ?? '';
                $maskedOriginal = $this->maskSecret($originalValue);

                // If submitted value is empty or matches the masked representation, keep existing
                if ($submittedValue === null || $submittedValue === '' || $submittedValue === $maskedOriginal || str_starts_with($submittedValue, '••••')) {
                    continue;
                }
            }

            // Otherwise, set the new value (trim whitespace)
            $updates[$key] = is_null($submittedValue) ? '' : trim($submittedValue);
        }

        try {
            if (! empty($updates)) {
                $this->updateEnvFile($updates);
            }

            // Clear configuration & cache after update
            Artisan::call('config:clear');
            Artisan::call('cache:clear');

            flashMessage('Environment settings updated successfully.', 'success');

            return back();
        } catch (Exception $e) {
            flashMessage('Failed to update environment settings: '.$e->getMessage(), 'error');

            return back();
        }
    }

    /**
     * Clear all application caches and optimize.
     */
    public function clearCache()
    {
        try {
            // First run optimize:clear, then all individual clears as requested
            Artisan::call('optimize:clear');
            Artisan::call('config:clear');
            Artisan::call('cache:clear');
            Artisan::call('route:clear');
            Artisan::call('view:clear');

            flashMessage('All caches cleared and application optimized successfully!', 'success');

            return back();
        } catch (Exception $e) {
            flashMessage('Failed to clear caches: '.$e->getMessage(), 'error');

            return back();
        }
    }

    /**
     * Mask secret values showing only the last 5 characters.
     */
    protected function maskSecret(?string $value): string
    {
        if (empty($value)) {
            return '';
        }

        $len = strlen($value);
        if ($len <= 5) {
            return str_repeat('•', $len);
        }

        $lastDigits = substr($value, -5);
        $maskLength = max(10, $len - 5);

        return str_repeat('•', $maskLength).$lastDigits;
    }

    /**
     * Read key-value pairs from .env file.
     */
    protected function readEnv(): array
    {
        $path = base_path('.env');
        if (! File::exists($path)) {
            return [];
        }

        $content = File::get($path);
        $lines = explode("\n", $content);
        $data = [];

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line) || str_starts_with($line, '#')) {
                continue;
            }

            if (str_contains($line, '=')) {
                [$key, $val] = explode('=', $line, 2);
                $key = trim($key);
                $val = trim($val);

                // Strip quotes if wrapped
                if ((str_starts_with($val, '"') && str_ends_with($val, '"')) ||
                    (str_starts_with($val, "'") && str_ends_with($val, "'"))) {
                    $val = substr($val, 1, -1);
                }

                $data[$key] = $val;
            }
        }

        return $data;
    }

    /**
     * Update .env file content safely.
     */
    protected function updateEnvFile(array $updates): void
    {
        $path = base_path('.env');
        $content = File::exists($path) ? File::get($path) : '';

        foreach ($updates as $key => $value) {
            $formattedValue = $this->formatEnvValue($value);
            $pattern = "/^{$key}=.*/m";

            if (preg_match($pattern, $content)) {
                $content = preg_replace($pattern, "{$key}={$formattedValue}", $content);
            } else {
                $content .= "\n{$key}={$formattedValue}";
            }
        }

        File::put($path, $content);
    }

    /**
     * Format value for .env (wrap in quotes if spaces or special characters exist).
     */
    protected function formatEnvValue(string $value): string
    {
        if (str_contains($value, ' ') || str_contains($value, '#') || str_contains($value, '$') || str_contains($value, '&')) {
            return '"'.addcslashes($value, '"\\').'"';
        }

        return $value;
    }
}
