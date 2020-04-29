<?php

namespace App\Kernel;

use App\Application;
use App\Aspect\Aspect;
use App\Utils\Config;
use Closure;
use function array_map;
use function array_merge;
use function array_push;
use function explode;
use function is_array;
use function is_string;

class AspectManager
{
    /**
     * @var Application
     */
    protected static $app;

    /**
     * @var array
     */
    protected static $pointMap = [];

    public function __construct(Application $app)
    {
        self::$app = $app;
    }

    public function putPoint($point, Aspect $aspect): void
    {
        if (is_string($point)) {
            [$class, $method] = explode('@', $point);
            $point = [$class => $method];
        }
        foreach ($point as $class => $method) {
            if (is_string($method)) {
                $method = [$method];
            }
            foreach ($method as $item) {
                self::$pointMap[$class][$item][] = $aspect;
            }
        }
    }

    public function getPoint($class, $method = null)
    {
        if (!self::hasAspect($class, $method)) {
            return [];
        }
        if ($method === null) {
            return self::$pointMap[$class];
        }
        return self::$pointMap[$class][$method];
    }

    public static function hasAspect($class, $method = null): bool
    {
        if ($method === null) {
            return isset(self::$pointMap[$class]);
        }
        return isset(self::$pointMap[$class][$method]);
    }

    /**
     * @param $callback
     * @param array $target
     * @param array $args
     * @param array $aspects
     * @return mixed
     */
    public static function weavingAspectWithClosure(
        $callback,
        array $target = ['', ''],
        array $args = [],
        array $aspects = []
    ) {
        $point = array_merge(
            self::$app->make(self::class)->getPoint($target[0], $target[1]),
            array_map(function ($item) {
                return self::$app->make($item);
            }, $aspects)
        );
        // 没有切面则直接返回实例
        if (empty($point)) {
            return $callback($args);
        }
        return (new AspectHandler(
            $callback,
            $target[0],
            $target[1],
            $point,
            $args
        ))->invokeAspect();
    }
}
