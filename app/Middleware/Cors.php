<?php

namespace App\Middleware;

use App\Kernel\MiddlewareRunner;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class Cors implements MiddlewareInterface
{
    /**
     * @param ServerRequestInterface $request
     * @param MiddlewareRunner $next
     * @return ResponseInterface
     */
    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $next
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
