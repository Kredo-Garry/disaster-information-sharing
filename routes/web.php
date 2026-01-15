<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\AdminFeedController;
use App\Http\Controllers\Admin\CategoryController; // ここを修正

// 公開ページ
Route::get('/', function () { return view('welcome'); });

// プロフィール (一般ユーザー用)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| Admin routes
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->name('admin.')->group(function () {
    // 管理者ログイン・ログアウト
    // ※AdminAuthControllerが未作成の場合は、適宜ルートを調整してください
    // Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('login');

    // 管理者専用エリア
    Route::middleware(['auth', \App\Http\Middleware\AdminMiddleware::class])->group(function () {
        // ダッシュボード
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        
        // ユーザー管理
        Route::resource('users', UserController::class);
        
        // フィード管理 (外部投稿)
        Route::resource('feeds', AdminFeedController::class)->only(['index', 'destroy']);
        
        // ★ 災害カテゴリ管理 (アイコン・ライフライン管理)
        // エラー解消のため AdminCategoryController ではなく CategoryController を指定
        Route::resource('categories', CategoryController::class);
    });
});

require __DIR__.'/auth.php';

    // 一般ユーザー用のダッシュボードルートを追加
    // これにより、User Login 後のリダイレクト先が正しく表示されます
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->middleware(['auth', 'verified'])->name('dashboard');

    // 管理画面への強制リダイレクトは不要になったので、コメントアウトのままでOKです
    // Route::get('/dashboard', function () {
    //     return redirect()->route('admin.dashboard');
    // })->name('dashboard');