<?php

namespace App\Models;

use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ShortUrl extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'original_url',
        'short_code',
        'custom_code',
        'is_commercial',
        'cost_per_view',
        'budget',
        'budget_spent',
        'unique_paid_views',
        'max_daily_views',
        'campaign_start_date',
        'campaign_end_date',
        'expires_at',
        'total_clicks'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'is_commercial' => 'boolean',
        'cost_per_view' => 'decimal:2',
        'budget' => 'decimal:2',
        'budget_spent' => 'decimal:2',
        'campaign_start_date' => 'date',
        'campaign_end_date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function clicks(): HasMany
    {
        return $this->hasMany(UrlClick::class);
    }

    public function paidViews(): HasMany
    {
        return $this->hasMany(PaidView::class);
    }

    public function isExpired(): bool
    {
        return $this->expires_at && now()->greaterThan($this->expires_at);
    }

    public function getShortUrlAttribute(): UrlGenerator|string
    {
        $code = $this->custom_code ?? $this->short_code;
        return url("/{$code}");
    }

    public function getStatsUrlAttribute(): UrlGenerator|string
    {
        return url("/stats/{$this->short_code}");
    }

    public function getCommercialRedirectUrlAttribute(): UrlGenerator|string
    {
        return url("/commercial/{$this->short_code}");
    }

    // Новые методы для платных показов

    public function getRemainingBudgetAttribute()
    {
        return max(0, $this->budget - $this->budget_spent);
    }

    public function getBudgetPercentageAttribute()
    {
        if ($this->budget <= 0) {
            return 0;
        }

        return min(100, ($this->budget_spent / $this->budget) * 100);
    }

    public function isCampaignActive(): bool
    {
        if (!$this->is_commercial) {
            return false;
        }

        // Проверка дат кампании
        if ($this->campaign_start_date && now()->lt($this->campaign_start_date)) {
            return false;
        }

        if ($this->campaign_end_date && now()->gt($this->campaign_end_date)) {
            return false;
        }

        // Проверка бюджета
        if ($this->budget > 0 && $this->budget_spent >= $this->budget) {
            return false;
        }

        return true;
    }

    public function canShowPaidAd(): bool
    {
        if (!$this->is_commercial) {
            return false;
        }

        // Проверка активности кампании
        if (!$this->isCampaignActive()) {
            return false;
        }

        // Проверка максимального количества показов в день
        if ($this->max_daily_views > 0) {
            $todayViews = $this->paidViews()
                ->whereDate('viewed_at', now()->toDateString())
                ->count();

            if ($todayViews >= $this->max_daily_views) {
                return false;
            }
        }

        return true;
    }

    public function getTodayViewsAttribute(): int
    {
        return $this->paidViews()
            ->whereDate('viewed_at', now()->toDateString())
            ->count();
    }

    public function getEstimatedRemainingViewsAttribute(): ?float
    {
        if ($this->cost_per_view <= 0) {
            return null;
        }

        return floor($this->remaining_budget / $this->cost_per_view);
    }
}
