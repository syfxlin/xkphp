<?php

return [
    'global' => [
        \App\Middleware\VerifyCsrfToken::class
    ],
    'route' => [
        'test' => \App\Middleware\TestMiddleware::class
    ]
];
