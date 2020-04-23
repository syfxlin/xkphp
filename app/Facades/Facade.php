<?php

namespace App\Facades;

use App\Application;
use RuntimeException;

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
     * @param   string  $name       方法名
     * @param   array   $arguments  方法参数
     *
     * @return  mixed
     */
    public static function __callStatic(string $name, array $arguments)
    {
        $class = self::$app->make(
            static::getFacadeAccessor(),
            static::getArgs()
        );
        if (static::isStatic()) {
            return $class::$name(...$arguments);
        }
        return $class->$name(...$arguments);
    }

    /**
     * 获取门面代理的类名
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        throw new RuntimeException(
            'Facade does not implement getFacadeAccessor method'
        );
    }

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
