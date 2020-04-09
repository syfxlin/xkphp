<?php

namespace App\Middleware;

use App\Application;
use App\Facades\Crypt;
use App\Kernel\Http\CookieManager;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class AddQueuedCookies implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $next): ResponseInterface
    {
        $response = $next($request);
        $cookies = Application::make(CookieManager::class)->getQueues();
        return $response->withCookies($cookies);
    }
}
