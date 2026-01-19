<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaidView extends Model
{
    use HasFactory;

    protected $fillable = [
        'short_url_id',
        'visitor_hash',
        'cost',
    ];

    protected $casts = [
        'cost' => 'decimal:2',
        'viewed_at' => 'datetime',
    ];

    public $timestamps = false;

    public function shortUrl(): BelongsTo
    {
        return $this->belongsTo(ShortUrl::class);
    }
}
