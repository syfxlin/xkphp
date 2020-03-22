<?php

namespace App\Kernel;

use FastRoute\RouteCollector;
use FastRoute\Dispatcher;

use function FastRoute\simpleDispatcher;

class RouteManager
{
    /**
     * FastRoute RouteCollector
     *
     * @var RouteCollector|null
     */
    public static $route;

    /**
     * 全局中间件
     *
     * @var array|null
     */
    public static $globalMiddlewares;

    /**
     * Route 中间件
     *
     * @var array|null
     */
    public static $routeMiddlewares;

    /**
     * RouteManager 构造器，外部请勿调用
     *
     * @return  this
     */
    public function __construct()
    {
        $middleware_config = config('middleware');
        self::$globalMiddlewares = $middleware_config['global'];
        self::$routeMiddlewares = $middleware_config['route'];
        $dispatcher = simpleDispatcher(function (RouteCollector $r) {
            self::$route = $r;
            $route_config = config('route');
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

    /**
     * 请求处理
     *
     * @param   Dispatcher  $dispatcher      Route Dispatchr
     * @param   string      $request_method  请求方法
     * @param   string      $request_uri     请求 URL
     *
     * @return  Response                     响应
     */
    private function handleRequest(Dispatcher $dispatcher, string $request_method, string $request_uri)
    {
        list($code, $handler, $path_param) = array_pad($dispatcher->dispatch($request_method, $request_uri), 3, null);

        $request = Request::getInstance([
            'path' => $path_param,
            'get' => $_GET,
            'post' => $_POST,
            'server' => $_SERVER,
            'files' => $_FILES
        ]);

        switch ($code) {
            case Dispatcher::NOT_FOUND:
                $response = response([
                    'status' => 404,
                    'message' => 'Not Found',
                    'errors' => [
                        sprintf('The URI "%s" was not found.', $request_uri)
                    ]
                ], 404);
                break;
            case Dispatcher::METHOD_NOT_ALLOWED:
                $allowedMethods = $handler;
                $response = response([
                    'status' => 405,
                    'message' => 'Method Not Allowed',
                    'errors' => [
                        sprintf('Method "%s" is not allowed.', $request_method)
                    ]
                ], 405);
                break;
            case Dispatcher::FOUND:
                $response = call_user_func($handler, $request);
                break;
        }

        return $response;
    }
}
