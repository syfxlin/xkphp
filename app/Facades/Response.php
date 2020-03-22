<?php

namespace App\Facades;

class Response extends Facade
{
    protected static $class = \App\Kernel\Response::class;
    protected static $isInstance = true;
}
