<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserCredit;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Admin',
                'email' => 'admin@example.com',
                'role' => 'admin',
                'plan_type' => 'pro',
                'is_premium' => true,
            ],
            [
                'name' => 'Staff Admin',
                'email' => 'staff@example.com',
                'role' => 'admin',
                'plan_type' => 'pro',
                'is_premium' => true,
            ],
            [
                'name' => 'Premium User',
                'email' => 'premium@example.com',
                'role' => 'user',
                'plan_type' => 'pro',
                'is_premium' => true,
            ],
            [
                'name' => 'User',
                'email' => 'user@example.com',
                'role' => 'user',
                'plan_type' => 'free',
                'is_premium' => false,
            ],
        ];

        foreach ($users as $data) {
            $user = User::create([
                ...$data,
                'email_verified_at' => now(),
                'password' => Hash::make('12345678'),
                'avatar' => 'backend/assets/images/user/profile.jpeg',
                'remember_token' => Str::random(10),
                'created_at' => now(),
            ]);

            // Add credits based on plan
            UserCredit::create([
                'user_id' => $user->id,
                'credits' => $data['plan_type'] === 'pro' ? 1000 : 50,
            ]);
        }
    }
}
