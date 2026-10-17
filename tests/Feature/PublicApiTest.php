<?php

use App\Models\DynamicPage;
use App\Models\Faq;
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
    Tutorial::create([
        'title' => 'Getting Started',
        'description' => 'Upload your video and select a character.',
        'is_active' => true,
    ]);

    $response = $this->getJson('/api/tutorials');
    $response->assertStatus(200);
});
