<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\PostApiController;
use App\Http\Controllers\Api\HomeEarthquakeController;
use App\Http\Controllers\Api\HomeTsunamiController;
use App\Http\Controllers\Api\HomeVolcanoController;
use App\Http\Controllers\Api\FeedApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| DIShiP API endpoints
|--------------------------------------------------------------------------
*/

/*
|--------------------------------------------------------------------------
| Health Check
|--------------------------------------------------------------------------
*/
Route::get('/ping', function () {
    return response()->json([
        'ok' => true,
        'time' => now()->toDateTimeString(),
    ]);
});

/*
|--------------------------------------------------------------------------
| Posts API
|--------------------------------------------------------------------------
*/
Route::prefix('posts')->group(function () {
    Route::get('/', [PostApiController::class, 'index']);
    Route::post('/', [PostApiController::class, 'store']);
});

/*
|--------------------------------------------------------------------------
| PHIVOLCS (for /home)
|--------------------------------------------------------------------------
*/
Route::prefix('home')->group(function () {
    Route::get('/earthquakes', [HomeEarthquakeController::class, 'index']);
    Route::get('/tsunami', [HomeTsunamiController::class, 'index']);
    Route::get('/volcano', [HomeVolcanoController::class, 'index']);
});

/*
|--------------------------------------------------------------------------
| Feed API
|--------------------------------------------------------------------------
| Used by FeedBlock.jsx
|--------------------------------------------------------------------------
*/
Route::get('/feed', [FeedApiController::class, 'index']);
