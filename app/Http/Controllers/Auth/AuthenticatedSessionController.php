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
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // ログインしたユーザーを取得
        $user = $request->user();

        // 管理者なら、Laravelの管理画面ダッシュボードへ
        if ($user->is_admin) {
            return redirect()->intended(route('admin.dashboard'));
        }

        // 一般ユーザーなら、React（localhost:3000）へ
        return redirect()->away('http://localhost:3000');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // ✅ ログアウト後はトップ（http://localhost:8000/）へ
        return redirect('/');
    }
}
