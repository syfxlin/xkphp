<?php

namespace App\Facades;

use App\Application;
use App\Kernel\AspectManager;

/**
 * Class Facade
 * @package App\Facades
 */
abstract class Facade
{
    /**
     * @var Application
     */
    protected static $app;

    public static function setApplication(Application $app): void
    {
        self::$app = $app;
    }

    /**
     * Facade 代理
     *
     * @param string $method
     * @param array $arguments 方法参数
     *
     * @return  mixed
     */
    public static function __callStatic(string $method, array $arguments)
    {
        $class = static::getFacadeAccessor();
        $instance = self::$app->make($class, static::getArgs());
        if (static::isStatic()) {
            return $instance::$method(...$arguments);
        }
        if (
            !self::$app->has(AspectManager::class) ||
            !AspectManager::has($class, $method)
        ) {
            return $instance->$method(...$arguments);
        }
        return AspectManager::weavingAspectWithClosure(
            function () use ($instance, $method, $arguments) {
                return $instance->$method(...$arguments);
            },
            [$class, $method]
        );
    }

    /**
     * 获取门面代理的类名
     *
     * @return string
     */
    abstract protected static function getFacadeAccessor(): string;

    /**
     * 动态获取构造器或者 getInstance 参数
     *
     * @return  array
     */
    protected static function getArgs(): array
    {
        return [];
    }

    protected static function isStatic(): bool
    {
        return false;
    }
}
