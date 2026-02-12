<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PhivolcsTsunamiBulletin;

class HomeTsunamiController extends Controller
{
    public function index(Request $request)
    {
        $limit = max(1, min(20, (int)$request->query('limit', 3)));
        $tz = config('app.timezone'); // Asia/Manila

        $items = PhivolcsTsunamiBulletin::query()
            ->orderByDesc('issued_at')
            ->orderByDesc('id')
            ->limit($limit)
            ->get([
                'id',
                'bulletin_no',
                'status',
                'issued_at',
                'summary_text',
                'source_url',
                'fetched_at',
                'hash',
            ])
            ->map(function ($item) use ($tz) {

                if ($item->issued_at) {
                    $item->issued_at = $item->issued_at
                        ->copy()
                        ->timezone($tz)
                        ->toIso8601String();
                }

                if ($item->fetched_at) {
                    $item->fetched_at = $item->fetched_at
                        ->copy()
                        ->timezone($tz)
                        ->toIso8601String();
                }

                return $item;
            });

        return response()->json(['tsunami' => $items]);
    }
}
