<?php

namespace App\Kernel;

use App\Facades\App;
use App\Http\Request;
use function str_parse_callback;
use function strpos;

class Controller
{
    /**
     * 反射注入 Request
     *
     * @param Request $request
     * @param string $handler
     * @return  mixed
     */
    public static function invokeController(Request $request, string $handler)
    {
        return AspectManager::weavingAspectWithClosure(function () use (
            $request,
            $handler
        ) {
            return App::callWithRequest($request, $handler);
        },
        str_parse_callback($handler));
    }

    /**
     * 获取完整的类名方法
     *
     * @param string $part
     * @return string
     */
    public static function getFull(string $part): string
    {
        if (strpos($part, 'App\Controllers\\') === false) {
            $part = 'App\Controllers\\' . $part;
        }
        return $part;
    }
}
