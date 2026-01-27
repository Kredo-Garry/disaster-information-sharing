<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('admin.auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // ✅ Remember Me は false に固定
        if (Auth::attempt($credentials, false)) {
            $user = Auth::user();

            // 1. Admin（管理者）の場合
            if ($user->is_admin) {
                $request->session()->regenerate();
                // intendedを使うことで、元々行こうとしていたページがあればそこへ、なければdashboardへ
                return redirect()->intended(route('admin.dashboard'));
            }

            // 2. 一般ユーザーの場合
            // 管理者画面にセッションを残したくない場合は、ここで一度ログアウトさせるのがおすすめ
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            // 外部のフロントエンド（Reactなど）へ飛ばす
            return redirect()->away('http://localhost:3000/home');
        }

        // ログイン失敗時
        return back()->withInput($request->only('email'))->withErrors([
            'email' => 'ログイン情報が正しくありません。',
        ]);
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}