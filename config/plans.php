<?php

return [

    // plan_type value from users table → monthly credits + video cost
    'free' => [
        'monthly_credits' => 0,
        'video_cost' => 0, // free plan cannot generate
        'can_generate' => false,
    ],

    'standard' => [
        'monthly_credits' => 30,
        'video_cost' => 2,
        'can_generate' => false,
    ],

    'pro' => [
        'monthly_credits' => 100,
        'video_cost' => 0,
        'can_generate' => true,
    ],

    'unlimited' => [
        'monthly_credits' => 999999, // effectively unlimited
        'video_cost' => 0,
        'can_generate' => true,
    ],

];
