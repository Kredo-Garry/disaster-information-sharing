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

    // app/Http/Controllers/Auth/AuthenticatedSessionController.php

    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // ログインしたユーザーを取得
        $user = $request->user();

        // ✅ ここで運命の分かれ道だにょ！
        if ($user->is_admin) {
            // 管理者なら、Laravelの管理画面ダッシュボードへ
            return redirect()->intended(route('admin.dashboard'));
        }

        // 一般ユーザーなら、React（localhost:3000）へ強制送還！
        return redirect()->away('http://localhost:3000/home');
    }

    /**
     * Destroy an authenticated session.
     */
    /**
 * Log the user out of the application.
 */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        // ❌ 修正前: return redirect('/'); 
        // または return redirect()->route('admin.login');

        // ✅ 修正後: 明示的に /login に飛ばすにょ！
        return redirect('/login'); 
    }

}
