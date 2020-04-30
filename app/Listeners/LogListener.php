<?php

namespace App\Listeners;

use App\Events\LogEvent;

class LogListener implements Listener
{
    /**
     * @inheritDoc
     * @param LogEvent $event
     */
    public function handle($event, array $args = []): void
    {
        $event->log();
    }
}
