<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserCredit extends Model
{
    protected $fillable = [
        'user_id',
        'credits',
        'credits_refreshed_at',
        'monthly_credits_granted',
    ];

    protected $casts = [
        'credits_refreshed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
