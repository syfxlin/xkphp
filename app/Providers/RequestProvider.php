<?php

namespace App\Providers;

use App\Http\Request;

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
