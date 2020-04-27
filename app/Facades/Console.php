<?php

namespace App\Facades;

use Throwable;

/**
 * Class Console
 * @package App\Facades
 *
 * @method static void error(Throwable $e)
 * @method static void fatal(Throwable $e)
 * @method static void warn(string $title = 'null', string $message = 'null', ...$objects)
 * @method static void info(string $title = 'null', string $message = 'null', ...$objects)
 * @method static void debug(string $title = 'null', string $message = 'null', ...$objects)
 *
 * @see \App\Utils\Console
 */
class Console extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \App\Utils\Console::class;
    }
}
