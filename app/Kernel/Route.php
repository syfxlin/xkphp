<?php

namespace App\Kernel;

use FastRoute\RouteCollector;
use FastRoute\Dispatcher;
use App\Controllers\Controller;

use function FastRoute\simpleDispatcher;

class Route
{
    protected static $route;
    protected static $globalMiddlewares;
    protected static $routeMiddlewares;

    public function __construct()
    {
        $middleware_config = require_once __DIR__ . "/../../config/middleware.php";
        self::$globalMiddlewares = $middleware_config['global'];
        self::$routeMiddlewares = $middleware_config['route'];
        $dispatcher = simpleDispatcher(function (RouteCollector $r) {
            self::$route = $r;
            require_once __DIR__ . "/../Route/web.php";
            require_once __DIR__ . "/../Route/api.php";
        });

        $request_method = $_SERVER['REQUEST_METHOD'];
        $request_uri = $_SERVER['REQUEST_URI'];

        if (false !== $pos = strpos($request_uri, '?')) {
            $request_uri = substr($request_uri, 0, $pos);
        }
        $request_uri = rawurldecode($request_uri);

        $response = $this->handleRequest($dispatcher, $request_method, $request_uri);

        $response->emit();
    }

    private function handleRequest($dispatcher, string $request_method, string $request_uri)
    {
        list($code, $handler, $path_param) = array_pad($dispatcher->dispatch($request_method, $request_uri), 3, null);

        $request = Request::getInstance([
            'path_param' => $path_param,
            'query_param' => $_GET,
            'body_param' => $_POST,
            'server_param' => $_SERVER,
            'uploaded_files' => $_FILES
        ]);

        switch ($code) {
            case Dispatcher::NOT_FOUND:
                $result = [
                    'status' => 404,
                    'message' => 'Not Found',
                    'errors' => [
                        sprintf('The URI "%s" was not found', $request_uri)
                    ]
                ];
                break;
            case Dispatcher::METHOD_NOT_ALLOWED:
                $allowedMethods = $handler;
                $result = [
                    'status' => 405,
                    'message' => 'Method Not Allowed',
                    'errors' => [
                        sprintf('Method "%s" is not allowed', $request_method)
                    ]
                ];
                break;
            case Dispatcher::FOUND:
                $result = call_user_func($handler, $request);
                break;
        }
        return is_object($result) && get_class($result) === 'App\Middleware\Response' ? $result : response($result);
    }

    public static function __callStatic($name, $arguments)
    {
        $route_item = new RouteItem(self::$route, self::$routeMiddlewares, self::$globalMiddlewares);
        return $route_item->$name(...$arguments);
    }
}

class RouteItem
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
        self::any($old_route, function () use ($new_route, $code) {
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
}
