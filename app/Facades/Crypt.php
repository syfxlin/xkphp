<?php

namespace App\Facades;

class Crypt extends Facade
{
    protected static $class = \App\Utils\Crypt::class;

    public static function getArgs()
    {
        return [
            base64_decode(env('APP_KEY')),
            env('APP_CIPHER', 'AES-256-CBC')
        ];
    }
}
