<?php

use App\Http\Controllers\Web\Frontend\PageController;
use App\Http\Controllers\Web\Frontend\ProfileDeletionController;
use App\Http\Controllers\Web\Frontend\ResetController;
use Illuminate\Support\Facades\Route;

// ! Route for Reset Database and Optimize Clear_______________________________________________________
Route::get('/reset', [ResetController::class, 'RunMigrations'])->name('reset');
Route::get('/composer', [ResetController::class, 'composer'])->name('composer');
Route::get('/migrate', [ResetController::class, 'migrate'])->name('migrate');
Route::get('/storage', [ResetController::class, 'storage'])->name('storage');

Route::get('/payment/success', function () {
    return view('frontend.layouts.pages.success');
})->name('payment.success');

Route::get('/payment/cancel', function () {
    return view('frontend.layouts.pages.cancel');
})->name('payment.cancel');

// Profile Deletion Routes________________________________________________________
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/profile-deletion', [ProfileDeletionController::class, 'showAuthenticated'])->name('profile.delete.authenticated');
    Route::delete('/profile-deletion', [ProfileDeletionController::class, 'destroyAuthenticated'])->name('profile.delete.authenticated.destroy');
});

// Dynamic Page
Route::get('/page/privacy-and-policy', [PageController::class, 'privacyAndPolicy'])->name('dynamicPage.privacyAndPolicy');
Route::get('/page/terms-service', [PageController::class, 'termsService'])->name('dynamicPage.termsService');
Route::get('/page/about-the-app', [PageController::class, 'aboutTheApp'])->name('dynamicPage.aboutTheApp');
