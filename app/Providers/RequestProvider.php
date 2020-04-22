<?php

namespace App\Providers;

use App\Facades\Crypt;
use App\Http\Request;
use RuntimeException;
use function array_map;

class RequestProvider extends Provider
{
    public function register(): void
    {
        $this->app->singleton(
            Request::class,
            function () {
                $request = Request::make();
                // Decrypt Cookies
                $request_cookies = $request->getCookieParams();
                $request_cookies = array_map(function ($cookie) {
                    try {
                        return Crypt::decrypt($cookie);
                    } catch (RuntimeException $e) {
                        return $cookie;
                    }
                }, $request_cookies);
                $request = $request->withCookieParams($request_cookies);
                return $request;
            },
            'request'
        );
    }
}
