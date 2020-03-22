<?php

return [
    /**
     * 全局中间件
     */
    'global' => [
        \App\Middleware\VerifyCsrfToken::class
    ],
    /**
     * 在 Route 中按需引入的中间件
     */
    'route' => [
        'auth' => \App\Middleware\Authenticate::class,
        'guest' => \App\Middleware\RedirectIfAuthenticated::class
    ]
];
