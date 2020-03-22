<?php

return [
    'global' => [
        \App\Middleware\VerifyCsrfToken::class
    ],
    'route' => [
        'test' => \App\Middleware\TestMiddleware::class,
        'auth' => \App\Middleware\Authenticate::class,
        'guest' => \App\Middleware\RedirectIfAuthenticated::class
    ]
];
