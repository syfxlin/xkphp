<?php

namespace App\Facades;

use App\Utils\Crypt as UtilsCrypt;

class Crypt
{
    public static function __callStatic($name, $arguments)
    {
        $crypt = new UtilsCrypt(base64_decode(env('APP_KEY')));
        return $crypt->$name(...$arguments);
    }
}
