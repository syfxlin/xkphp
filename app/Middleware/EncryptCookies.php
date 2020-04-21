<?php

namespace App\Middleware;

use App\Facades\Crypt;
use App\Http\Cookie;
use App\Http\Request;
use App\Kernel\MiddlewareRunner;
use Psr\Http\Message\ResponseInterface;
use function array_map;

class EncryptCookies extends Middleware
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
        $response_cookies = $response->getCookies();
        $response_cookies = array_map(function (Cookie $cookie) {
            return $cookie->withValue(Crypt::encrypt($cookie->getValue()));
        }, $response_cookies);
        return $response->withCookies($response_cookies);
    }
}
