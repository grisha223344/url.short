<?php

namespace App\Http\Controllers;

use App\Models\ShortUrl;
use App\Models\UrlClick;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class ShortUrlController extends Controller
{
    public function shorten(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'original_url' => 'required|url|max:2048',
            'custom_code' => 'nullable|string|max:255|unique:short_urls,custom_code',
            'expires_in' => 'nullable|integer|min:1',
            'is_commercial' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();

        // Генерация короткого кода
        $shortCode = $data['custom_code'] ?? Str::random(8);

        // Проверка на уникальность
        if (ShortUrl::where('short_code', $shortCode)->exists()) {
            $shortCode = Str::random(8);
        }

        // Расчет даты истечения
        $expiresAt = null;
        if (isset($data['expires_in'])) {
            $expiresAt = Carbon::now()->addDays($data['expires_in']);
        }

        $shortUrl = ShortUrl::create([
            'user_id' => $request->user() ? $request->user()->id : null,
            'original_url' => $data['original_url'],
            'short_code' => $shortCode,
            'custom_code' => $data['custom_code'] ?? null,
            'is_commercial' => $data['is_commercial'] ?? false,
            'expires_at' => $expiresAt,
        ]);

        return response()->json([
            'short_url' => $shortUrl->short_url,
            'stats_url' => $shortUrl->stats_url,
            'expires_at' => $shortUrl->expires_at,
        ], 201);
    }

    public function run(string $code)
    {
        $shortUrl = ShortUrl::query()->where('short_code', $code)->first();

        if ($shortUrl === null) {
            return response()->json(['error' => 'URL not found'], 404);
        }

        if ($shortUrl->isExpired()) {
            return response()->json(['error' => 'URL has expired'], 410);
        }

        // Запись клика
        UrlClick::create([
            'short_url_id' => $shortUrl->id,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'referer' => request()->header('referer'),
        ]);

        $shortUrl->increment('total_clicks');

        if ($shortUrl->is_commercial) {
            return response()->json(['error' => 'it is commercial url add /commercial/{url}'], 404);
        }

        return redirect()->away($shortUrl->original_url);
    }
}
