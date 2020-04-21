<?php

namespace App\Middleware;

use App\Http\Request;
use App\Kernel\MiddlewareRunner;
use Psr\Http\Message\ResponseInterface;

class Cors extends Middleware
{
    /**
     * @param Request $request
     * @param MiddlewareRunner $next
     * @return ResponseInterface
     */
    public function handle(
        Request $request,
        MiddlewareRunner $next
    ): ResponseInterface {
        $response = $next($request);
        return $response->withHeaders([
            'Access-Control-Allow-Origin' => $request->server(
                'HTTP_ORIGIN',
                '*'
            ),
            'Access-Control-Allow-Credentials' => 'true',
            'Access-Control-Allow-Headers' =>
                'Authorization, Content-Type, If-Match, If-Modified-Since, If-None-Match, If-Unmodified-Since, X-Requested-With',
            'Access-Control-Allow-Methods' => 'GET, POST, PATCH, PUT, DELETE'
        ]);
    }
}
