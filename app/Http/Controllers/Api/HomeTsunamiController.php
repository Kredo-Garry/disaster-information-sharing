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
            ]);

        return response()->json(['tsunami' => $items]);
    }
}
