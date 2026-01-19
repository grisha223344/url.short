<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UrlClick extends Model
{
    use HasFactory;

    protected $fillable = [
        'short_url_id',
        'ip_address',
        'user_agent',
        'referer'
    ];

    public $timestamps = false;

    protected $casts = [
        'clicked_at' => 'datetime',
    ];

    public function shortUrl()
    {
        return $this->belongsTo(ShortUrl::class);
    }
}
