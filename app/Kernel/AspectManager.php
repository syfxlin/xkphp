<?php

namespace App\Kernel;

use App\Application;
use App\Aspect\Aspect;
use function array_map;
use function array_merge;
use function is_string;
use function str_parse_callback;

class AspectManager
{
    /**
     * @var Application
     */
    protected static $app;

    /**
     * @var array<string, array<string, Aspect>>
     *
     * ["HomeController" => ["aspect" => new Aspect()]]
     */
    protected static $point_map = [];

    public function __construct(Application $app)
    {
        self::$app = $app;
    }

    public function putPoint($point, Aspect $aspect): void
    {
        if (is_string($point)) {
            [$class, $method] = str_parse_callback($point);
            $point = [$class => $method];
        }
        foreach ($point as $class => $method) {
            if (is_string($method)) {
                $method = [$method];
            }
            foreach ($method as $item) {
                self::$point_map[$class][$item][] = $aspect;
            }
        }
    }

    public function getPoint($class, $method = null)
    {
        if (!self::has($class, $method)) {
            return [];
        }
        if ($method === null) {
            return self::$point_map[$class];
        }
        return self::$point_map[$class][$method];
    }

    public static function has($class, $method = null): bool
    {
        if ($method === null) {
            return isset(self::$point_map[$class]);
        }
        return isset(self::$point_map[$class][$method]);
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
