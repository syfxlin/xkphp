<?php

namespace App\Facades;

use App\Kernel\EventDispatcher;

/**
 * Class Event
 * @package App\Facades
 *
 * @method static void listen(string $event, $listener)
 * @method static void has(string $event)
 * @method static array dispatch($event, $args = [])
 * @method static array getListener(string $event)
 * @method static void subscribe(string $subscriber)
 *
 * @see \App\Kernel\EventDispatcher
 */
class Event extends Facade
{
    /**
     * @inheritDoc
     */
    protected static function getFacadeAccessor(): string
    {
        return EventDispatcher::class;
    }
}
