<?php

namespace Database\Seeders;

use App\Models\DynamicPage;
use Illuminate\Database\Seeder;

class DynamicPageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DynamicPage::insert([
            [
                'page_title' => 'Terms of Service',
                'page_slug' => 'terms-service',
                'banner' => null,
                'page_content' => '<p>1. Introduction</p><p>Welcome to Sinozia. By accessing or using our application, media processing services, and generative character features, you agree to comply with and be bound by these Terms of Service.</p><p><br></p><p>2. Acceptable Use</p><p>Users agree to upload only content for which they hold appropriate rights. Processing unauthorized likenesses, harmful materials, or violating intellectual property rights is strictly prohibited.</p><p><br></p><p>3. Service Availability & Credits</p><p>Video generation and AI synthesis jobs consume credits as determined by your subscription plan. Unused credits expire according to your plan rules.</p>',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'page_title' => 'Privacy Policies',
                'page_slug' => 'privacy-policies',
                'banner' => null,
                'page_content' => '<p>1. Privacy Overview</p><p>Sinozia respects your privacy. Media uploaded for transformation is processed securely via automated background pipelines.</p><p><br></p><p>2. Data Retention & Media Processing</p><p>Uploaded frames, temporary audio, and intermediate synthesis files are strictly retained for the duration required to complete video rendering and user preview validation.</p><p><br></p><p>3. Your Rights</p><p>You may request full account deletion and purge of all associated media and character generation history at any time through the profile management interface.</p>',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'page_title' => 'About the app',
                'page_slug' => 'about-the-app',
                'banner' => null,
                'page_content' => '<p>Sinozia is an enterprise-grade AI-powered media transformation platform enabling seamless character swaps, multi-model vision synthesis, and voice transformations on personal videos through an asynchronous job orchestration pipeline.</p>',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
