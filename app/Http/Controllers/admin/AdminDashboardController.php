<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Post;
use App\Models\Category;

class AdminDashboardController extends Controller
{
    public function index()
    {
        return view('admin.dashboard', [
            'userCount'     => User::count(),
            'postCount'     => Post::count(),
            'categoryCount' => Category::count(),
            'users'         => User::latest()->limit(5)->get(),
        ]);
    }
}
