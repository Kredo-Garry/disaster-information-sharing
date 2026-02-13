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

        // 空文字対策（"") を除外したいので trim して判定
        $tag = trim((string) $request->query('tag', ''));
        $platform = trim((string) $request->query('platform', ''));

        $q = Feed::query()
            ->where('is_visible', true);

        if ($platform !== '') {
            $q->where('source_platform', $platform);
        }

        if ($tag !== '') {
            $q->whereJsonContains('tags', $tag);
        }

        // ✅ 並び順：重要度（sort_weight）を最優先 → 次に日時 → 次にid
        // published_at が無いものは created_at を使う
        $q->orderByDesc('sort_weight')
          ->orderByRaw('COALESCE(published_at, created_at) DESC')
          ->orderByDesc('id');

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
