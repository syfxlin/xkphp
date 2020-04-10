<?php

namespace App;

use App\Facades\App;
use App\Facades\Crypt;
use Dotenv\Dotenv;
use App\Kernel\Container;
use App\Http\CookieManager;
use App\Http\Request;
use App\Http\SessionManager;
use RuntimeException;

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
 * @method static mixed call($method, array $args = [])
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
     * 在 App 启动时预加载的类，如 Route，和 Database
     *
     * @var array
     */
    protected static $bootInstanceClass = [
        \App\Database\DB::class,
        \App\Kernel\RouteManager::class
    ];

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
        self::bootDotenv();
        self::bootRequest();
        self::bootInstance();
        return self::$app;
    }

    /**
     * 预加载实例
     *
     * @return  void
     */
    protected static function bootInstance(): void
    {
        foreach (self::$bootInstanceClass as $class) {
            if (!self::has($class)) {
                self::singleton($class)->make($class);
            }
        }
    }

    /**
     * 加载 env 配置文件
     *
     * @return  void
     */
    protected static function bootDotenv(): void
    {
        $dotenv = Dotenv::createImmutable(__DIR__ . '/..');
        $dotenv->load();
    }

    protected static function bootRequest(): void
    {
        self::singleton(
            Request::class,
            function () {
                $request = Request::make();
                // Decrypt Cookies
                $request_cookies = $request->getCookieParams();
                $request_cookies = array_map(function ($cookie) {
                    try {
                        return Crypt::decrypt($cookie);
                    } catch (RuntimeException $e) {
                        return $cookie;
                    }
                }, $request_cookies);
                $request = $request->withCookieParams($request_cookies);
                return $request;
            },
            'request'
        );
        self::singleton(CookieManager::class, function () {
            return CookieManager::make();
        });
        self::singleton(SessionManager::class, function () {
            // Session
            $session_config = config('session');
            $cookies = App::make(Request::class)->getCookieParams();
            $session_id =
                $cookies[$session_config['name'] ?? session_name()] ?? null;
            if (isset($session_config['id'])) {
                $session_id = $session_config['id'];
                unset($session_config['id']);
            }
            return SessionManager::make($session_id, $session_config);
        });
    }

    public static function __callStatic($name, $arguments)
    {
        if (!isset(self::$app)) {
            self::boot();
        }
        return self::$app->$name(...$arguments);
    }
}
