<?php

namespace App\Middleware;

use App\Application;
use App\Facades\App;
use App\Facades\Crypt;
use App\Kernel\Http\CookieManager;
use App\Kernel\MiddlewareRunner;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class AddQueuedCookies implements MiddlewareInterface
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
        $cookies = App::make(CookieManager::class)->getQueues();
        return $response->withCookies($cookies);
    }
}
