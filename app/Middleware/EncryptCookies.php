<?php

namespace App\Middleware;

use App\Facades\App;
use App\Facades\Crypt;
use App\Http\Cookie;
use App\Http\Request;
use App\Kernel\MiddlewareRunner;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;
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
        return $this->encrypt($next($this->decrypt($request)));
    }

    protected function decrypt(Request $request): Request
    {
        $request_cookies = $request->getCookieParams();
        $request_cookies = array_map(function ($cookie) {
            try {
                return Crypt::decrypt($cookie);
            } catch (RuntimeException $e) {
                return $cookie;
            }
        }, $request_cookies);
        $request = $request->withCookieParams($request_cookies);
        App::instance(Request::class, $request, 'request', true);
        return $request;
    }

    protected function encrypt(ResponseInterface $response): ResponseInterface
    {
        $response_cookies = $response->getCookies();
        $response_cookies = array_map(function (Cookie $cookie) {
            return $cookie->withValue(Crypt::encrypt($cookie->getValue()));
        }, $response_cookies);
        return $response->withCookies($response_cookies);
    }
}
