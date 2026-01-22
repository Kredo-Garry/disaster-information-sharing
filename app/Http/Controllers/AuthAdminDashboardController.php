<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Post;
use App\Models\Category;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function index()
    {
        return view('admin.dashboard', [
            'userCount'     => \App\Models\User::count(),
            'postCount'     => 0, // 投稿機能がないので 0 固定
            'categoryCount' => \App\Models\Category::count(),
            'latestUsers'   => \App\Models\User::latest()->limit(5)->get(), 
            // 投稿データは空の「コレクション」を渡して、ループでエラーが出ないようにする
            'latestPosts'   => collect(), 
        ]);
    }
}