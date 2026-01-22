<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UrlClick extends Model
{
    use HasFactory;

    protected $fillable = [
        'url_id',
        'ip_address',
        'user_agent',
    ];

    public function url(): BelongsTo
    {
        return $this->belongsTo(Url::class, 'url_id', 'id');
    }
}
