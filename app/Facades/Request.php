<?php

namespace App\Facades;

use App\Kernel\Request as KernelRequest;

class Request extends Facade
{
    public static function __callStatic($name, $arguments)
    {
        $request = KernelRequest::getInstance();
        return $request->$name(...$arguments);
    }
}
