<?php

namespace App\Kernel;

use App\Controllers\Controller;

class Route
{
    public static $route;
    public static $globalMiddlewares;
    public static $routeMiddlewares;
    public static $useGroupMiddlewares = null;
    public $prefix = null;
    public $middlewares = [];

    public function __construct($r, $rm, $gm)
    {
        self::$route = $r;
        self::$routeMiddlewares = $rm;
        self::$globalMiddlewares = $gm;
    }

    public function getHandle($handler)
    {
        // 注册组中间件
        if (self::$useGroupMiddlewares !== null) {
            $this->middlewares = array_merge($this->middlewares, self::$useGroupMiddlewares);
        }
        return function ($request) use ($handler) {
            foreach (array_merge(self::$globalMiddlewares, $this->middlewares) as $middleware) {
                $handler = function ($request) use ($middleware, $handler) {
                    return (new $middleware())->handle($request, $handler);
                };
            }
            return $handler($request);
        };
    }

    public function addRoute($httpMethod, $route, $handler)
    {
        if (is_string($handler) && strpos($handler, '@') !== false) {
            list($c_name, $f_name) = explode('@', $handler);
            $c_name = 'App\Controllers\\' . $c_name;
            self::$route->addRoute($httpMethod, $route, $this->getHandle(function ($request) use ($c_name, $f_name) {
                return Controller::invokeController($c_name, $f_name, [], ['request' => $request]);
            }));
        } else {
            self::$route->addRoute($httpMethod, $route, $this->getHandle($handler));
        }
        return $this;
    }

    public function get(string $route, $handler)
    {
        self::addRoute('GET', $route, $handler);
        return $this;
    }

    public function post(string $route, $handler)
    {
        self::addRoute('POST', $route, $handler);
        return $this;
    }

    public function put(string $route, $handler)
    {
        self::addRoute('PUT', $route, $handler);
        return $this;
    }

    public function delete(string $route, $handler)
    {
        self::addRoute('DELETE', $route, $handler);
        return $this;
    }

    public function patch(string $route, $handler)
    {
        self::addRoute('PATCH', $route, $handler);
        return $this;
    }

    public function head(string $route, $handler)
    {
        self::addRoute('HEAD', $route, $handler);
        return $this;
    }

    public function match(array $httpMethod, string $route, $handler)
    {
        self::addRoute($httpMethod, $route, $handler);
        return $this;
    }

    public function any(string $route, $handler)
    {
        self::addRoute(['GET', 'POST', 'PUT', 'DELTE', 'PATCH'], $route, $handler);
        return $this;
    }

    public function prefix($prefix)
    {
        $this->prefix = $prefix;
        return $this;
    }

    public function group(callable $callback)
    {
        self::$useGroupMiddlewares = $this->middlewares;
        self::$route->addGroup($this->prefix, $callback);
        self::$useGroupMiddlewares = null;
        $this->prefix = null;
        return $this;
    }

    public function redirect($old_route, $new_route, $code = 301)
    {
        self::get($old_route, function () use ($new_route, $code) {
            redirect($new_route, $code);
        });
        return $this;
    }

    public function middleware($name)
    {
        if (is_array($name)) {
            foreach ($name as $n) {
                $this->middlewares[] = self::$routeMiddlewares[$n];
            }
        } else {
            $this->middlewares[] = self::$routeMiddlewares[$name];
        }
        return $this;
    }

    public function view($route, $view, $data = [])
    {
        self::get($route, function () use ($view, $data) {
            return view($view, $data);
        });
        return $this;
    }
}
