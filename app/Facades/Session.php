<?php

namespace App\Facades;

/**
 * Class Session
 * @package App\Facades
 *
 * @method static string getId()
 * @method static void setId(string $id)
 * @method static string getName()
 * @method static void setName(string $name)
 * @method static array getData()
 * @method static void setData(array $data)
 * @method static bool has(string $name)
 * @method static bool exists(string $name)
 * @method static mixed get(string $name, $default = null)
 * @method static void put(string $key, $value)
 * @method static void forget($name)
 * @method static void flush()
 * @method static string regenerate()
 * @method static mixed pull(string $name, $default = null)
 * @method static string token()
 * @method static void regenerateToken()
 *
 * @see \App\Http\SessionManager
 */
class Session extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \App\Http\SessionManager::class;
    }
}
