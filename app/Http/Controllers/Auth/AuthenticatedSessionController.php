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
    // app/Http/Controllers/Auth/AuthenticatedSessionController.php

    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // ログインしたユーザー情報を取得
        $user = $request->user();

        // Role（役割）によってリダイレクト先を分岐
        // ※DBに 'role' カラムがある前提です
        if ($user->role === 'admin') {
            // 管理者はローカルのダッシュボードへ
            return redirect()->intended('http://127.0.0.1:8000/admin/dashboard');
        }

        // 一般ユーザーは別ドメイン（React等）のフロントエンドへ強制移動
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
