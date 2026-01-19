<?php

namespace App\Services;

use App\Models\ShortUrl;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaidViewService
{
    /**
     * Проверяет, является ли показ уникальным
     */
    public function isUniqueView(ShortUrl $shortUrl, Request $request): bool
    {
        $visitorHash = $this->generateVisitorHash($request);

        // Проверяем, был ли уже показ для этого посетителя
        return !$shortUrl->paidViews()
            ->where('visitor_hash', $visitorHash)
            ->exists();
    }

    /**
     * Регистрирует уникальный платный показ
     */
    public function registerPaidView(ShortUrl $shortUrl, Request $request): bool
    {
        if (!$this->isUniqueView($shortUrl, $request)) {
            return false;
        }

        $visitorHash = $this->generateVisitorHash($request);
        $cost = $shortUrl->cost_per_view;
        // Проверяем, достаточно ли бюджета
        if ($shortUrl->budget > 0 && $shortUrl->budget_spent + $cost > $shortUrl->budget) {
            return false;
        }

        try {
            // Начинаем транзакцию для атомарности
            DB::transaction(function () use ($shortUrl, $visitorHash, $cost) {
                // Создаем запись о показе
                $shortUrl->paidViews()->create([
                    'visitor_hash' => $visitorHash,
                    'cost' => $cost,
                ]);

                // Обновляем статистику
                $shortUrl->increment('unique_paid_views');
                $shortUrl->increment('budget_spent', $cost);
            });

            return true;
        } catch (\Exception $e) {
            Log::error('Error registering paid view: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Генерирует хэш посетителя на основе IP и User-Agent
     */
    private function generateVisitorHash(Request $request): string
    {
        $ip = $request->ip();
        $userAgent = $request->userAgent();

        // Можно добавить дополнительные параметры для уникальности
        $string = $ip . '|' . $userAgent;

        return hash('sha256', $string);
    }

    /**
     * Получает статистику по платным показам
     */
    public function getStats(ShortUrl $shortUrl): array
    {
        $stats = [
            'total_views' => $shortUrl->paidViews()->count(),
            'unique_paid_views' => $shortUrl->unique_paid_views,
            'budget_spent' => $shortUrl->budget_spent,
            'remaining_budget' => $shortUrl->remaining_budget,
            'budget_percentage' => $shortUrl->budget_percentage,
            'estimated_remaining_views' => $shortUrl->estimated_remaining_views,
            'today_views' => $shortUrl->today_views,
        ];

        // Статистика по дням за последние 30 дней
        $viewsByDay = $shortUrl->paidViews()
            ->selectRaw('DATE(viewed_at) as date, COUNT(*) as count, SUM(cost) as cost')
            ->where('viewed_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $stats['views_by_day'] = $viewsByDay;

        // Статистика по источникам (первые 10 рефереров)
        $topReferers = $shortUrl->clicks()
            ->selectRaw('referer, COUNT(*) as count')
            ->whereNotNull('referer')
            ->where('referer', '!=', '')
            ->groupBy('referer')
            ->orderByDesc('count')
            ->limit(10)
            ->get();

        $stats['top_referers'] = $topReferers;

        return $stats;
    }
}
