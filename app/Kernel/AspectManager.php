<?php

namespace App\Kernel;

use App\Aspect\Aspect;
use function array_push;
use function explode;
use function is_array;
use function is_string;

class AspectManager
{
    /**
     * @var array
     */
    protected static $pointMap = [];

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
}
