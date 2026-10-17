<?php

namespace Database\Seeders;

use App\Models\Credit;
use Illuminate\Database\Seeder;

class CreditsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Credit::insert([
            [
                'credit' => 300,
                'amount' => 12,
                'most_popular' => false,
            ],
            [
                'credit' => 1000,
                'amount' => 39,
                'most_popular' => true,
            ],
            [
                'credit' => 3000,
                'amount' => 119,
                'most_popular' => false,
            ],
        ]);
    }
}
