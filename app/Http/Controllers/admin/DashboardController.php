<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Post;
use App\Models\Category;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $userCount = User::count();
        $postCount = Post::count();
        $categoryCount = Category::count();

        $latestUsers = User::latest()->limit(5)->get();
        // userリレーションをロード
        $latestPosts = Post::with('user')->latest()->limit(5)->get();

        return view('admin.dashboard', compact(
            'userCount', 
            'postCount', 
            'categoryCount', 
            'latestUsers', 
            'latestPosts'
        ));
    }
}