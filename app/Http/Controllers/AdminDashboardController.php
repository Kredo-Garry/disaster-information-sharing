<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Post;
use App\Models\Category;

class AdminDashboardController extends Controller
{
    public function index()
    {
        return view('admin.dashboard', [
            'userCount' => User::count(),
            'postCount' => Post::count(),
            'categoryCount' => Category::count(),

            'latestUsers' => User::latest()->limit(5)->get(),
            'latestPosts' => Post::with('user')->latest()->limit(5)->get(),
        ]);
    }
}
