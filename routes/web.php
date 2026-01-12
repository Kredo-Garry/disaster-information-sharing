<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;

// 公開ページ
Route::get('/', function () { return view('welcome'); });

// プロフィール
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
    // 管理者ログイン
    Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AdminAuthController::class, 'login'])->name('login.submit');
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');

    // 'admin' を消して、自作のミドルウェアを通すようにします
Route::middleware(['auth', \App\Http\Middleware\AdminMiddleware::class])->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::resource('users', \App\Http\Controllers\Admin\UserController::class);
    Route::resource('feeds', \App\Http\Controllers\Admin\AdminFeedController::class)->only(['index', 'destroy']);
        
        // 他の管理者用ルートをここに追加
    });
});

require __DIR__.'/auth.php';

// ★ これを追加！ 
// auth.php や他の部品が「dashboard」という名前を探した時に、
// エラーを出さずに管理画面のダッシュボードへ案内する設定です。
Route::get('/dashboard', function () {
    return redirect()->route('admin.dashboard');
})->name('dashboard');