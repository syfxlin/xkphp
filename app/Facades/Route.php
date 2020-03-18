<?php

namespace App\Facades;

use App\Kernel\Route as KernelRoute;
use App\Kernel\RouteManager;

class Route
{
    public static function __callStatic($name, $arguments)
    {
        $route_item = new KernelRoute(
            RouteManager::$route,
            RouteManager::$routeMiddlewares,
            RouteManager::$globalMiddlewares
        );
        return $route_item->$name(...$arguments);
    }
}
