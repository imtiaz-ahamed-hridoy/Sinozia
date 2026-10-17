<?php

use App\Models\DynamicPage;
use App\Models\Faq;
use App\Models\SocialMedia;
use App\Models\SystemSetting;
use App\Models\Tutorial;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('public can retrieve dynamic pages', function () {
    DynamicPage::create([
        'page_title' => 'Privacy Policy',
        'page_slug' => 'privacy-policies',
        'page_content' => '<p>Privacy Content</p>',
        'status' => 'active',
    ]);

    $response = $this->getJson('/api/dynamic-pages');
    $response->assertStatus(200);

    $detailResponse = $this->getJson('/api/dynamic-pages/privacy-policies');
    $detailResponse->assertStatus(200);
});

test('public can retrieve active faqs', function () {
    Faq::create([
        'question' => 'How does Sinozia character swap work?',
        'answer' => 'It processes video frames through an AI vision pipeline.',
        'is_active' => true,
    ]);

    $response = $this->getJson('/api/faqs');
    $response->assertStatus(200);
});

test('public can retrieve active tutorials', function () {
    $tutorial = Tutorial::create([
        'title' => 'Getting Started',
        'description' => 'Upload your video and select a character.',
        'is_active' => true,
    ]);

    $response = $this->getJson('/api/tutorials');
    $response->assertStatus(200);

    $showResponse = $this->getJson("/api/tutorials/{$tutorial->id}");
    $showResponse->assertStatus(200);
});

test('public can retrieve system settings', function () {
    SystemSetting::create([
        'title' => 'Sinozia',
        'system_name' => 'Sinozia Platform',
        'email' => 'support@example.com',
    ]);

    $response = $this->getJson('/api/system-settings');
    $response->assertStatus(200)
        ->assertJsonPath('status', true);
});

test('public can retrieve social media links', function () {
    SocialMedia::create([
        'social_media' => 'Twitter',
        'profile_link' => 'https://twitter.com/example',
    ]);

    $response = $this->getJson('/api/social-media');
    $response->assertStatus(200)
        ->assertJsonPath('status', true);
});
