<?php

namespace App\Providers;

use App\Utils\Logger;
use function config;

class LoggerProvider extends Provider
{
    public function register(): void
    {
        $this->app->singleton(
            Logger::class,
            function () {
                return new Logger([
                    'print_to' => config('app.log_to'),
                ]);
            },
            'log'
        );
    }
}
