<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * ログイン画面を表示
     */
    public function showLoginForm()
    {
        return view('admin.auth.login');
    }

    /**
     * ログイン処理
     */
    public function login(Request $request)
    {
        // 1. 入力チェック
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // 2. ログイン試行
        // 第2引数の $request->boolean('remember') で「Remember me」に対応
        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate(); // セッション固定攻撃対策

            // ログイン成功：ダッシュボードへ
            return redirect()->intended(route('admin.dashboard'));
        }

        // 3. ログイン失敗：エラーを返して元の画面へ
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * ログアウト処理
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}