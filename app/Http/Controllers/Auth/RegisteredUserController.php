<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            // もしデフォルトで一般ユーザーにするならここでroleを指定してもOK
            // 'is_admin' => false, 
        ]);

        event(new Registered($user));

        Auth::login($user);

        // ✅ リダイレクト先を分岐
        if ($user->is_admin) {
            // 管理者の場合はLaravel内のダッシュボードへ
            return redirect()->intended(route('admin.dashboard'));
        }

        // 一般ユーザーの場合はReact側のフロントエンドへ飛ばす
        // 管理者画面のセッションを持たせたくないなら、ここでAuth::logout()しても良いですが、
        // React側でAPIを使うならログイン状態のまま飛ばすのが一般的だにょ
        return redirect()->away('http://localhost:3000/home');
    }
    
}
