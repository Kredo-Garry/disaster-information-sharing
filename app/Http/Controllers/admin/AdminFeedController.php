<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Feed; // Feedモデルの名前が違う場合は修正してください
use Illuminate\Http\Request;

class AdminFeedController extends Controller
{
    public function index()
    {
        // 投稿を新しい順に取得（投稿者情報 user も一緒に取得）
        $feeds = Feed::with('user')->latest()->paginate(10);
        return view('admin.feeds.index', compact('feeds'));
    }

    public function destroy(Feed $feed)
    {
        $feed->delete();
        return redirect()->route('admin.feeds.index')->with('success', '投稿を削除しました。');
    }
}
