<?php

namespace Database\Seeders;

use App\Models\SocialMedia;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class SocialMediaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SocialMedia::insert([
            [
                'social_media' => 'facebook',
                'profile_link' => 'https://www.facebook.com/',
                'created_at' => Carbon::now(),
            ],
            [
                'social_media' => 'instagram',
                'profile_link' => 'https://www.instagram.com/',
                'created_at' => Carbon::now(),
            ],
            [
                'social_media' => 'twitter',
                'profile_link' => 'https://www.twitter.com/',
                'created_at' => Carbon::now(),
            ],
        ]);
    }
}
