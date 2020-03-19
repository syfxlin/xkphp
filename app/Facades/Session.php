<?php

namespace App\Facades;

use App\Kernel\Session as KernelSession;

class Session extends Facade
{
    public static function __callStatic($name, $arguments)
    {
        $session = KernelSession::getInstance();
        return $session->$name(...$arguments);
    }
}
