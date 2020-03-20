<?php

return [
    'save_path' => null,
    'life_time' => env('SESSION_LIFETIME', 1440),
    'cookie' => env(
        'SESSION_COOKIE',
        str_replace(' ', '_', env('APP_NAME', 'xk')) . '_session'
    ),
    'path' => '/',
    'domain' => env('SESSION_DOMAIN', null),
    'secure' => env('SESSION_SECURE_COOKIE', false),
    'http_only' => true
];
