<?php

namespace App\Kernel;

use App\Application;

class Controller
{
    /**
     * 反射注入 Request
     *
     * @param   string  $class         Class 名称
     * @param   string  $method        方法名称
     * @param   array   $class_param   构造器参数
     * @param   array   $method_param  方法参数
     *
     * @return  mixed
     */
    public static function invokeController(string $method)
    {
        return Application::call($method);
    }
}
