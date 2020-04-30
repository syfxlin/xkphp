<?php

namespace App\Listeners;

use App\Events\Event;

interface Listener
{
    /**
     * @param $event
     * @param array $args
     * @return void
     */
    public function handle($event, array $args = []): void;
}
