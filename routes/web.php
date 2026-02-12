<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\AdminFeedController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Api\MyPageApiController;

// --- 公開ページ ---
Route::get('/', function () { return view('welcome'); });

// --- 共通のログイン後リダイレクト先 (自動振り分け) ---
Route::get('/dashboard', function () {
    $user = Auth::user();

    if ($user->is_admin === 1) {
        return redirect()->route('admin.dashboard');
    }

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
| ✅ MyPage JSON API（web/session認証で守る）
|--------------------------------------------------------------------------
|
| routes/api.php ではなく web.php に置くことで
| Breeze(session) の auth をそのまま使える。
| React側は fetch/axios で credentials を付けて呼ぶ想定。
|
*/
Route::middleware('auth')->prefix('api')->group(function () {
    Route::get('/me', [MyPageApiController::class, 'me']);
    Route::patch('/me/status', [MyPageApiController::class, 'updateStatus']);
    Route::patch('/me/family', [MyPageApiController::class, 'updateFamily']);
    Route::get('/family', [MyPageApiController::class, 'family']);
});

/*
|--------------------------------------------------------------------------
| 管理者ルート (Admin routes)
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware(['auth', \App\Http\Middleware\AdminMiddleware::class])->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        Route::resource('users', UserController::class);
        Route::resource('feeds', AdminFeedController::class)->only(['index', 'destroy']);
        Route::resource('categories', CategoryController::class);
    });
});

// --- Auth関連 (ログイン・登録など) ---
require __DIR__.'/auth.php';
