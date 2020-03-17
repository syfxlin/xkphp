<?php

namespace App\Middleware;

use Closure;

class TestMiddleware
{
    public function handle($request, Closure $next)
    {
        echo "Test-Start\n";
        $response = $next($request);
        echo "Test-End\n";
        return $response;
    }
}