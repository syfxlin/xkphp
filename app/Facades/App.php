<?php

namespace App\Facades;

use App\Application;
use App\Http\Request;
use App\Kernel\Container;
use App\Kernel\ProviderManager;

/**
 * Class Application
 * @package App\Facades
 *
 * @method static Container bind($abstract, $concrete = null, bool $shared = false, $alias = false, bool $overwrite = false)
 * @method static mixed make(string $abstract, array $args = [])
 * @method static Container singleton(string $abstract, $concrete = null, $alias = false, $overwrite = false)
 * @method static Container instance(string $abstract, $instance, $alias = false)
 * @method static mixed build($class, array $args = [])
 * @method static void useAutoBind(bool $use)
 * @method static bool has($id)
 * @method static mixed get($id)
 * @method static bool hasMethod(string $method)
 * @method static void bindMethod(string $method, $callback)
 * @method static mixed call($method, array $args = [], $object = null, $isStatic = false)
 * @method static mixed callWithRequest(Request $request, $method, array $args = [], $object = null, $isStatic = false)
 * @method static bool isAlias($name)
 * @method static void alias($abstract, $alias)
 * @method static string getAlias($abstract)
 * @method static string getAbstract($alias)
 * @method static void removeAlias($alias)
 * @method static Application boot()
 * @method static mixed callWithAspect($method, array $args = [], $object = null, bool $isStatic = false, array $aspects = [])
 * @method static Application create()
 * @method static Application getInstance()
 * @method static void setInstance(Application $application)
 * @method static mixed environment(string $env = null)
 * @method static string version()
 * @method static ProviderManager getProviderManager()
 * @method static void setProviderManager(ProviderManager $manager)
 * @method static bool isBooted()
 * @method static void booting(callable $callback)
 * @method static void booted(callable $callback)
 * @method static string getLocale()
 * @method static bool isLocale(string $locale)
 *
 * @see \App\Application
 */
class App extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return Application::class;
    }
}
