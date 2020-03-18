<?php

namespace App\Kernel;

use FastRoute\RouteCollector;
use FastRoute\Dispatcher;

use function FastRoute\simpleDispatcher;

class RouteManager
{
    public static $route;
    public static $globalMiddlewares;
    public static $routeMiddlewares;

    public function __construct()
    {
        $middleware_config = require_once __DIR__ . "/../../config/middleware.php";
        self::$globalMiddlewares = $middleware_config['global'];
        self::$routeMiddlewares = $middleware_config['route'];
        $dispatcher = simpleDispatcher(function (RouteCollector $r) {
            self::$route = $r;
            $route_config = require_once __DIR__ . "/../../config/route.php";
            foreach ($route_config as $route) {
                require_once $route;
            }
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

        // TODO: 处理多种类型
        return is_object($result) && get_class($result) === 'App\Kernel\Response' ? $result : response($result);
    }
}