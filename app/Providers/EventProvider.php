<?php

namespace App\Providers;

use App\Kernel\EventDispatcher;

class EventProvider extends Provider
{
    public function register(): void
    {
        $this->app->singleton(
            EventDispatcher::class,
            function () {
                return new EventDispatcher($this->app);
            },
            'event'
        );
    }

    public function boot(): void
    {
        $this->app->make(EventDispatcher::class);
    }
}
