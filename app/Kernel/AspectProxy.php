<?php

namespace App\Kernel;

use App\Facades\App;
use function get_class;

class AspectProxy
{
    /**
     * @var object
     */
    protected $target;

    /**
     * @var string
     */
    protected $class;

    public function __construct($target, string $class = null)
    {
        $this->target = $target;
        $this->class = $class ?? get_class($target);
    }

    public function __call($name, $arguments)
    {
        $handler = new AspectHandler(
            $this,
            $this->class,
            $name,
            App::make(AspectManager::class)->getPoint($this->class, $name),
            $arguments
        );
        return $handler->invokeAspect();
    }

    public function handle(string $method, $arguments)
    {
        return $this->target->$method(...$arguments);
    }

    public function getTarget()
    {
        return $this->target;
    }
}
