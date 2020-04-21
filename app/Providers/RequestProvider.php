<?php

namespace App\Providers;

use App\Facades\App;
use App\Facades\Crypt;
use App\Http\CookieManager;
use App\Http\Request;
use App\Http\SessionManager;
use RuntimeException;
use function array_map;
use function config;
use function session_name;

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
        $this->app->singleton(
            CookieManager::class,
            function () {
                return CookieManager::make();
            },
            'cookie'
        );
        $this->app->singleton(
            SessionManager::class,
            function () {
                // Session
                $session_config = config('session');
                $cookies = App::make(Request::class)->getCookieParams();
                $session_id =
                    $cookies[$session_config['name'] ?? session_name()] ?? null;
                if (isset($session_config['id'])) {
                    $session_id = $session_config['id'];
                    unset($session_config['id']);
                }
                return SessionManager::make($session_id, $session_config);
            },
            'session'
        );
    }
}
