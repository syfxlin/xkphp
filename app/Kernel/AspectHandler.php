<?php

namespace App\Kernel;

use App\Aspect\Aspect;
use Throwable;
use function array_shift;

class AspectHandler
{
    /**
     * @var AspectProxy
     */
    protected $proxy;

    /**
     * @var string
     */
    protected $class;

    /**
     * @var string
     */
    protected $method;

    /**
     * @var array
     */
    protected $args = [];

    /**
     * @var Aspect[]
     */
    protected $aspects = [];

    /**
     * @var Aspect
     */
    protected $aspect;

    /**
     * @var Throwable
     */
    protected $error;

    public function __construct(
        AspectProxy $proxy,
        string $class,
        string $method,
        array $aspects,
        array $args = []
    ) {
        $this->proxy = $proxy;
        $this->class = $class;
        $this->method = $method;
        $this->aspect = array_shift($aspects);
        $this->aspects = $aspects;
        $this->args = $args;
    }

    public function invokeAspect()
    {
        if (!$this->aspect) {
            return null;
        }
        $result = null;
        try {
            // Around
            $result = $this->aspect->around($this->makeProceedingJoinPoint());
        } catch (Throwable $e) {
            $this->error = $e;
        }
        // After
        $this->aspect->after($this->makeJoinPoint($result));
        // After*
        if ($this->error) {
            $this->aspect->afterThrowing($this->error);
        } else {
            $this->aspect->afterReturning($this->makeJoinPoint($result));
        }
        return $result;
    }

    public function invokeProcess($args = [])
    {
        // Before
        $this->aspect->before();
        if (!empty($this->aspects)) {
            return $this->invokeNext();
        }
        $args = empty($args) ? $this->args : $args;
        return $this->proxy->_handle($this->method, $args);
    }

    public function invokeNext()
    {
        $handler = clone $this;
        $handler->aspect = array_shift($this->aspects);
        $handler->aspects = $this->aspects;
        return $handler->invokeAspect();
    }

    protected function makeProceedingJoinPoint(): ProceedingJoinPoint
    {
        $point = new ProceedingJoinPoint(
            $this,
            $this->proxy,
            $this->class,
            $this->method
        );
        if ($this->error) {
            $point->setError($this->error);
        }
        return $point;
    }

    protected function makeJoinPoint($return = null): JoinPoint
    {
        $point = new JoinPoint(
            $this,
            $this->proxy,
            $this->class,
            $this->method
        );
        if ($this->error) {
            $point->setError($this->error);
        }
        if ($return) {
            $point->setReturn($return);
        }
        return $point;
    }
}
