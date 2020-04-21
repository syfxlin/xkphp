<?php

namespace App\Middleware;

use App\Facades\App;
use App\Http\CookieManager;
use App\Http\Request;
use App\Kernel\MiddlewareRunner;
use Psr\Http\Message\ResponseInterface;

class AddQueuedCookies extends Middleware
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
        $cookies = App::make(CookieManager::class)->getQueues();
        return $response->withCookies($cookies);
    }
}
