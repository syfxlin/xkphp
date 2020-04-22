<?php

use App\Providers\AnnotationProvider;
use App\Providers\CookieProvider;
use App\Providers\DatabaseProvider;
use App\Providers\DotEnvProvider;
use App\Providers\RequestProvider;
use App\Providers\RouteProvider;
use App\Providers\SessionProvider;

return [
    'name' => env('APP_NAME', 'XK-PHP'),
    'env' => env('APP_ENV', 'production'),
    'url' => env('APP_URL', 'http://localhost'),
    'asset_url' => env('ASSET_URL', null),
    'providers' => [
        DotEnvProvider::class,
        RequestProvider::class,
        CookieProvider::class,
        SessionProvider::class,
        AnnotationProvider::class,
        DatabaseProvider::class,
        RouteProvider::class
    ]
];
