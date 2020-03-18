<?php

namespace App\Facades;

class Facade
{
    protected static $class;
    protected static $construct_args = [];
    public static function __callStatic($name, $arguments)
    {
        return (new static::$class(...static::$construct_args))->$name(...$arguments);
    }
}
