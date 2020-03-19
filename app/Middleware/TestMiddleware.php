<?php

namespace App\Middleware;

use Closure;

class TestMiddleware
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);
        echo "Test-End\n";
        return $response;
    }
}
