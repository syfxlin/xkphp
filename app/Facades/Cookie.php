<?php

namespace App\Facades;

/**
 * Class Cookie
 * @package App\Facades
 *
 * @method static bool has(string $name)
 * @method static string|null get(string $name, $default = null)
 * @method static void put(\App\Kernel\Http\Cookie $cookie)
 * @method static void forever(\App\Kernel\Http\Cookie $cookie)
 * @method static void forget($name)
 * @method static bool hasQueue(string $name)
 * @method static void unqueue(string $name)
 * @method static void queue(\App\Kernel\Http\Cookie $cookie)
 * @method static \App\Kernel\Http\Cookie queued(string $name, \App\Kernel\Http\Cookie $default = null)
 * @method static array getQueues()
 *
 * @see \App\Kernel\Http\CookieManager
 */
class Cookie extends Facade
{
    public static function getFacadeAccessor(): string
    {
        return \App\Kernel\Http\CookieManager::class;
    }
}
