<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\PhivolcsFetchController;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
| 管理画面専用ルーティング
| - ログイン前アクセス可
| - ログイン必須（admin）
|--------------------------------------------------------------------------
*/

// 1. ログインしていない状態でもアクセスできるルート
Route::prefix('admin')->name('admin.')->group(function () {
    // ✅ 名前の競合を避けるため 'login' ではなく 'showLogin'
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('showLogin');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
});

// 2. ログイン必須のルート
Route::middleware(['web', 'auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // --- Dashboard ---
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // --- Resource CRUD ---
        Route::resource('users', UserController::class);
        Route::resource('posts', PostController::class);
        Route::resource('categories', CategoryController::class);

        // --- PHIVOLCS 手動取得 ---
        // 一覧ページ（ボタンUI）
        Route::get('/phivolcs', [PhivolcsFetchController::class, 'index'])
            ->name('phivolcs.index');

        // 全取得
        Route::post('/phivolcs/fetch-all', [PhivolcsFetchController::class, 'fetchAll'])
            ->name('phivolcs.fetchAll');

        // 個別取得
        Route::post('/phivolcs/fetch-earthquakes', [PhivolcsFetchController::class, 'fetchEarthquakes'])
            ->name('phivolcs.fetchEarthquakes');

        Route::post('/phivolcs/fetch-tsunami', [PhivolcsFetchController::class, 'fetchTsunami'])
            ->name('phivolcs.fetchTsunami');

        Route::post('/phivolcs/fetch-volcano', [PhivolcsFetchController::class, 'fetchVolcano'])
            ->name('phivolcs.fetchVolcano');

        // --- Logout ---
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    });
