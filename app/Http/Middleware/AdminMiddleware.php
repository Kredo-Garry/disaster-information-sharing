<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. まずは「ログインしているか」だけをチェック
        if (!Auth::check()) {
            // 未ログインなら、名前付きルートを使わず直接パスで /login へ飛ばすにょ！
            // これで admin/login に連行されるのを確実に防ぐにぇ。
            return redirect('/login');
        }

        // 2. ログインはしているけど「管理者(is_admin)か」をチェック
        // ※DBの型に合わせて (bool) か 1 かを判定するにょ
        if (Auth::user()->is_admin != 1) {
            // 管理者じゃない（一般ユーザー）が紛れ込んできたら、React側のホームへ強制送還だにょ！
            return redirect()->away('http://localhost:3000/home')
                             ->with('error', '管理者権限がありません。');
        }

        // 3. どちらもクリア（ログイン済み ＋ 管理者）ならそのまま通してあげるにょ！
        return $next($request);
    }
}