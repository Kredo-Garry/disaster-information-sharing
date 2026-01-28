<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\AuthController;

// 1. ログインしていない状態でもアクセスできるルート
Route::prefix('admin')->name('admin.')->group(function () {
    // ✅ 名前の競合を避けるため 'login' ではなく 'showLogin' に変更だにょ！
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('showLogin');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
});

// 2. ログイン必須のルート
Route::middleware(['web', 'auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        Route::resource('users', UserController::class);
        Route::resource('posts', PostController::class);
        Route::resource('categories', CategoryController::class);

        // ✅ ログアウト処理
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    });