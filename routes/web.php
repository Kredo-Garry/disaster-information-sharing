<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\AdminFeedController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Api\MyPageApiController;

/*
|--------------------------------------------------------------------------
| 公開ページ
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| ログイン後リダイレクト
|--------------------------------------------------------------------------
*/
Route::get('/dashboard', function () {
    $user = Auth::user();

    if ($user && $user->is_admin === 1) {
        return redirect()->route('admin.dashboard');
    }

    return redirect()->away('http://localhost:3000');
})->middleware(['auth', 'verified'])->name('dashboard');

/*
|--------------------------------------------------------------------------
| プロフィール（全ログインユーザー）
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| MyPage JSON API（React用・session認証）
|--------------------------------------------------------------------------
| ※ 未認証時にリダイレクトさせないため、JSONを返す設計
|--------------------------------------------------------------------------
*/
Route::middleware('auth:web')->prefix('api')->group(function () {

    Route::get('/me', [MyPageApiController::class, 'me']);
    Route::patch('/me/status', [MyPageApiController::class, 'updateStatus']);
    Route::patch('/me/family', [MyPageApiController::class, 'updateFamily']);
    Route::get('/family', [MyPageApiController::class, 'family']);

    /*
    |--------------------------------------------------------------------------
    | React用ログアウト
    |--------------------------------------------------------------------------
    */
    Route::post('/logout', function (Request $request) {

        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // APIなのでリダイレクトではなくJSONを返す
        return response()->json([
            'ok' => true,
        ]);
    })->name('api.logout');
});

/*
|--------------------------------------------------------------------------
| 管理者ルート
|--------------------------------------------------------------------------
*/
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', \App\Http\Middleware\AdminMiddleware::class])
    ->group(function () {

        Route::get('/dashboard', [AdminDashboardController::class, 'index'])
            ->name('dashboard');

        Route::resource('users', UserController::class);

        Route::resource('feeds', AdminFeedController::class)
            ->only(['index', 'store', 'destroy']);

        Route::resource('categories', CategoryController::class);
    });

/*
|--------------------------------------------------------------------------
| Auth関連
|--------------------------------------------------------------------------
*/
require __DIR__.'/auth.php';
