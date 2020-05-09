<?php

use App\Listeners\StrListener;

return [
    'listeners' => [
        'event.str_config' => StrListener::class,
    ],
    'subscribers' => [],
];
