<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
// Postモデルがまだ存在しない、またはテーブルがない場合はここもエラーの原因になるので注意
use App\Models\Post; 

class PostController extends Controller
{
    public function index()
    {
        // 修正：存在しない view を呼び出すのをやめて、ダッシュボードへリダイレクトさせる
        return redirect()->route('admin.dashboard');

        /* 将来的に posts フォルダを作成した際は、以下に戻します
        return view('admin.posts.index', [
            'posts' => Post::with('user')->latest()->paginate(10),
        ]);
        */
    }
}