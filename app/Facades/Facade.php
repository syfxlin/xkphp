<?php

namespace App\Facades;

class Facade
{
    /**
     * Facade 对应的类名
     *
     * @var string|null
     */
    protected static $class;

    /**
     * 是否是单例接口
     *
     * @var bool
     */
    protected static $isInstance = false;

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
        if (!static::$isInstance) {
            return (new static::$class(...static::getArgs()))->$name(...$arguments);
        } else {
            return (static::$class::getInstance(...static::getArgs()))->$name(...$arguments);
        }
    }

    /**
     * 动态获取构造器或者 getInstance 参数
     *
     * @return  array
     */
    public static function getArgs()
    {
        return [];
    }
}
