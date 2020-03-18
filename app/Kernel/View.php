<?php

namespace App\Kernel;

use App\Application;

class View
{
    public static function getInstance()
    {
        return Application::getInstance(self::class);
    }

    public static function exists($view)
    {
        $view = str_replace('.', '/', $view);
        $view_file = __DIR__ . "/../Views/$view.php";
        return file_exists($view_file);
    }

    public static function __callStatic($name, $arguments)
    {
        $view = new ViewItem();
        return $view->$name(...$arguments);
    }
}
