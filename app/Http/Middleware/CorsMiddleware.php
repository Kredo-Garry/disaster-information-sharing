<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CorsMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $origin = $request->headers->get('Origin');

        // ✅ 許可するOrigin（必要なら増やす）
        $allowedOrigins = [
            'http://localhost:3000',
            'http://127.0.0.1:3000',
        ];

        // credentials を使うので '*' は絶対に返さない
        $allowOriginHeader = in_array($origin, $allowedOrigins, true)
            ? $origin
            : 'http://localhost:3000';

        $headers = [
            'Access-Control-Allow-Origin' => $allowOriginHeader,
            'Access-Control-Allow-Credentials' => 'true',
            'Access-Control-Allow-Methods' => 'GET, POST, PUT, PATCH, DELETE, OPTIONS',
            'Access-Control-Allow-Headers' => 'Content-Type, Authorization, X-Requested-With, X-XSRF-TOKEN, X-CSRF-TOKEN',
            'Vary' => 'Origin',
        ];

        // ✅ Preflight は即時返す
        if ($request->getMethod() === 'OPTIONS') {
            return response('', 204)->withHeaders($headers);
        }

        $response = $next($request);

        // ✅ ここが最後に走るので、他が '*' を付けても上書きできる
        return $response->withHeaders($headers);
    }
}
