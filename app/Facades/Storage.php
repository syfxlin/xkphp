<?php

namespace App\Facades;

class Storage extends Facade
{
    protected static $class = \App\Utils\File::class;
}