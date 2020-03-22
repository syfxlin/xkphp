<?php

namespace App\Facades;

class Request extends Facade
{
    protected static $class = \App\Kernel\Request::class;
    protected static $isInstance = true;
}
