<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PhivolcsEarthquake;

class HomeEarthquakeController extends Controller
{
    public function index()
    {
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
            ]);

        return response()->json([
            'earthquakes' => $items,
        ]);
    }
}
