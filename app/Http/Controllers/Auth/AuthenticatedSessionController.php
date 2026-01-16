<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // ログインしたユーザー情報を取得
        $user = Auth::user();

        // 画像のDB構造に基づき、is_admin カラムの値で判定
        if ($user->is_admin === 1) {
            // 管理者の場合：LaravelのAdminダッシュボードへ
            return redirect()->intended(route('dashboard', absolute: false));
        }

        // 一般ユーザー（is_admin が 0）の場合：React側のページへ
        // ※ReactのURLが異なる場合は、このURLを修正してください
        return redirect()->away('http://localhost:3000/home');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
