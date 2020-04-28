<?php

namespace App\Kernel;

use App\Facades\App;
use function get_class;

trait AspectProxy
{
    public function __call($name, $arguments)
    {
        $class = get_class($this);
        $handler = new AspectHandler(
            $this,
            $class,
            $name,
            App::make(AspectManager::class)->getPoint($class, $name),
            $arguments
        );
        return $handler->invokeAspect();
    }

    public function _handle(string $method, $arguments)
    {
        return $this->$method(...$arguments);
    }

    public function _getTarget(): AspectProxy
    {
        return $this;
    }
}
