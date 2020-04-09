<?php

namespace App\Middleware;

use App\Facades\Crypt;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class EncryptCookies implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $next): ResponseInterface
    {
        $response = $next($request);
        $response_cookies = $response->getCookies();
        $response_cookies = array_map(function ($cookie) {
            return $cookie->withValue(Crypt::encrypt($cookie->getValue()));
        }, $response_cookies);
        return $response->withCookies($response_cookies);
    }
}
