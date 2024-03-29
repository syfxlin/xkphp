<?php

namespace App\Kernel;

use App\Contracts\Responseable;
use App\Facades\App;
use Closure;
use Psr\Http\Message\ResponseInterface;
use function array_merge;
use function is_array;
use function is_string;
use function redirect;
use function response;
use function strpos;
use function view;

class Route
{
    /**
     * 组路由中间件
     *
     * @var array|null
     */
    public static $useGroupMiddlewares = null;

    /**
     * Group prefix
     *
     * @var string|null
     */
    public $prefix = null;

    /**
     * 当前路由使用的中间件
     *
     * @var array
     */
    public $middlewares = [];

    /**
     * 中间件处理器
     *
     * @param   Closure  $handler  分配到路由的处理事件闭包
     *
     * @return  Closure            增加了中间件的处理事件闭包
     */
    protected function getHandle(Closure $handler): Closure
    {
        // 注册组中间件
        if (self::$useGroupMiddlewares !== null) {
            $this->middlewares = array_merge(
                $this->middlewares,
                self::$useGroupMiddlewares
            );
        }
        return function ($request) use ($handler) {
            // Make response handler
            $handler = function ($request) use ($handler) {
                $result = $handler($request);
                // 构建响应
                return $this->toResponse($result);
            };
            // Make middlewares handler
            $runner = new MiddlewareRunner(
                array_merge(
                    RouteManager::$global_middlewares,
                    $this->middlewares
                )
            );
            return $runner->then($request, $handler);
        };
    }

    protected function toResponse($result): ResponseInterface
    {
        if ($result instanceof ResponseInterface) {
            return $result;
        }
        if ($result instanceof Responseable) {
            return $result->toResponse();
        }
        return response($result);
    }

    protected function registerAnnotationMiddleware(string $handler): void
    {
        if (isset(RouteManager::$annotation_middlewares[$handler])) {
            $this->middleware(RouteManager::$annotation_middlewares[$handler]);
        }
    }

    /**
     * 注册路由
     *
     * @param   array|string  $httpMethod  路由方法
     * @param   string        $route       路由 URL
     * @param   mixed         $handler     路由事件
     *
     * @return  Route
     */
    public function addRoute($httpMethod, string $route, $handler): Route
    {
        if (is_string($handler) && strpos($handler, '@') !== false) {
            // 获取完整的类方法名
            $handler = Controller::getFull($handler);
            // 注册注解中间件
            $this->registerAnnotationMiddleware($handler);
            RouteManager::$route->addRoute(
                $httpMethod,
                $route,
                $this->getHandle(function ($request) use ($handler) {
                    return Controller::invokeController($request, $handler);
                })
            );
        } else {
            RouteManager::$route->addRoute(
                $httpMethod,
                $route,
                $this->getHandle(function ($request) use ($handler) {
                    return App::callWithRequest($request, $handler);
                })
            );
        }
        return $this;
    }

    /**
     * 注册 GET 方法的路由
     *
     * @param   string  $route    Route URL
     * @param   mixed   $handler  路由事件
     *
     * @return  Route
     */
    public function get(string $route, $handler): Route
    {
        $this->addRoute('GET', $route, $handler);
        return $this;
    }

    /**
     * 注册 POST 方法的路由
     *
     * @param   string  $route    Route URL
     * @param   mixed   $handler  路由事件
     *
     * @return  Route
     */
    public function post(string $route, $handler): Route
    {
        $this->addRoute('POST', $route, $handler);
        return $this;
    }

    /**
     * 注册 PUT 方法的路由
     *
     * @param   string  $route    Route URL
     * @param   mixed   $handler  路由事件
     *
     * @return  Route
     */
    public function put(string $route, $handler): Route
    {
        $this->addRoute('PUT', $route, $handler);
        return $this;
    }

    /**
     * 注册 DELETE 方法的路由
     *
     * @param   string  $route    Route URL
     * @param   mixed   $handler  路由事件
     *
     * @return  Route
     */
    public function delete(string $route, $handler): Route
    {
        $this->addRoute('DELETE', $route, $handler);
        return $this;
    }

    /**
     * 注册 PATCH 方法的路由
     *
     * @param   string  $route    Route URL
     * @param   mixed   $handler  路由事件
     *
     * @return  Route
     */
    public function patch(string $route, $handler): Route
    {
        $this->addRoute('PATCH', $route, $handler);
        return $this;
    }

    /**
     * 注册 HEAD 方法的路由
     *
     * @param   string  $route    Route URL
     * @param   mixed   $handler  路由事件
     *
     * @return  Route
     */
    public function head(string $route, $handler): Route
    {
        $this->addRoute('HEAD', $route, $handler);
        return $this;
    }

    /**
     * 注册部分方法的路由
     *
     * @param array $httpMethod
     * @param string $route Route URL
     * @param mixed $handler 路由事件
     *
     * @return  Route
     */
    public function match(array $httpMethod, string $route, $handler): Route
    {
        $this->addRoute($httpMethod, $route, $handler);
        return $this;
    }

    /**
     * 注册所有方法的路由
     *
     * @param   string  $route    Route URL
     * @param   mixed   $handler  路由事件
     *
     * @return  Route
     */
    public function any(string $route, $handler): Route
    {
        $this->addRoute(
            ['GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'HEAD', 'OPTIONS'],
            $route,
            $handler
        );
        return $this;
    }

    /**
     * 路由前缀
     *
     * @param   string  $prefix  路由前缀
     *
     * @return  Route
     */
    public function prefix(string $prefix): Route
    {
        $this->prefix = $prefix;
        return $this;
    }

    /**
     * 分组路由
     *
     * @param   callable  $callback  路由分组的回调函数
     *
     * @return  Route
     */
    public function group(callable $callback): Route
    {
        self::$useGroupMiddlewares = $this->middlewares;
        RouteManager::$route->addGroup($this->prefix, $callback);
        self::$useGroupMiddlewares = null;
        $this->prefix = null;
        return $this;
    }

    /**
     * 跳转路由
     *
     * @param   string  $old_route  从该路由 URL 跳转
     * @param   string  $new_route  跳转到该路由 URL
     * @param   int     $code       跳转路由响应码
     *
     * @return  Route
     */
    public function redirect(
        string $old_route,
        string $new_route,
        int $code = 301
    ): Route {
        $this->get($old_route, function () use ($new_route, $code) {
            return redirect($new_route, $code);
        });
        return $this;
    }

    /**
     * 为路由增加中间件
     *
     * @param   mixed   $name  中间件名称或中间件数组
     *
     * @return  Route
     */
    public function middleware($name): Route
    {
        if (is_array($name)) {
            foreach ($name as $n) {
                $this->middlewares[] =
                    RouteManager::$route_middlewares[$n] ?? $n;
            }
        } else {
            $this->middlewares[] =
                RouteManager::$route_middlewares[$name] ?? $name;
        }
        return $this;
    }

    /**
     * 视图路由
     *
     * @param   string  $route  Route URL
     * @param   string  $view   视图名称
     * @param   array   $data   传递到视图的数据
     *
     * @return  Route
     */
    public function view(string $route, string $view, array $data = []): Route
    {
        $this->get($route, function () use ($view, $data) {
            return view($view, $data);
        });
        return $this;
    }
}
