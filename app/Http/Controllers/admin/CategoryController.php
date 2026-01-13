<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    // 一覧表示
    public function index()
    {
        return view('admin.categories.index', [
            'categories' => Category::latest()->paginate(10),
        ]);
    }

    // ★ここが足りなかったためにエラーが出ていました
    public function create()
    {
        // 登録画面を表示する
        return view('admin.categories.create');
    }

    // 保存処理 (後で必要になるので一緒に入れておきます)
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            // 他に必要なバリデーションがあれば追加
        ]);

        Category::create($validated);

        return redirect()->route('admin.categories.index')
            ->with('success', 'カテゴリを登録しました');
    }
}