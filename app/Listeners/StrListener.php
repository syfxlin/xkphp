<?php

namespace App\Listeners;

use function report;

class StrListener implements Listener
{
    /**
     * @inheritDoc
     */
    public function handle($event, array $args = []): void
    {
        report('debug', 'str-listener');
    }
}
