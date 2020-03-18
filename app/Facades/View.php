<?php

namespace App\Facades;

class View extends Facade
{
    protected static $class = \App\Kernel\View::class;

    public static function exists($view)
    {
        $view = str_replace('.', '/', $view);
        $view_file = __DIR__ . "/../Views/$view.php";
        return file_exists($view_file);
    }
}
