<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\Admin\AdminDashboardController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| User routes
|--------------------------------------------------------------------------
*/

// 一般ユーザー用ダッシュボード
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// プロフィール編集
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

    // 🔓 管理者ログイン（未ログイン可）
    Route::get('/login', [AdminAuthController::class, 'showLoginForm'])
        ->name('login');

    Route::post('/login', [AdminAuthController::class, 'login'])
        ->name('login.post');

    // 🔐 管理者専用（ログイン後）
    Route::middleware(['auth', 'admin'])->group(function () {

        Route::get('/dashboard', [AdminDashboardController::class, 'index'])
            ->name('dashboard');

        Route::post('/logout', [AdminAuthController::class, 'logout'])
            ->name('logout');
    });
});

require __DIR__.'/auth.php';
