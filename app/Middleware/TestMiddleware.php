<?php

namespace App\Middleware;

use Closure;

class TestMiddleware
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);
        return $response;
    }
}
