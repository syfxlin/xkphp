<?php

namespace App\Events;

use App\Contracts\Event;
use function report;

class LogEvent implements Event
{
    public function __construct()
    {
    }

    public function log(): void
    {
        report('debug', 'log-event');
    }
}
