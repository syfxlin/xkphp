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
                return Request::make();
            },
            'request'
        );
    }
}
