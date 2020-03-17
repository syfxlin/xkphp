<?php

namespace App;

use Dotenv\Dotenv;

class Application
{
    public static $app;

    protected static $bootInstanceClass = [
        \App\Middleware\Route::class,
        \App\Database\DB::class
    ];

    public static function boot()
    {
        if (isset($app)) {
            return self::$app;
        }
        self::bootDotenv();
        self::bootInstance();
        return self::$app;
    }

    public static function bootInstance()
    {
        foreach (self::$bootInstanceClass as $class) {
            if (!isset(self::$app[$class])) {
                self::$app[$class] = new $class();
            }
        }
    }

    public static function getInstance($class, ...$args)
    {
        if (isset(self::$app[$class])) {
            return self::$app[$class];
        }
        return self::$app[$class] = new $class(...$args);
    }

    public static function bootDotenv()
    {
        $dotenv = Dotenv::createImmutable(__DIR__ . "/..");
        $dotenv->load();
    }
}
