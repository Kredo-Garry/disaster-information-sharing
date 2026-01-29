<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth; // 追加
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\AdminFeedController;
use App\Http\Controllers\Admin\CategoryController;

// --- 公開ページ ---
Route::get('/', function () { return view('welcome'); });

// --- 共通のログイン後リダイレクト先 (自動振り分け) ---
// Breezeが標準で使う 'dashboard' ルートを一つにまとめ、中身で分岐させます
Route::get('/dashboard', function () {
    $user = Auth::user();

    // 管理者の場合は管理画面へ、一般ユーザーはReactへ
    if ($user->is_admin === 1) {
        return redirect()->route('admin.dashboard');
    }

    // 一般ユーザーの飛び先
    return redirect()->away('http://localhost:3000');
})->middleware(['auth', 'verified'])->name('dashboard');

// --- プロフィール (全ログインユーザー) ---
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| 管理者ルート (Admin routes)
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->group(function () {
    // 管理者専用エリア
    Route::middleware(['auth', \App\Http\Middleware\AdminMiddleware::class])->group(function () {
        // 管理者ダッシュボード
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // 各種管理機能
        Route::resource('users', UserController::class);
        Route::resource('feeds', AdminFeedController::class)->only(['index', 'destroy']);
        Route::resource('categories', CategoryController::class);
    });
});

// --- Auth関連 (ログイン・登録など) ---
require __DIR__.'/auth.php';
