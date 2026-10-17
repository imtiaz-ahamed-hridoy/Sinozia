<?php

use App\Mail\ForgotPasswordOtp;
use App\Mail\RegistationOtp;
use App\Models\EmailOtp;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

test('guest user can register via api and receives otp', function () {
    Mail::fake();

    $response = $this->postJson('/api/users/register', [
        'name' => 'John Doe',
        'email' => 'johndoe@example.com',
        'password' => 'SecretPassword123!',
        'password_confirmation' => 'SecretPassword123!',
    ]);

    $response->assertStatus(201)
        ->assertJsonPath('status', true);

    $this->assertDatabaseHas('users', [
        'email' => 'johndoe@example.com',
    ]);

    Mail::assertSent(RegistationOtp::class);
});

test('user can verify registration otp and obtain sanctum token', function () {
    $user = User::factory()->create([
        'email_verified_at' => null,
    ]);

    EmailOtp::create([
        'user_id' => $user->id,
        'verification_code' => 1234,
        'expires_at' => Carbon::now()->addMinutes(5),
    ]);

    $response = $this->postJson('/api/users/verify-otp', [
        'email' => $user->email,
        'otp' => 1234,
    ]);

    $response->assertStatus(200)
        ->assertJsonPath('status', true);

    expect($response->json('data.token'))->not->toBeNull();

    $this->assertNotNull($user->fresh()->email_verified_at);
});

test('unverified user logging in receives verification otp instead of token', function () {
    Mail::fake();

    $user = User::factory()->create([
        'password' => Hash::make('SecretPassword123!'),
        'email_verified_at' => null,
    ]);

    $response = $this->postJson('/api/users/login', [
        'email' => $user->email,
        'password' => 'SecretPassword123!',
    ]);

    $response->assertStatus(200)
        ->assertJsonPath('status', true)
        ->assertJsonPath('message', 'OTP sent successfully');

    expect($response->json('data.token'))->toBeNull();
    Mail::assertSent(RegistationOtp::class);
});

test('verified user logging in receives valid sanctum token', function () {
    $user = User::factory()->create([
        'password' => Hash::make('SecretPassword123!'),
        'email_verified_at' => now(),
    ]);

    $response = $this->postJson('/api/users/login', [
        'email' => $user->email,
        'password' => 'SecretPassword123!',
    ]);

    $response->assertStatus(200)
        ->assertJsonPath('status', true);

    expect($response->json('data.token'))->not->toBeNull();
});

test('user can request password reset otp and reset password successfully', function () {
    Mail::fake();

    $user = User::factory()->create([
        'password' => Hash::make('OldPassword123!'),
        'email_verified_at' => now(),
    ]);

    // 1. Request OTP
    $emailVerifyResponse = $this->postJson('/api/users/email-verify', [
        'email' => $user->email,
    ]);

    $emailVerifyResponse->assertStatus(200);
    Mail::assertSent(ForgotPasswordOtp::class);

    $otpRecord = EmailOtp::where('user_id', $user->id)->first();
    expect($otpRecord)->not->toBeNull();

    // 2. Verify OTP
    $otpVerifyResponse = $this->postJson('/api/users/otp-verify', [
        'email' => $user->email,
        'otp' => $otpRecord->verification_code,
    ]);

    $otpVerifyResponse->assertStatus(200);
    $user->refresh();
    expect($user->password_reset_token)->not->toBeNull();

    // 3. Reset Password
    $resetResponse = $this->postJson('/api/users/reset-password', [
        'email' => $user->email,
        'password' => 'NewSuperPassword123!',
        'password_confirmation' => 'NewSuperPassword123!',
    ]);

    $resetResponse->assertStatus(200);

    $user->refresh();
    expect(Hash::check('NewSuperPassword123!', $user->password))->toBeTrue()
        ->and($user->password_reset_token)->toBeNull();
});
