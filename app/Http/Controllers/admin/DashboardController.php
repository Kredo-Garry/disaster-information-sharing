<?php

// app/Http/Controllers/Admin/AdminDashboardController.php

public function index()
{
    $userCount = User::count();
    $postCount = Post::count();
    $categoryCount = Category::count();

    $latestUsers = User::latest()->limit(5)->get();
    $latestPosts = Post::with('user')->latest()->limit(5)->get();

    // ★重要：もしエラーが出ているファイルが resources/views/dashboard.blade.php なら
    // return view('dashboard', ...); に書き換える必要があります。
    // もし resources/views/admin/dashboard.blade.php なら今のままでOK。
    
    return view('admin.dashboard', compact(
        'userCount', 
        'postCount', 
        'categoryCount', 
        'latestUsers', 
        'latestPosts'
    ));
}