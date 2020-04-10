<?php

use App\Middleware\AddQueuedCookies;
use App\Middleware\Authenticate;
use App\Middleware\EncryptCookies;
use App\Middleware\RedirectIfAuthenticated;
use App\Middleware\VerifyCsrfToken;

return [
    /**
     * 全局中间件
     */
    'global' => [
        EncryptCookies::class,
        VerifyCsrfToken::class,
        AddQueuedCookies::class
    ],
    /**
     * 在 Route 中按需引入的中间件
     */
    'route' => [
        'auth' => Authenticate::class,
        'guest' => RedirectIfAuthenticated::class
    ]
];
