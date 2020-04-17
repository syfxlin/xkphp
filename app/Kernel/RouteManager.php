<?php

namespace App\Kernel;

use App\Facades\App;
use App\Http\Request;
use Closure;
use FastRoute\RouteCollector;
use FastRoute\Dispatcher;
use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

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
     * 注解中间件，按需开启
     *
     * @var array
     */
    public static $annotationMiddlewares = [];

    /**
     * 注解路由，按需开启
     *
     * @var Closure[]
     */
    public static $annotationRoute = [];

    /**
     * RouteManager 构造器，外部请勿调用
     */
    public function __construct()
    {
        $middleware_config = config('middleware');
        self::$globalMiddlewares = $middleware_config['global'];
        self::$routeMiddlewares = $middleware_config['route'];
        $dispatcher = simpleDispatcher(function (RouteCollector $r) {
            self::$route = $r;
            // 导入路由表
            $route_config = config('route');
            foreach ($route_config as $route) {
                require_once $route;
            }
            // 导入注解路由
            foreach (self::$annotationRoute as $route) {
                // 闭包执行的效果和路由表的 require 方式类似
                $route();
            }
        });

        $request = App::make(Request::class);

        $response = $this->handleRequest($dispatcher, $request);

        (new SapiEmitter())->emit($response);
    }

    /**
     * 请求处理
     *
     * @param Dispatcher $dispatcher Route Dispatcher
     * @param ServerRequestInterface $request
     * @return  ResponseInterface                     响应
     */
    private function handleRequest(
        Dispatcher $dispatcher,
        ServerRequestInterface $request
    ): ResponseInterface {
        [$code, $handler, $path_param] = array_pad(
            $dispatcher->dispatch(
                $request->getMethod(),
                rawurldecode($request->getUri()->getPath())
            ),
            3,
            null
        );

        // 修改 Request 中的 Path 参数
        if ($path_param !== null) {
            foreach ($path_param as $key => $value) {
                $request = $request->withAttribute($key, $value);
            }
        }

        $response = null;

        switch ($code) {
            case Dispatcher::NOT_FOUND:
                $response = response(
                    [
                        'status' => 404,
                        'message' => 'Not Found',
                        'errors' => [
                            sprintf(
                                'The URI "%s" was not found.',
                                $request->getUri()
                            )
                        ]
                    ],
                    404
                );
                break;
            case Dispatcher::METHOD_NOT_ALLOWED:
                $allowedMethods = $handler;
                $response = response(
                    [
                        'status' => 405,
                        'message' => 'Method Not Allowed',
                        'errors' => [
                            sprintf(
                                'Method "%s" is not allowed.',
                                $request->getMethod()
                            )
                        ]
                    ],
                    405
                );
                break;
            case Dispatcher::FOUND:
                $response = $handler($request);
                break;
        }

        return $response;
    }
}
