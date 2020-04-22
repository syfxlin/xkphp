<?php

namespace App;

use App\Database\DB;
use App\Facades\Annotation;
use App\Facades\App;
use App\Facades\Crypt;
use App\Facades\File;
use App\Facades\Route;
use App\Kernel\ProviderManager;
use App\Kernel\RouteManager;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Dotenv\Dotenv;
use App\Kernel\Container;
use App\Http\CookieManager;
use App\Http\Request;
use App\Http\SessionManager;
use ReflectionClass;
use ReflectionMethod;
use RuntimeException;
use function app_path;
use function array_map;
use function class_exists;
use function config;
use function config_path;
use function session_name;
use function str_replace;
use function strtoupper;
use function substr;

/**
 * Class Application
 * @package App
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
 *
 * @see \App\Kernel\Container
 */
class Application
{
    /**
     * 存储 App 中所有的单例 instance
     *
     * @var Container
     */
    public static $app;

    /**
     * 启动 App，程序入口
     *
     * @return  Container  $app
     */
    public static function boot(): Container
    {
        // 若已启动则直接返回
        if (isset(self::$app)) {
            return self::$app;
        }
        self::$app = new Container();
        self::bootProvider();
        return self::$app;
    }

    protected static function bootProvider(): void
    {
        $provider = new ProviderManager(self::$app, config('app.providers'));
        $provider->register();
        $provider->boot();
    }

    public static function __callStatic($name, $arguments)
    {
        if (!isset(self::$app)) {
            self::boot();
        }
        return self::$app->$name(...$arguments);
    }
}
