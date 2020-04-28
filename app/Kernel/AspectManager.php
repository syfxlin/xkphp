<?php

namespace App\Kernel;

use App\Application;
use App\Aspect\Aspect;
use Closure;
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
        if (!$this->hasAspect($class, $method)) {
            return null;
        }
        if ($method === null) {
            return self::$pointMap[$class];
        }
        return self::$pointMap[$class][$method];
    }

    public function putAspect(Aspect $aspect): void
    {
        $this->putPoint($aspect->pointCut(), $aspect);
    }

    public function hasAspect($class, $method = null): bool
    {
        if ($method === null) {
            return isset(self::$pointMap[$class]);
        }
        return isset(self::$pointMap[$class][$method]);
    }

    public static function weavingAspectWithProxy($instance, string $class)
    {
        if (
            $class === \App\Utils\Config::class ||
            $class === self::class ||
            $instance instanceof Aspect ||
            !self::$app->has(self::class)
        ) {
            return $instance;
        }
        $point = self::$app->make(self::class)->getPoint($class);
        // 没有切面则直接返回实例
        if ($point === null) {
            return $instance;
        }
        return new AspectProxy($instance, $class);
    }

    /**
     * @param $callback
     * @param string $class
     * @param string $method
     * @param array $args
     * @return mixed
     */
    public static function weavingAspectWithClosure(
        $callback,
        string $class,
        string $method,
        array $args = []
    ) {
        $point = self::$app->make(self::class)->getPoint($class, $method);
        // 没有切面则直接返回实例
        if ($point === null) {
            return $callback($args);
        }
        return (new AspectHandler(
            $callback,
            $class,
            $method,
            $point
        ))->invokeAspect();
    }
}
