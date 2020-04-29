<?php

use App\Aspect\LogAspect;
use App\Controllers\HomeController;
use App\Utils\Crypt;
use App\Utils\Hash;

return [
    LogAspect::class => [
        HomeController::class => 'aspect',
        Hash::class => 'make',
        Crypt::class => 'encrypt'
    ]
];
