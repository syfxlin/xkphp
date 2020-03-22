<?php

namespace App\Facades;

use App\Utils\Crypt as UtilsCrypt;

class Crypt extends Facade
{
    public static function __callStatic($name, $arguments)
    {
        $crypt = new UtilsCrypt(base64_decode(env('APP_KEY')), env('APP_CIPHER', 'AES-256-CBC'));
        return $crypt->$name(...$arguments);
    }
}
