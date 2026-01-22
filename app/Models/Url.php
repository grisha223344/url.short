<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Url extends Model
{
    protected $fillable = [
        'user_id',
        'original_url',
        'short_code',
        'is_commercial',
        'cost_per_view',
        'budget',
        'budget_spent',
        'expires_at'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    public function clicks(): HasMany
    {
        return $this->hasMany(UrlClick::class);
    }

    public function isExpired(): bool
    {
        return $this->expires_at && now()->greaterThan($this->expires_at);
    }
}
