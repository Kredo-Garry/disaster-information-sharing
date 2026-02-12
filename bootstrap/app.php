<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware('web')
                ->group(base_path('routes/admin.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware) {

        // ✅ ログイン後、未ログイン時のリダイレクト先を制御
        $middleware->redirectTo(
            guests: '/admin/login',
            users: '/admin/dashboard'
        );

        // ✅ CSRF検証の除外（React SPA → Laravel web(session) API で 419 を避ける）
        // web.php 側に作った JSON API (/api/*) を除外する
        $middleware->validateCsrfTokens(except: [
            'api/*',
        ]);

        // ミドルウェアのエイリアス登録
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
        ]);

        /**
         * ✅ CORS を “最終的に確定させる” ため prepend
         * prepend = 外側（レスポンス時に最後に実行）なので、
         * 他が Access-Control-Allow-Origin を触ってもここで上書きできる
         */
        $middleware->prepend(\App\Http\Middleware\CorsMiddleware::class);

    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->create();
