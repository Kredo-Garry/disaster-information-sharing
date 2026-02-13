<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthApiController extends Controller
{
    public function logout(Request $request)
    {
        Auth::logout(); // webガード（セッション）をログアウト

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Reactから叩くのでリダイレクトではなく 204 を返すのがベスト
        return response()->noContent(); // 204
    }
}
