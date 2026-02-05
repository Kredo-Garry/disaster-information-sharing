<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Post;
use Throwable;

class PostApiController extends Controller
{
    public function index()
    {
        return Post::latest()->get();
    }

    public function store(Request $request)
    {
        // ✅ まず「何が飛んできてるか」をログに出す
        Log::info('[POST /api/posts] payload', $request->all());

        try {
            $data = $request->validate([
                'title' => ['required','string','max:255'],
                'body' => ['nullable','string'],
                'lat' => ['required','numeric'],
                'lng' => ['required','numeric'],
                'category_id' => ['nullable','integer'],
                'user_id' => ['nullable','integer'],
            ]);

            $post = Post::create($data);

            Log::info('[POST /api/posts] created', ['id' => $post->id]);

            return response()->json($post, 201);

        } catch (Throwable $e) {
            Log::error('[POST /api/posts] error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // ✅ フロントで原因を見えるように返す（開発中のみ）
            return response()->json([
                'ok' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
