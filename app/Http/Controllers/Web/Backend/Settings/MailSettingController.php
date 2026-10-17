<?php

namespace App\Http\Controllers\Web\Backend\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class MailSettingController extends Controller
{
    public function index()
    {
        return view('backend.layouts.settings.mail_settings');
    }

    public function update(Request $request)
    {
        $request->validate([
            'mail_mailer' => 'nullable|string',
            'mail_host' => 'nullable|string',
            'mail_port' => 'nullable|string',
            'mail_username' => 'nullable|string',
            'mail_password' => 'nullable|string',
            'mail_encryption' => 'nullable|string',
            'mail_from_address' => 'nullable|string',
        ]);

        try {
            $envFile = base_path('.env');
            if (! File::exists($envFile)) {
                flashMessage('.env file not found. Please create one from .env.example.', 'error');

                return back();
            }
            $envContent = File::get($envFile);

            $replacements = [
                'MAIL_MAILER' => $request->mail_mailer,
                'MAIL_HOST' => $request->mail_host,
                'MAIL_PORT' => $request->mail_port,
                'MAIL_USERNAME' => $request->mail_username,
                'MAIL_PASSWORD' => $request->mail_password,
                'MAIL_ENCRYPTION' => $request->mail_encryption,
                'MAIL_FROM_ADDRESS' => $request->mail_from_address,
            ];

            foreach ($replacements as $key => $value) {
                $pattern = "/^{$key}=.*/m";
                $replacement = $key.'='.$value;
                $envContent = preg_replace($pattern, $replacement, $envContent);
            }

            File::put($envFile, $envContent);

            // Clear cache so Blade sees updated values immediately
            Artisan::call('config:clear');
            Artisan::call('cache:clear');
            flashMessage('Mail settings updated successfully.', 'success');

            return back();
        } catch (\Exception $e) {
            flashMessage('Failed to update mail settings.', 'error');

            return back();
        }
    }
}
