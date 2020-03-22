<?php

namespace App\Facades;

class Session extends Facade
{
    protected static $class = \App\Kernel\Session::class;
    protected static $isInstance = true;
}
