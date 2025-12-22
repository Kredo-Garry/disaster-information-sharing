<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DisasterController;

Route::prefix('api')->group(function () {

    // GET /api/disasters  ← 一覧取得
    Route::get('/disasters', [DisasterController::class, 'index']);

    // POST /api/disasters  ← 新規登録
    Route::post('/disasters', [DisasterController::class, 'store']);

});
