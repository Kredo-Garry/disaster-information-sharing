<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PhivolcsEarthquake;

class HomeEarthquakeController extends Controller
{
    public function index()
    {
        $tz = config('app.timezone'); // 例: Asia/Manila

        $items = PhivolcsEarthquake::query()
            ->orderByDesc('occurred_at')
            ->limit(10)
            ->get([
                'id',
                'occurred_at',
                'magnitude',
                'depth_km',
                'lat',
                'lng',
                'location_text',
            ])
            ->map(function ($item) use ($tz) {
                // occurred_at を現地時間に変換して ISO8601(+08:00) で返す
                $item->occurred_at = $item->occurred_at
                    ? $item->occurred_at->copy()->timezone($tz)->toIso8601String()
                    : null;

                return $item;
            });

        return response()->json([
            'earthquakes' => $items,
        ]);
    }
}
