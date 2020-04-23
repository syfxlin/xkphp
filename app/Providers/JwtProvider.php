<?php

namespace App\Providers;

use App\Utils\JWT;
use function base64_decode;
use function env;

class JwtProvider extends Provider
{
    public function register(): void
    {
        $this->app->singleton(
            JWT::class,
            function () {
                return new JWT(
                    base64_decode(env('APP_KEY')),
                    env('APP_JWT', 'HS256')
                );
            },
            'jwt'
        );
    }
}
