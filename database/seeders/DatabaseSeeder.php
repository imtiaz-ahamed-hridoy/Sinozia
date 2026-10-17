<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(UsersTableSeeder::class);
        $this->call(SystemSettingSeeder::class);
        $this->call(DynamicPageSeeder::class);
        $this->call(SocialMediaSeeder::class);
        $this->call(FaqsSeeder::class);
        $this->call(TutorialSeeder::class);
        $this->call(CreditsSeeder::class);
    }
}
