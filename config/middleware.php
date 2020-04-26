<?php

use App\Middleware\AddQueuedCookies;
use App\Middleware\Authenticate;
use App\Middleware\Cors;
use App\Middleware\EncryptCookies;
use App\Middleware\RedirectIfAuthenticated;
use App\Middleware\StartSession;
use App\Middleware\VerifyCsrfToken;

return [
    /**
     * 全局中间件
     */
    'global' => [
        // Cors::class, // 若要跨域，需要关闭 CSRF中间件，如果不关闭则必须将 CSRF 的中间件放置于 CORS 之后
        EncryptCookies::class,
        AddQueuedCookies::class,
        StartSession::class,
        VerifyCsrfToken::class
    ],
    /**
     * 在 Route 中按需引入的中间件
     */
    'route' => [
        'auth' => Authenticate::class,
        'guest' => RedirectIfAuthenticated::class
    ]
];
