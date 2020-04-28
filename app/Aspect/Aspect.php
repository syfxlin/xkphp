<?php

namespace App\Aspect;

use App\Kernel\JoinPoint;
use App\Kernel\ProceedingJoinPoint;
use Throwable;

abstract class Aspect
{
    abstract public function pointCut(): array;

    public function before(): void
    {
    }

    public function after(JoinPoint $point): void
    {
    }

    public function afterReturning(JoinPoint $point): void
    {
    }

    public function afterThrowing(Throwable $e): void
    {
        throw $e;
    }

    /**
     * @param ProceedingJoinPoint $point
     * @return mixed
     */
    public function around(ProceedingJoinPoint $point)
    {
        return $point->proceed();
    }
}
