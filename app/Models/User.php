<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        // 'email_verified_at',
        'created_at',
        'updated_at',
        'password_reset_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'agree_to_terms' => 'boolean',
            'is_active' => 'boolean',
            'is_premium' => 'boolean',
        ];
    }

    // Scopes
    public function scopeVerified($q)
    {
        return $q->whereNotNull('email_verified_at');
    }

    public function scopeActive($q)
    {
        return $q->where('is_active', true);
    }

    // Relationships
    public function credits()
    {
        return $this->hasOne(UserCredit::class);
    }

    public function paymentHistories()
    {
        return $this->hasMany(PaymentHistory::class);
    }

    public function scopePremium($q)
    {
        return $q->where('is_premium', true);
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function activeSubscription()
    {
        return $this->hasOne(Subscription::class)->where('is_active', true)->latestOfMany();
    }

    public function getCreditBalance(): int
    {
        return $this->credits?->credits ?? 0;
    }

    public function getPlanConfig(): array
    {
        $planKey = strtolower(str_replace(' ', '_', $this->plan_type ?? 'free'));

        return config("plans.{$planKey}", config('plans.free'));
    }

    public function canGenerate(): bool
    {
        return $this->getPlanConfig()['can_generate'] ?? false;
    }

    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    public function creditLedgers()
    {
        return $this->hasMany(CreditLedger::class);
    }

    public function targetVoices()
    {
        return $this->hasMany(TargetVoice::class);
    }

    public function generatedCharacters()
    {
        return $this->hasMany(GeneratedCharacter::class);
    }
}
