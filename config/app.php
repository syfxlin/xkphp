<?php

use App\Providers\AnnotationProvider;
use App\Providers\AppProvider;
use App\Providers\AuthProvider;
use App\Providers\CookieProvider;
use App\Providers\DatabaseProvider;
use App\Providers\EncryptionProvider;
use App\Providers\FileProvider;
use App\Providers\HashProvider;
use App\Providers\JwtProvider;
use App\Providers\RequestProvider;
use App\Providers\RouteProvider;
use App\Providers\SessionProvider;
use App\Providers\StorageProvider;

return [
    'name' => env('APP_NAME', 'XK-PHP'),
    'env' => env('APP_ENV', 'production'),
    'url' => env('APP_URL', 'http://localhost'),
    'asset_url' => env('ASSET_URL', null),
    'providers' => [
        RequestProvider::class,
        CookieProvider::class,
        SessionProvider::class,
        AnnotationProvider::class,
        DatabaseProvider::class,
        AuthProvider::class,
        EncryptionProvider::class,
        FileProvider::class,
        HashProvider::class,
        JwtProvider::class,
        StorageProvider::class,

        // App
        AppProvider::class,
        RouteProvider::class
    ]
];
