<?php

namespace App\Facades;

/**
 * Class Config
 * @package App\Facades
 *
 * @method static string path(string $config_name)
 * @method static array all()
 * @method static mixed get(string $name, $default = null)
 * @method static void set($name, $value = null)
 * @method static bool has(string $name)
 * @method static void push(string $name, $value)
 *
 * @see \App\Utils\Config
 */
class Config extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \App\Utils\Config::class;
    }
}
