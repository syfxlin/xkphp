<?php

namespace App\Providers;

use App\Utils\Console;
use function config;

class ConsoleProvider extends Provider
{
    public function register(): void
    {
        $this->app->singleton(
            Console::class,
            function () {
                return new Console([
                    'print_to' => config('app.log_to')
                ]);
            },
            'console'
        );
    }
}
