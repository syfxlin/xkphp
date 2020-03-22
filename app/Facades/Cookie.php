<?php

namespace App\Facades;

class Cookie extends Facade
{
    protected static $class = \App\Kernel\Cookie::class;
    protected static $isInstance = true;
}
