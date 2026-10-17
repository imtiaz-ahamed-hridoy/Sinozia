<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TargetCharacter extends Model
{
    protected $guarded = [];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'id' => 'integer',
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function projects()
    {
        return $this->hasMany(Project::class, 'target_character_id');
    }
}
