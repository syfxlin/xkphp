<?php

namespace App;

use Dotenv\Dotenv;

class Application
{
    /**
     * 存储 App 中所有的单例 instance
     *
     * @var array
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
     * @return  array  $app
     */
    public static function boot(): array
    {
        // 若已启动则直接返回
        if (isset($app)) {
            return self::$app;
        }
        self::bootDotenv();
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
            if (!isset(self::$app[$class])) {
                self::$app[$class] = new $class();
            }
        }
    }

    /**
     * 获取 instance 单例，若未启动则需要加载
     *
     * @param   string  $class  类名
     *
     * @return  mixed           对应类的实例
     */
    public static function getInstance(string $class, ...$args)
    {
        if (isset(self::$app[$class])) {
            return self::$app[$class];
        }
        return self::$app[$class] = new $class(...$args);
    }

    /**
     * 加载 env 配置文件
     *
     * @return  void
     */
    public static function bootDotenv(): void
    {
        $dotenv = Dotenv::createImmutable(__DIR__ . "/..");
        $dotenv->load();
    }
}
