<?php

namespace App\Aspect;

use App\Controllers\HomeController;
use App\Kernel\JoinPoint;
use App\Kernel\ProceedingJoinPoint;
use App\Utils\Hash;
use Throwable;
use function report;

class LogAspect extends Aspect
{
    public function after(JoinPoint $point): void
    {
        report('info', 'after-' . $point->getMethod());
    }

    public function before(JoinPoint $point): void
    {
        report('info', 'before-' . $point->getMethod());
    }

    public function afterReturning(JoinPoint $point): void
    {
        report('info', 'afterReturning-' . $point->getMethod());
    }

    public function afterThrowing(Throwable $e): void
    {
        report('error', $e);
    }

    public function around(ProceedingJoinPoint $point)
    {
        report('info', 'beforeAround');
        $result = $point->proceed();
        report('info', 'afterAround');
        return $result;
    }
}
