<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ShortUrl;
use App\Models\UrlClick;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;

class UrlController extends Controller
{
    public function index(Request $request): View
    {
        $urls = ShortUrl::where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('urls.index', compact('urls'));
    }

    public function create(): View
    {
        return view('urls.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'original_url' => 'required|url|max:2048',
            'custom_code' => 'nullable|string|max:50|regex:/^[a-zA-Z0-9_-]+$/|unique:short_urls,custom_code',
            'expires_in' => 'nullable|integer|min:1|max:365',
            'is_commercial' => 'nullable|boolean',
        ]);

        $data = $request->all();
        $data['user_id'] = $request->user()->id;

        // Генерация короткого кода
        $data['short_code'] = $data['custom_code'] ?? Str::random(8);

        // Проверка на уникальность
        if (ShortUrl::where('short_code', $data['short_code'])->exists()) {
            $data['short_code'] = Str::random(8);
        }

        // Расчет даты истечения
        if (isset($data['expires_in'])) {
            $data['expires_at'] = Carbon::now()->addDays($data['expires_in']);
        }

        $shortUrl = ShortUrl::create($data);

        // Если ссылка коммерческая, показываем специальный URL
        $short_url = $shortUrl->is_commercial
            ? $shortUrl->commercial_redirect_url
            : $shortUrl->short_url;

        return redirect()->route('urls.index')
            ->with('success', 'Ссылка успешно создана!')
            ->with('new_url', [
                'short_url' => $short_url,
                'stats_url' => $shortUrl->stats_url,
                'is_commercial' => $shortUrl->is_commercial,
            ]);
    }

    public function stats(ShortUrl $url, Request $request): View
    {
        // Проверка прав доступа
        if ($url->user_id !== $request->user()->id) {
            abort(403);
        }

        // Статистика за последние 14 дней
        $startDate = Carbon::now()->subDays(14);
        $endDate = Carbon::now();

        // Клики за последние 14 дней
        $clicksLast14Days = UrlClick::where('short_url_id', $url->id)
            ->whereBetween('clicked_at', [$startDate, $endDate])
            ->orderBy('clicked_at', 'asc')
            ->get();

        // Уникальные посетители за последние 14 дней
        $uniqueVisitors = UrlClick::where('short_url_id', $url->id)
            ->whereBetween('clicked_at', [$startDate, $endDate])
            ->distinct('ip_address')
            ->count('ip_address');

        // Клики по дням для графика
        $clicksByDay = UrlClick::where('short_url_id', $url->id)
            ->whereBetween('clicked_at', [$startDate, $endDate])
            ->selectRaw('DATE(clicked_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->pluck('count', 'date');

        // Заполняем пропущенные дни нулями
        $dateRange = [];
        $currentDate = clone $startDate;
        while ($currentDate <= $endDate) {
            $date = $currentDate->format('Y-m-d');
            $dateRange[$date] = $clicksByDay[$date] ?? 0;
            $currentDate->addDay();
        }

        // История кликов
        $clicksHistory = UrlClick::where('short_url_id', $url->id)
            ->orderBy('clicked_at', 'desc')
            ->paginate(20);

        return view('urls.stats', compact('url', 'uniqueVisitors', 'dateRange', 'clicksHistory'));
    }

    public function destroy(ShortUrl $url, Request $request): RedirectResponse
    {
        // Проверка прав доступа
        if ($url->user_id !== $request->user()->id) {
            abort(403);
        }

        $url->delete();

        return redirect()->route('urls.index')->with('success', 'Ссылка успешно удалена!');
    }
}
