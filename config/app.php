<?php

use App\Providers\AnnotationProvider;
use App\Providers\DatabaseProvider;
use App\Providers\DotEnvProvider;
use App\Providers\RequestProvider;
use App\Providers\RouteProvider;

return [
    'name' => env('APP_NAME', 'XK-PHP'),
    'env' => env('APP_ENV', 'production'),
    'url' => env('APP_URL', 'http://localhost'),
    'asset_url' => env('ASSET_URL', null),
    'providers' => [
        DotEnvProvider::class,
        RequestProvider::class,
        AnnotationProvider::class,
        DatabaseProvider::class,
        RouteProvider::class
    ]
];
