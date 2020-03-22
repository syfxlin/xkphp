<?php

namespace App\Facades;

use App\Kernel\RouteManager;

class Route extends Facade
{
    protected static $class = \App\Kernel\Route::class;

    public static function getArgs()
    {
        return [
            RouteManager::$route,
            RouteManager::$routeMiddlewares,
            RouteManager::$globalMiddlewares
        ];
    }
}
