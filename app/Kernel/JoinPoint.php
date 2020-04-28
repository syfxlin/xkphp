<?php

namespace App\Kernel;

use Throwable;

class JoinPoint
{
    /**
     * @var array
     */
    protected $args = [];

    /**
     * @var object
     */
    protected $object;

    /**
     * @var string
     */
    protected $class;

    /**
     * @var string
     */
    protected $method;

    /**
     * @var AspectHandler
     */
    protected $handler;

    /**
     * @var mixed
     */
    protected $return;

    /**
     * @var Throwable
     */
    protected $error;

    public function __construct(
        AspectHandler $handler,
        $object,
        string $class,
        string $method,
        array $args = []
    ) {
        $this->handler = $handler;
        $this->object = $object;
        $this->class = $class;
        $this->method = $method;
        $this->args = $args;
    }

    public function getHandler(): AspectHandler
    {
        return $this->handler;
    }

    public function getThis()
    {
        return $this->object;
    }

    public function getClassName(): string
    {
        return $this->class;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getArgs(): array
    {
        return $this->args;
    }

    public function setReturn($return): void
    {
        $this->return = $return;
    }

    public function getReturn()
    {
        return $this->return;
    }

    public function setError(Throwable $e): void
    {
        $this->error = $e;
    }

    public function getError(): Throwable
    {
        return $this->error;
    }
}
