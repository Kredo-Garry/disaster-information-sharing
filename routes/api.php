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
|
| DIShiP API endpoints
|
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
Route::get('/posts', [PostApiController::class, 'index']);
Route::post('/posts', [PostApiController::class, 'store']);

/*
|--------------------------------------------------------------------------
| PHIVOLCS (for /home)
|--------------------------------------------------------------------------
|
| These are used by React/Builder blocks on /home.
|
*/
Route::get('/home-earthquakes', [HomeEarthquakeController::class, 'index']);
Route::get('/home-tsunami', [HomeTsunamiController::class, 'index']);
Route::get('/home-volcano', [HomeVolcanoController::class, 'index']);

Route::get('/feed', [FeedApiController::class, 'index']);
