<?php

namespace App\Facades;

use App\Kernel\RouteManager;

/**
 * @method static Route addRoute($httpMethod, string $route, $handler)
 * @method static Route get(string $route, $handler)
 * @method static Route post(string $route, $handler)
 * @method static Route put(string $route, $handler)
 * @method static Route delete(string $route, $handler)
 * @method static Route patch(string $route, $handler)
 * @method static Route head(string $route, $handler)
 * @method static Route match(array $httpMethod, string $route, $handler)
 * @method static Route any(string $route, $handler)
 * @method static Route prefix(string $prefix)
 * @method static Route group(callable $callback)
 * @method static Route redirect(string $old_route, string $new_route, int $code = 301)
 * @method static Route middleware($name)
 * @method static Route view(string $route, string $view, array $data)
 *
 * @see \App\Kernel\Route
 */
class Route extends Facade
{
    protected static $class = \App\Kernel\Route::class;

    public static function getArgs(): array
    {
        return [
            RouteManager::$route,
            RouteManager::$routeMiddlewares,
            RouteManager::$globalMiddlewares
        ];
    }
}
