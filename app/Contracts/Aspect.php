<?php

namespace App\Contracts;

use App\Kernel\JoinPoint;
use App\Kernel\ProceedingJoinPoint;
use Throwable;

interface Aspect
{
    public function before(JoinPoint $point): void;

    public function after(JoinPoint $point): void;

    public function afterReturning(JoinPoint $point): void;

    public function afterThrowing(Throwable $e): void;

    public function around(ProceedingJoinPoint $point);
}
