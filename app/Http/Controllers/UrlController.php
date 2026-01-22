<?php

namespace App\Http\Controllers;

use App\Models\Url;
use App\Models\UrlClick;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;

class UrlController extends Controller
{
    public function dashboard(): View
    {
        $urlList = Url::with('clicks')->get();
        $activeUrls = $urlList->filter(function($url) {
            if ($url->expires_at === null or $url->expires_at > Carbon::now()) {
                return true;
            }
            return false;
        })->count();
        $totalClicks = UrlClick::query()->count();

        return view('dashboard.index', [
            'totalUrls' => $urlList->count(),
            'activeUrls' => $activeUrls,
            'totalClicks' => $totalClicks,
            'urlList' => $urlList,
        ]);
    }

    public function create(): View
    {
        return view('dashboard.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'original_url' => 'required|url|max:2048',
            'expires_in' => 'nullable|integer|min:1|max:365',
            'is_commercial' => 'nullable|boolean',
        ]);

        Url::create([
            'user_id' => auth()->id(),
            'original_url' => $request->original_url,
            'short_code' => Str::random(8),
            'is_commercial' => $request->is_commercial ? 1 : 0,
            'cost_per_view' => $request->is_commercial ? $request->cost_per_view : 0,
            'budget' => $request->is_commercial ? $request->budget : 0,
            'expires_at' => $request->expires_in ? Carbon::now()->addDays((int)$request->expires_in) : null,
        ]);

        return redirect()->route('dashboard')->with('success', 'Ссылка успешно создана!');
    }

    public function stats(Url $url, Request $request): View
    {
        $startDate = Carbon::now()->subDays(14);
        $endDate = Carbon::now();
        $clicksLast14Days = UrlClick::where('url_id', $url->id)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->distinct('ip_address')
            ->count();

        return view('dashboard.stats', [
            'urlLink' => config('app.url') . '/' . $url->short_code,
            'clicksHistory' => $url->clicks,
            'url' => $url,
            'clicksLast14Days' => $clicksLast14Days,
        ]);
    }

    public function destroy(Url $url, Request $request): RedirectResponse
    {
        if ($url->user_id !== auth()->id()) {
            abort(403);
        }

        $url->clicks()->delete();
        $url->delete();

        return redirect()->route('dashboard')->with('success', 'Ссылка успешно удалена!');
    }

    public function run(string $code)
    {
        $url = Url::query()->where('short_code', $code)->first();

        if ($url === null) {
            return response()->json(['error' => 'URL not found'], 404);
        }

        if ($url->isExpired()) {
            return response()->json(['error' => 'URL has expired'], 410);
        }

        if ($url->is_commercial) {
            if ($url->budget_spent >= $url->budget) {
                return response()->json(['error' => 'Бюджет закончился'], 404);
            }

            // Check is unique view
            $urlClicks = UrlClick::where([['url_id', $url->id], ['ip_address', request()->ip()]])->exists();
            if (!$urlClicks) {
                $url->budget_spent = $url->budget_spent + $url->cost_per_view;
                $url->save();
            }
        }

        UrlClick::create([
            'url_id' => $url->id,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return !$url->is_commercial ?
            redirect()->away($url->original_url) :
            view('dashboard.commercial', [
                'originalUrl' => $url->original_url,
            ]);
    }
}
