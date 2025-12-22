<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DisasterController;

Route::prefix('api')->group(function () {

    // GET /api/disasters  ← 一覧取得
    Route::get('/disasters', [DisasterController::class, 'index']);

    // GET /api/disasters/{id}  ← 詳細取得
    Route::get('/disasters/{id}', [DisasterController::class, 'show']);

    // POST /api/disasters  ← 新規登録
    Route::post('/disasters', [DisasterController::class, 'store']);

    // PUT /api/disasters/{id}  ← 更新
    Route::put('/disasters/{id}', [DisasterController::class, 'update']);

    // DELETE /api/disasters/{id}  ← 削除
    Route::delete('/disasters/{id}', [DisasterController::class, 'destroy']);
});
