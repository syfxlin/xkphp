<?php

namespace App\Providers;

use App\Kernel\EventDispatcher;
use function config;

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
        /* @var EventDispatcher $dispatcher */
        $dispatcher = $this->app->make(EventDispatcher::class);
        $config = config('event');
        foreach ($config['listeners'] as $event => $listener) {
            $dispatcher->listen($event, $listener);
        }
        foreach ($config['subscribers'] as $subscriber) {
            $dispatcher->subscribe($subscriber);
        }
    }
}
