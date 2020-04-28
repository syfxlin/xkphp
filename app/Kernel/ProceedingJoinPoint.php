<?php

namespace App\Kernel;

class ProceedingJoinPoint extends JoinPoint
{
    /**
     * @param array $args
     * @return mixed
     */
    public function proceed($args = [])
    {
        return $this->handler->invokeProcess($args);
    }
}
