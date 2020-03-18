<?php

namespace App\Facades;

use App\Kernel\Response as KernelResponse;

class Response
{
    public static function __callStatic($name, $arguments)
    {
        $response = KernelResponse::getInstance();
        return $response->$name(...$arguments);
    }
}
