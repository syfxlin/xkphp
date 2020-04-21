<?php

namespace App\Facades;

use App\Application;
use App\Kernel\Container;

/**
 * Class Application
 * @package App\Facades
 *
 * @method static Container bind($abstract, $concrete = null, bool $shared = false, bool $alias = false)
 * @method static mixed make(string $abstract, array $args = [])
 * @method static Container singleton(string $abstract, $concrete = null, $alias = false)
 * @method static Container instance(string $abstract, $instance)
 * @method static mixed build($class, array $args = [])
 * @method static void useAutoBind(bool $use)
 * @method static bool has($id)
 * @method static mixed get($id)
 * @method static bool hasMethod(string $method)
 * @method static void bindMethod(string $method, $callback)
 * @method static mixed call($method, array $args = [], $object = null, $isStatic = false)
 * @method static bool isAlias($name)
 * @method static void alias($abstract, $alias)
 * @method static string getAlias($abstract)
 * @method static string getAbstract($alias)
 * @method static void removeAlias($alias)
 * @method static Container boot()
 *
 * @see \App\Kernel\Container
 */
class App extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return Application::class;
    }

    protected static function isStatic(): bool
    {
        return true;
    }
}
