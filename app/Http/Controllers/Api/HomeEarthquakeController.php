<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PhivolcsEarthquake;

class HomeEarthquakeController extends Controller
{
    public function index(Request $request)
    {
        $tz = config('app.timezone'); // 例: Asia/Manila

        $limit = (int) $request->query('limit', 50);
        $limit = max(1, min($limit, 200));

        $items = PhivolcsEarthquake::query()
            ->orderByDesc('occurred_at')
            ->limit($limit)
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
