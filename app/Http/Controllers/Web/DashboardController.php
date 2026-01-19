<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ShortUrl;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();

        // Статистика для дашборда
        $totalUrls = ShortUrl::where('user_id', $user->id)->count();
        $activeUrls = ShortUrl::where('user_id', $user->id)
            ->where(function($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', Carbon::now());
            })
            ->count();

        $totalClicks = ShortUrl::where('user_id', $user->id)->sum('total_clicks');

        // Последние ссылки
        $recentUrls = ShortUrl::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('dashboard.index', compact('totalUrls', 'activeUrls', 'totalClicks', 'recentUrls'));
    }
}
