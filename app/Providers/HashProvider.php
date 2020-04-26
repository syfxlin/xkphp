<?php

namespace App\Providers;

use App\Utils\Hash;
use function config;

class HashProvider extends Provider
{
    public function register(): void
    {
        $this->app->singleton(
            Hash::class,
            function () {
                return new Hash(
                    config('app.hash_algo'),
                    config('app.hash_options')
                );
            },
            'hash'
        );
    }
}
