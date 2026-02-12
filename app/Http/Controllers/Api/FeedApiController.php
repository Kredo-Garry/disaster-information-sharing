<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Feed;
use Illuminate\Http\Request;

class FeedApiController extends Controller
{
    public function index(Request $request)
    {
        $pageSize = (int) $request->query('pageSize', 10);
        $pageSize = max(1, min($pageSize, 50));

        $tag = $request->query('tag');
        $platform = $request->query('platform');

        $q = Feed::query()
            ->where('is_visible', true);

        if ($platform) {
            $q->where('source_platform', $platform);
        }

        if ($tag) {
            $q->whereJsonContains('tags', $tag);
        }

        $q->orderByRaw('COALESCE(published_at, created_at) DESC')
          ->orderBy('sort_weight', 'DESC')
          ->orderBy('id', 'DESC');

        $p = $q->paginate($pageSize);

        return response()->json([
            'data' => $p->items(),
            'meta' => [
                'page' => $p->currentPage(),
                'pageSize' => $p->perPage(),
                'total' => $p->total(),
                'totalPages' => $p->lastPage(),
            ],
        ]);
    }
}
