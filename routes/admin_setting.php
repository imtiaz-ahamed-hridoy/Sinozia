<?php

use App\Http\Controllers\Web\Backend\Settings\DynamicPageController;
use App\Http\Controllers\Web\Backend\Settings\EnvSettingController;
use App\Http\Controllers\Web\Backend\Settings\MailSettingController;
use App\Http\Controllers\Web\Backend\Settings\ProfileSettingController;
use App\Http\Controllers\Web\Backend\Settings\SocialMediaController;
use App\Http\Controllers\Web\Backend\Settings\SystemSettingController;
use Illuminate\Support\Facades\Route;

// ! This route is for updating the user's profile_______________________________________________________
Route::controller(ProfileSettingController::class)->group(function () {
    Route::post('/update-profile-picture', 'UpdateProfilePicture')->name('update.profile.picture');
    Route::post('/update-profile-password', 'UpdatePassword')->name('update.password');

    // ! Route for ProfileController
    Route::get('/profile', 'showProfile')->name('profile.setting');
    Route::post('/update-profile', 'UpdateProfile')->name('update.profile');
});
// ! End of ProfileController_______________________________________________________

// ! Route for SystemSettingController_______________________________________________________
Route::controller(SystemSettingController::class)->group(function () {
    Route::get('/system-setting', 'index')->name('system.index');
    Route::post('/system-setting', 'update')->name('system.update');
});
// ! End of SystemSettingController_______________________________________________________

// ! DynamicPageController_______________________________________________________
Route::controller(DynamicPageController::class)->group(function () {
    Route::get('/dynamic-page', 'index')->name('dynamic_page.index');
    Route::get('/dynamic-page/create', 'create')->name('dynamic_page.create');
    Route::post('/dynamic-page', 'store')->name('dynamic_page.store');
    Route::get('/dynamic-page/{id}/edit', 'edit')->name('dynamic_page.edit');
    Route::patch('/dynamic-page/{id}', 'update')->name('dynamic_page.update');
    Route::patch('/dynamic-page/{id}/status', 'status')->name('dynamic_page.status');
    Route::get('/dynamic-page/{id}', 'show')->name('dynamic_page.show');
    Route::delete('/dynamic-page/{id}', 'destroy')->name('dynamic_page.destroy');
});
// ! End of DynamicPageController_______________________________________________________

// ! Route for MailSettingsController_______________________________________________________
Route::controller(MailSettingController::class)->group(function () {
    Route::get('/mail-setting', 'index')->name('mail.index');
    Route::post('/mail-setting', 'update')->name('mail.update');
});
// ! End of MailSettingsController_______________________________________________________

// ! Route for SocialMediaController_______________________________________________________
Route::controller(SocialMediaController::class)->group(function () {
    Route::get('/social-media', 'index')->name('social.index');
    Route::post('/social-media', 'update')->name('social.update');
    Route::delete('/social-media/{id}', 'destroy')->name('social.delete');
});
// ! End of SocialMediaController_______________________________________________________

// ! Route for EnvSettingController_______________________________________________________
Route::controller(EnvSettingController::class)->group(function () {
    Route::get('/env-setting', 'index')->name('env.index');
    Route::post('/env-setting', 'update')->name('env.update');
    Route::post('/clear-cache', 'clearCache')->name('env.clearCache');
});
// ! End of EnvSettingController_______________________________________________________
