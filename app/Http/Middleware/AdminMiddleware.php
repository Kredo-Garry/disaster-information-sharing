<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // ログインしていない、または管理者(is_admin)ではない場合
        if (!auth()->check() || !auth()->user()->is_admin) {
            
            // ログインしていないならログイン画面へ
            if (!auth()->check()) {
                return redirect()->route('login');
            }

            // 管理者じゃないならエラーメッセージ付きでトップへ
            return redirect('/')->with('error', '管理者権限がありません。');
        }

        return $next($request);
    }
}