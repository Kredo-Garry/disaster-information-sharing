<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Post;

class PostApiController extends Controller
{
    public function index()
    {
        return Post::latest()->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required','string','max:255'],
            'body' => ['nullable','string'],
            'lat' => ['required','numeric'],
            'lng' => ['required','numeric'],
            'category_id' => ['nullable','integer'],
            'user_id' => ['nullable','integer'],
        ]);

        $post = Post::create($data);

        return response()->json($post, 201);
    }
}
