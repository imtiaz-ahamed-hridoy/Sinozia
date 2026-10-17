<?php

use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\CreditController;
use App\Http\Controllers\Api\CreditPurchaseController;
use App\Http\Controllers\Api\DynamicPageController;
use App\Http\Controllers\Api\FaqController;
use App\Http\Controllers\Api\RevenueCatWebhookController;
use App\Http\Controllers\Api\System\SocialMediaController;
use App\Http\Controllers\Api\System\SystemSettingController;
use App\Http\Controllers\Api\TutorialController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

// Register API
Route::middleware('throttle:5,1')->controller(RegisterController::class)->prefix('users')->group(function () {
    // User Register
    Route::post('/register', 'Register');
    // Resend OTP
    Route::post('/resend-otp', 'resendOtp');
    // Verify OTP
    Route::post('/verify-otp', 'verifyOtp');

});

// Login API
Route::middleware('throttle:5,1')->controller(LoginController::class)->prefix('users')->group(function () {
    // User Login
    Route::post('/login', 'Login');
    // Verify Email
    Route::post('/email-verify', 'emailVerify');
    // Resend OTP
    Route::post('/otp-resend', 'otpResend');
    // Verify OTP
    Route::post('/otp-verify', 'otpVerify');
    // Reset Password
    Route::post('/reset-password', 'resetPassword');
});

Route::post('/revenuecat/webhook', [RevenueCatWebhookController::class, 'handle']);
// User Profile
Route::middleware(['auth:sanctum', 'enabled'])->group(function () {
    Route::controller(UserController::class)->prefix('user')->group(function () {
        Route::get('/profile', 'profile');
        Route::post('/update-profile', 'updateProfile');
        Route::post('/logout', 'logout');
        Route::post('/change-password', 'passwordChange');
        Route::delete('/delete-account', 'deleteAccount');
    });

    // Credits
    Route::controller(CreditController::class)->prefix('credit')->group(function () {
        Route::get('/', 'index');
    });

    // Credits Purchase
    Route::controller(CreditPurchaseController::class)->prefix('credits')->group(function () {
        Route::post('/purchase', 'purchaseCredits');
    });
});

Route::controller(CreditPurchaseController::class)->group(function () {
    Route::get('/purchase-success', 'success')->name('purchase.success');
    Route::get('/purchase-cancel', 'cancel')->name('purchase.cancel');
});

// Dynamic Pages
Route::controller(DynamicPageController::class)->prefix('dynamic-pages')->group(function () {
    Route::get('/', 'index');
    Route::get('/{slug}', 'show');
});

// System Settings
Route::controller(SystemSettingController::class)->prefix('system-settings')->group(function () {
    Route::get('/', 'index');
});

// Social Media Links
Route::controller(SocialMediaController::class)->prefix('social-media')->group(function () {
    Route::get('/', 'index');
});

// FAQs
Route::controller(FaqController::class)->prefix('faqs')->group(function () {
    Route::get('/', 'index');
});

// Tutorials
Route::controller(TutorialController::class)->prefix('tutorials')->group(function () {
    Route::get('/', 'index');
    Route::get('/{id}', 'show');
});
