<?php

namespace App\Providers;

use App\Utils\Crypt;
use function base64_decode;
use function config;
use function env;

class EncryptionProvider extends Provider
{
    public function register(): void
    {
        $this->app->singleton(
            Crypt::class,
            function () {
                return new Crypt(
                    base64_decode(config('app.key')),
                    config('app.cipher')
                );
            },
            'crypt'
        );
    }
}
