<?php

namespace App\Listeners;

use App\Contracts\Subscriber;
use App\Events\LogEvent;
use App\Kernel\EventDispatcher;

class LogSubscriber implements Subscriber
{
    /**
     * @param LogEvent $event
     * @param array $args
     */
    public function handle1($event, array $args = []): void
    {
        $event->log();
    }

    /**
     * @param LogEvent $event
     * @param array $args
     */
    public function handle2($event, array $args = []): void
    {
        $event->log();
    }

    /**
     * @inheritDoc
     */
    public function subscribe(EventDispatcher $dispatcher): array
    {
        return [
            LogEvent::class => [
                [self::class, 'handle1'],
                [self::class, 'handle2']
            ]
        ];
    }
}
