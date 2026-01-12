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

require __DIR__.'/auth.php';

// ★ これを追加！ 
// auth.php や他の部品が「dashboard」という名前を探した時に、
// エラーを出さずに管理画面のダッシュボードへ案内する設定です。
Route::get('/dashboard', function () {
    return redirect()->route('admin.dashboard');
})->name('dashboard');