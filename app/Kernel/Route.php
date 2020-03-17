<?php

namespace App\Kernel;

use FastRoute\RouteCollector;
use FastRoute\Dispatcher;
use App\Controllers\Controller;

use function FastRoute\simpleDispatcher;

class Route
{
    protected static $instance;
    protected static $route;
    protected static $prefix;

    public function __construct()
    {
        self::$instance = $this;
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

        echo $response->content;
    }

    private function handleRequest($dispatcher, string $request_method, string $request_uri)
    {
        list($code, $handler, $path_param) = array_pad($dispatcher->dispatch($request_method, $request_uri), 3, null);
        $request = Request::getInstance([
            'path_param' => $path_param,
            'cookie_param' => $_COOKIE,
            'query_param' => $_GET,
            'body_param' => $_POST,
            'server_param' => $_SERVER,
            'uploaded_files' => $_FILES
        ]);
        $middlewares = [
            \App\Middleware\TestMiddleware::class
        ];
        foreach ($middlewares as $middleware) {
            $handler = function ($request) use ($middleware, $handler) {
                return (new $middleware())->handle($request, $handler);
            };
        }
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

    public static function addRoute($httpMethod, $route, $handler)
    {
        if (is_string($handler) && strpos($handler, '@') !== false) {
            list($c_name, $f_name) = explode('@', $handler);
            $c_name = 'App\Controllers\\' . $c_name;
            self::$route->addRoute($httpMethod, $route, function ($request) use ($c_name, $f_name) {
                return Controller::invokeController($c_name, $f_name, [], ['request' => $request]);
            });
        } else {
            self::$route->addRoute($httpMethod, $route, $handler);
        }
    }

    public static function get(string $route, $handler)
    {
        self::addRoute('GET', $route, $handler);
    }

    public static function post(string $route, $handler)
    {
        self::addRoute('POST', $route, $handler);
    }

    public static function put(string $route, $handler)
    {
        self::addRoute('PUT', $route, $handler);
    }

    public static function delete(string $route, $handler)
    {
        self::addRoute('DELETE', $route, $handler);
    }

    public static function patch(string $route, $handler)
    {
        self::addRoute('PATCH', $route, $handler);
    }

    public static function head(string $route, $handler)
    {
        self::addRoute('HEAD', $route, $handler);
    }

    public static function match(array $httpMethod, string $route, $handler)
    {
        self::addRoute($httpMethod, $route, $handler);
    }

    public static function any(string $route, $handler)
    {
        self::addRoute(['GET', 'POST', 'PUT', 'DELTE', 'PATCH'], $route, $handler);
    }

    public static function prefix($prefix)
    {
        self::$prefix = $prefix;
        return self::$instance;
    }

    public static function group(callable $callback)
    {
        self::$route->addGroup(self::$prefix, $callback);
        self::$prefix = null;
    }

    public static function redirect($old_route, $new_route, $code = 301)
    {
        self::get($old_route, function () use ($new_route, $code) {
            redirect($new_route, $code);
        });
    }
}
