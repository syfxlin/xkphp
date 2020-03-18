<?php

namespace App\Facades;

use App\Utils\Hash as UtilsHash;

class Hash
{
    public static function __callStatic($name, $arguments)
    {
        $hash = new UtilsHash();
        return $hash->$name(...$arguments);
    }
}
