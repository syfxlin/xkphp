<?php

namespace App\Providers;

use App\Utils\JWT;
use function base64_decode;
use function config;

class JwtProvider extends Provider
{
    public function register(): void
    {
        $this->app->singleton(
            JWT::class,
            function () {
                return new JWT(
                    base64_decode(config('app.key')),
                    config('app.jwt_algo'),
                    config('app.jwt_payload')
                );
            },
            'jwt'
        );
    }
}
