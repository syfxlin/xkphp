<?php

namespace App\Facades;

use App\Http\CookieManager;

/**
 * Class Cookie
 * @package App\Facades
 *
 * @method static bool has(string $name)
 * @method static string|null get(string $name, $default = null)
 * @method static void put(\App\Http\Cookie $cookie)
 * @method static void forever(\App\Http\Cookie $cookie)
 * @method static void forget($name)
 * @method static bool hasQueue(string $name)
 * @method static void unqueue(string $name)
 * @method static void queue(\App\Http\Cookie $cookie)
 * @method static \App\Http\Cookie queued(string $name, \App\Http\Cookie $default = null)
 * @method static array getQueues()
 *
 * @see \App\Http\CookieManager
 */
class Cookie extends Facade
{
    public static function getFacadeAccessor(): string
    {
        return CookieManager::class;
    }
}
