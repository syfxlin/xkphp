<?php

namespace App\Facades;

use App\Kernel\Cookie as KernelCookie;

class Cookie extends Facade
{
    public static function __callStatic($name, $arguments)
    {
        $cookie = KernelCookie::getInstance();
        return $cookie->$name(...$arguments);
    }
}
