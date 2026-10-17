<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Mail\RegistationOtp;
use App\Models\EmailOtp;
use App\Models\User;
use App\Traits\ApiResponse;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    use ApiResponse;

    /**
     * Send a Register (OTP) to the user via email.
     *
     * @param  User  $user
     * @return void
     */
    private function sendOtp($user)
    {
        $code = rand(1000, 9999);

        // Store verification code in the database
        $verification = EmailOtp::updateOrCreate(
            ['user_id' => $user->id],
            [
                'verification_code' => $code,
                'expires_at' => Carbon::now()->addMinutes(3),
            ]
        );

        Mail::to($user->email)->send(new RegistationOtp($user, $code));
    }

    /**
     * Register User
     *
     * @param  Request  $request  The HTTP request with the register query.
     * @return JsonResponse JSON response with success or error.
     */
    public function Register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
            ],
        ], [
            'password.min' => 'The password must be at least 8 characters long.',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors(), $validator->errors()->first(), 422);
        }

        try {
            // Find the user by ID
            $user = new User;
            $user->name = $request->input('name');
            $user->email = $request->input('email');
            $user->password = Hash::make($request->input('password')); // Hash the password

            $user->save();

            $this->sendOtp($user);

            return $this->success($user, 'Verification email sent', 201);
        } catch (\Exception $e) {
            return $this->error([], $e->getMessage(), 500);
        }
    }

    public function resendOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors(), $validator->errors()->first(), 422);
        }

        $user = User::where('email', $request->input('email'))->first();

        if (! $user) {
            return $this->error([], 'User not found', 404);
        }

        $this->sendOtp($user);

        return $this->success([], 'OTP resent successfully', 200);
    }

    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'otp' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors(), $validator->errors()->first(), 422);
        }

        $user = User::where('email', $request->input('email'))->first();

        if (! $user) {
            return $this->error([], 'User not found', 404);
        }

        $otp = EmailOtp::where('user_id', $user->id)->first();

        if (! $otp) {
            return $this->error([], 'OTP not found', 404);
        }

        if ((string) $otp->verification_code !== (string) $request->input('otp')) {
            return $this->error([], 'Invalid OTP', 401);
        }

        if ($otp->expires_at < now()) {
            return $this->error([], 'OTP has expired', 401);
        }

        $user->email_verified_at = now();
        $user->save();

        $user->credits()->create([
            'credits' => 0,
        ]);

        $otp->delete();

        // Create Sanctum token
        $token = $user->createToken('auth_token')->plainTextToken;
        $user->setAttribute('token', $token);

        return $this->success($user, 'Email verified successfully', 200);
    }
}
