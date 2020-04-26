<?php

namespace App\Kernel;

use App\Facades\App;
use App\Http\Request;

class Controller
{
    /**
     * 反射注入 Request
     *
     * @param Request $request
     * @param string $method 方法名称
     * @return  mixed
     */
    public static function invokeController(Request $request, string $method)
    {
        // TODO: 命名空间设置
        return App::callWithRequest($request, $method);
    }
}
