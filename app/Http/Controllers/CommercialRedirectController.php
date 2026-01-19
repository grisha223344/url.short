<?php

namespace App\Http\Controllers;

use App\Models\ShortUrl;
use App\Services\PaidViewService;
use Illuminate\Http\Request;

class CommercialRedirectController extends Controller
{
    protected PaidViewService $paidViewService;

    public function __construct(PaidViewService $paidViewService)
    {
        $this->paidViewService = $paidViewService;
    }

    public function showRedirectPage(string $code, Request $request)
    {
        $shortUrl = ShortUrl::where('short_code', $code)
            ->orWhere('custom_code', $code)
            ->first();

        if (!$shortUrl) {
            abort(404);
        }

        // Проверяем, истекла ли ссылка
        if ($shortUrl->isExpired()) {
            return view('commercial.expired', [
                'code' => $code,
            ]);
        }

        // Проверяем, является ли ссылка коммерческой
        if (!$shortUrl->is_commercial) {
            return abort(404);
        }

        // Проверяем, можно ли показывать платную рекламу
        if (!$shortUrl->canShowPaidAd()) {
            // Бюджет исчерпан или кампания не активна - перенаправляем сразу
            $this->recordDirectClick($shortUrl);
            return redirect($shortUrl->original_url);
        }

        // Проверяем уникальность показа
        if (!$this->paidViewService->isUniqueView($shortUrl, $request)) {
            // Показ не уникальный - показываем рекламу, но не списываем деньги
            return $this->showAdPage($shortUrl, false);
        }

        // Регистрируем платный показ
        $paidViewRegistered = $this->paidViewService->registerPaidView($shortUrl, $request);

        if (!$paidViewRegistered) {
            // Не удалось зарегистрировать показ (например, не хватило бюджета)
            $this->recordDirectClick($shortUrl);
            return redirect($shortUrl->original_url);
        }

        // Показываем рекламную страницу со списанием
        return $this->showAdPage($shortUrl, true);
    }

    private function showAdPage(ShortUrl $shortUrl, bool $isPaid)
    {
        return view('urls.commercial', [
            'code' => $shortUrl->short_code,
            'originalUrl' => $shortUrl->original_url,
            'randomImage' => '/cat.png',
            'shortUrl' => $shortUrl,
            'isPaid' => $isPaid,
            'cost' => $isPaid ? $shortUrl->cost_per_view : 0,
        ]);
    }

    public function performRedirect($code)
    {
        $shortUrl = ShortUrl::where('short_code', $code)
            ->orWhere('custom_code', $code)
            ->first();

        if (!$shortUrl) {
            abort(404);
        }

        // Записываем клик
        $this->recordClick($shortUrl);

        return redirect($shortUrl->original_url);
    }

    private function recordClick(ShortUrl $shortUrl)
    {
        \App\Models\UrlClick::create([
            'short_url_id' => $shortUrl->id,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'referer' => request()->header('referer'),
        ]);

        $shortUrl->increment('total_clicks');
    }

    private function recordDirectClick(ShortUrl $shortUrl)
    {
        // Записываем клик без показа рекламы
        \App\Models\UrlClick::create([
            'short_url_id' => $shortUrl->id,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'referer' => request()->header('referer'),
            'clicked_at' => now(),
        ]);

        $shortUrl->increment('total_clicks');
    }

    /**
     * API для получения информации о бюджете
     */
    public function getBudgetInfo($code)
    {
        $shortUrl = ShortUrl::where('short_code', $code)
            ->orWhere('custom_code', $code)
            ->first();

        if (!$shortUrl || !$shortUrl->is_commercial) {
            return response()->json(['error' => 'URL not found or not commercial'], 404);
        }

        $stats = $this->paidViewService->getStats($shortUrl);

        return response()->json([
            'success' => true,
            'data' => [
                'short_url' => $shortUrl->short_url,
                'is_commercial' => $shortUrl->is_commercial,
                'cost_per_view' => $shortUrl->cost_per_view,
                'budget' => $shortUrl->budget,
                'budget_spent' => $shortUrl->budget_spent,
                'remaining_budget' => $shortUrl->remaining_budget,
                'unique_paid_views' => $shortUrl->unique_paid_views,
                'campaign_active' => $shortUrl->isCampaignActive(),
                'can_show_ad' => $shortUrl->canShowPaidAd(),
                'today_views' => $shortUrl->today_views,
                'estimated_remaining_views' => $shortUrl->estimated_remaining_views,
                'stats' => $stats,
            ]
        ]);
    }
}
