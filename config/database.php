<?php

return [
    'driver' => env('DB_CONNECTION', 'mysql'),
    'host' => env('DB_HOST', 'localhost'),
    'database' => env('DB_DATABASE', 'database'),
    'username' => env('DB_USERNAME', 'root'),
    'password' => env('DB_PASSWORD', 'password'),
    'charset' => env('DB_CHARSET', 'utf8'),
    'collation' => env('DB_COLLATION', 'utf8_unicode_ci'),
    'prefix' => env('DB_PREFIX', ''),
];
