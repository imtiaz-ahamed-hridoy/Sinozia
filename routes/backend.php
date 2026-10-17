<?php

use App\Http\Controllers\Web\Backend\CreditsController;
use App\Http\Controllers\Web\Backend\DashboardController;
use App\Http\Controllers\Web\Backend\Faq\FaqController;
use App\Http\Controllers\Web\Backend\GeneratedCharacterController;
use App\Http\Controllers\Web\Backend\TargetCharacterController;
use App\Http\Controllers\Web\Backend\TargetVoiceController;
use App\Http\Controllers\Web\Backend\TutorialController;
use App\Http\Controllers\Web\Backend\User\UsersController;
use Illuminate\Support\Facades\Route;

Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

Route::prefix('users')->name('admin.users.')->group(function () {
    Route::resource('/', UsersController::class)->parameter('', 'user');
    Route::patch('/{user}/status', [UsersController::class, 'changeStatus'])->name('status');
});

// FAQs Management
Route::prefix('faqs')->name('admin.faqs.')->group(function () {
    Route::resource('/', FaqController::class)->parameter('', 'faq');
    Route::patch('/status/{faq}', [FaqController::class, 'status'])->name('status');
});

// Tutorials Management
Route::prefix('tutorials')->name('admin.tutorials.')->group(function () {
    Route::resource('/', TutorialController::class)->parameter('', 'tutorial');
    Route::patch('/status/{tutorial}', [TutorialController::class, 'status'])->name('status');
});

// Target Characters Management
Route::prefix('target-characters')->name('admin.target-characters.')->group(function () {
    Route::resource('/', TargetCharacterController::class)->parameter('', 'target_character');
    Route::patch('/status/{target_character}', [TargetCharacterController::class, 'status'])->name('status');
});

// Target Voices Management
Route::prefix('target-voices')->name('admin.target-voices.')->group(function () {
    Route::resource('/', TargetVoiceController::class)->parameter('', 'target_voice');
    Route::patch('/status/{target_voice}', [TargetVoiceController::class, 'status'])->name('status');
});

// Credits Management
Route::prefix('credits')->name('admin.credits.')->group(function () {
    Route::resource('/', CreditsController::class)->parameter('', 'credit');
    Route::patch('/most-popular/{credit}', [CreditsController::class, 'mostPopular'])->name('most-popular');
});

// Generated Characters Management (Admin View — user-generated content)
Route::prefix('generated-characters')->name('admin.generated-characters.')->group(function () {
    Route::resource('/', GeneratedCharacterController::class)
        ->parameter('', 'generated_character')
        ->only(['index', 'show', 'destroy']);
    Route::patch('/status/{generated_character}', [GeneratedCharacterController::class, 'status'])->name('status');
});
