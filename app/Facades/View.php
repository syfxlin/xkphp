<?php

namespace App\Facades;

use App\Kernel\View as KernelView;

class View
{
    public static function exists($view)
    {
        $view = str_replace('.', '/', $view);
        $view_file = __DIR__ . "/../Views/$view.php";
        return file_exists($view_file);
    }

    public static function __callStatic($name, $arguments)
    {
        $view = new KernelView();
        return $view->$name(...$arguments);
    }
}
