<?php

namespace App\Facades;

use App\Database\DB as DatabaseDB;

class DB
{
    public static function __callStatic($name, $arguments)
    {
        return DatabaseDB::$name(...$arguments);
    }
}
