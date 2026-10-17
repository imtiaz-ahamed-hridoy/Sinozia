<?php

namespace Database\Seeders;

use App\Models\SystemSetting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class SystemSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SystemSetting::insert([
            [
                'id' => 1,
                'system_name' => 'Zono Admin Dashboard',
                'email' => 'support@example.com',
                'copyright_text' => 'Zono Admin Dashboard',
                'logo' => 'backend/assets/images/logo/logo.png',
                'logo_dark' => 'backend/assets/images/logo/logo.png',
                'favicon' => 'backend/assets/images/logo/logo.png',
                'created_at' => Carbon::now(),
            ],
        ]);
    }
}
