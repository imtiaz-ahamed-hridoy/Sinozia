<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasUuids;

    protected $guarded = [];

    protected $casts = [
        'metadata' => 'array',
        'voice_enabled' => 'boolean',
        'transformation_type' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function hasFailed(): bool
    {
        return $this->status === 'failed';
    }

    public function targetCharacter()
    {
        return $this->belongsTo(TargetCharacter::class, 'target_character_id');
    }

    public function targetVoice()
    {
        return $this->belongsTo(TargetVoice::class, 'target_voice_id');
    }
}
