<?php

return [
    'save_path' => '',
    'name' => env(
        'SESSION_COOKIE',
        str_replace(' ', '_', env('APP_NAME', 'xk')) . '_session'
    ),
    'cookie_lifetime' => env('SESSION_LIFETIME', 1440),
    'cookie_path' => '/',
    'cookie_domain' => env('SESSION_DOMAIN', ''),
    'cookie_secure' => env('SESSION_SECURE_COOKIE', false),
    'cookie_httponly' => true
];
