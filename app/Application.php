<?php

namespace App;

use App\Facades\Crypt;
use Dotenv\Dotenv;
use App\Kernel\Container;
use App\Kernel\Http\CookieManager;
use App\Kernel\Http\Request;
use App\Kernel\Http\SessionManager;
use RuntimeException;

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
        self::$app->singleton(Request::class, function () {
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
        });
        self::$app->singleton(CookieManager::class, function () {
            return CookieManager::make();
        });
        self::$app->singleton(SessionManager::class, function () {
            // Session
            $session_config = config('session');
            $cookies = Application::make(Request::class)->getCookieParams();
            $session_id = $cookies[$session_config['name'] ?? session_name()] ?? null;
            if (isset($session_config['id'])) {
                $session_id = $session_config['id'];
                unset($session_config['id']);
            }
            return SessionManager::make($session_id, $session_config);
        });
        self::bootInstance();
        return self::$app;
    }

    /**
     * 预加载实例
     *
     * @return  void
     */
    public static function bootInstance(): void
    {
        foreach (self::$bootInstanceClass as $class) {
            if (!self::$app->has($class)) {
                self::$app->singleton($class)->make($class);
            }
        }
    }

    /**
     * 加载 env 配置文件
     *
     * @return  void
     */
    public static function bootDotenv(): void
    {
        $dotenv = Dotenv::createImmutable(__DIR__ . '/..');
        $dotenv->load();
    }

    public static function __callStatic($name, $arguments)
    {
        if (!isset(self::$app)) {
            self::boot();
        }
        return self::$app->$name(...$arguments);
    }
}
