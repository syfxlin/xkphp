<?php

namespace App\Providers;

use App\Utils\Crypt;
use function base64_decode;
use function env;

class EncryptionProvider extends Provider
{
    public function register(): void
    {
        $this->app->singleton(
            Crypt::class,
            function () {
                return new Crypt(
                    base64_decode(env('APP_KEY')),
                    env('APP_CIPHER', 'AES-256-CBC')
                );
            },
            'crypt'
        );
    }
}
