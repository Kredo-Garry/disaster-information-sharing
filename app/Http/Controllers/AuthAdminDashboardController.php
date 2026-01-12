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
        // Fetch counts for the statistics cards
        $userCount = User::count();
        $postCount = Post::count();
        $categoryCount = Category::count();

        // Fetch latest data for the tables (English variable names)
        $latestUsers = User::latest()->limit(5)->get();
        $latestPosts = Post::with('user')->latest()->limit(5)->get();

        // Pass variables to the view
        return view('admin.dashboard', compact(
            'userCount', 
            'postCount', 
            'categoryCount', 
            'latestUsers', 
            'latestPosts'
        ));
    }
}