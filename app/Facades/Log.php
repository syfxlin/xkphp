<?php

namespace App\Facades;

use App\Utils\Logger;

/**
 * Class Log
 * @package App\Facades
 *
 * @method static void emergency($message, array $context = [])
 * @method static void alert($message, array $context = [])
 * @method static void critical($message, array $context = [])
 * @method static void error($message, array $context = [])
 * @method static void warning($message, array $context = [])
 * @method static void notice($message, array $context = [])
 * @method static void info($message, array $context = [])
 * @method static void debug($message, array $context = [])
 * @method static void log($level, $message, array $context = [])
 *
 * @see \App\Utils\Logger
 */
class Log extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return Logger::class;
    }
}
