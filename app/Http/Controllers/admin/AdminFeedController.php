<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Feed;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminFeedController extends Controller
{
    public function index(Request $request)
    {
        $feeds = Feed::query()
            ->orderByDesc('sort_weight')
            ->orderByRaw('COALESCE(published_at, created_at) DESC')
            ->orderByDesc('id')
            ->paginate(20);

        return view('admin.feeds.index', compact('feeds'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'source_platform' => ['required', 'string', 'max:50'],
            'external_author' => ['nullable', 'string', 'max:255'],
            'original_url'    => ['nullable', 'url', 'max:2048'],

            // content は embed_html だけで運用したい場合があるので nullable 推奨。
            // ただし DB 側が content NOT NULL の場合は required に戻してね。
            'content'         => ['nullable', 'string'],

            'tags'            => ['nullable', 'string'], // カンマ区切り入力
            'published_at'    => ['nullable', 'date'],
            'is_visible'      => ['nullable', 'in:0,1'],
            'sort_weight'     => ['nullable', 'integer', 'min:-999999', 'max:999999'],
            'embed_html'      => ['nullable', 'string'],
        ]);

        // tags: "earthquake, tsunami" -> ["earthquake","tsunami"]
        $tags = null;
        $rawTags = trim((string) ($validated['tags'] ?? ''));
        if ($rawTags !== '') {
            $tags = array_values(array_filter(array_map(
                fn ($t) => trim($t),
                explode(',', $rawTags)
            )));
            if (count($tags) === 0) $tags = null;
        }

        Feed::create([
            'user_id'         => Auth::id(), // 管理者が作成した印として入れる（不要なら null でOK）
            'source_platform' => $validated['source_platform'],
            'external_author' => $validated['external_author'] ?? null,
            'original_url'    => $validated['original_url'] ?? null,
            'content'         => $validated['content'] ?? null,
            'tags'            => $tags,
            'published_at'    => $validated['published_at'] ?? now(),
            'is_visible'      => (int)($validated['is_visible'] ?? 1) === 1,
            'sort_weight'     => (int)($validated['sort_weight'] ?? 0),
            'embed_html'      => $validated['embed_html'] ?? null,
        ]);

        return redirect()
            ->route('admin.feeds.index')
            ->with('status', 'Feed added!');
    }

    public function destroy(Feed $feed)
    {
        $feed->delete();

        return redirect()
            ->route('admin.feeds.index')
            ->with('status', 'Feed deleted!');
    }
}
