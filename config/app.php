<?php

use App\Providers\AnnotationProvider;
use App\Providers\AppProvider;
use App\Providers\AspectProvider;
use App\Providers\AuthProvider;
use App\Providers\CookieProvider;
use App\Providers\DatabaseProvider;
use App\Providers\EncryptionProvider;
use App\Providers\EventProvider;
use App\Providers\FileProvider;
use App\Providers\HashProvider;
use App\Providers\JwtProvider;
use App\Providers\LocaleProvider;
use App\Providers\LoggerProvider;
use App\Providers\RequestProvider;
use App\Providers\RouteProvider;
use App\Providers\SessionProvider;
use App\Providers\StorageProvider;

return [
    'name' => env('APP_NAME', 'XK-PHP'),
    'version' => env('APP_VERSION', '1.0'),
    'locale' => env('APP_LOCALE', 'zh_CN'),
    'env' => env('APP_ENV', 'production'),
    'url' => env('APP_URL', 'http://localhost'),
    'asset_url' => env('ASSET_URL', ''),
    'key' => env('APP_KEY'),
    'cipher' => env('APP_CIPHER', 'AES-256-CBC'),
    'hash_algo' => env('APP_HASH', 'bcrypt'),
    'hash_options' => [],
    'jwt_algo' => env('APP_JWT', 'HS256'),
    'jwt_payload' => '',
    'log_to' =>
        env('APP_ENV', 'production') !== 'production' &&
        in_array(PHP_SAPI, ['cli', 'cli-server'])
            ? 'console'
            : BASE_PATH . 'storage/app.log',
    'providers' => [
        RequestProvider::class,
        CookieProvider::class,
        SessionProvider::class,
        LocaleProvider::class,
        AspectProvider::class,
        EventProvider::class,
        AnnotationProvider::class,
        DatabaseProvider::class,
        AuthProvider::class,
        EncryptionProvider::class,
        FileProvider::class,
        HashProvider::class,
        JwtProvider::class,
        StorageProvider::class,
        LoggerProvider::class,

        // App
        AppProvider::class,
        RouteProvider::class,
    ],
];
