<?php

namespace App\Contracts;

use App\Kernel\EventDispatcher;

interface Subscriber
{
    /**
     * @param EventDispatcher $dispatcher
     * @return array
     */
    public function subscribe(EventDispatcher $dispatcher): array;
}
