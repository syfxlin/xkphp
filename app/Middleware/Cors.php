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
        return $response->setHeaders([
            'Access-Control-Allow-Origin' => $request->server(
                'HTTP_ORIGIN',
                '*'
            ),
            'Access-Control-Allow-Credentials' => 'true',
            'Access-Control-Allow-Headers' => '*',
            'Access-Control-Allow-Methods' => '*'
        ]);
    }
}
