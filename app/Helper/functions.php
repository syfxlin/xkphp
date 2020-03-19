<?php

use App\Facades\Config;
use App\Kernel\Cookie;
use App\Facades\Response;
use App\Kernel\Session;
use App\Facades\View;

function response($content = '', $code = 200, $headers = []): App\Kernel\Response
{
    return Response::make($content, $code, $headers);
}

function redirect($url, $code = 301, $headers = [])
{
    response('', $code, $headers)->header('Location', $url)->emit();
    exit;
}

function session($name = null, $default = null)
{
    $session = Session::getInstance();
    if ($name === null) {
        return $session;
    }
    if (is_string($name)) {
        return $session->get($name, $default);
    } else if (is_array($name)) {
        foreach ($name as $key => $value) {
            $session->put($key, $value);
        }
    }
}

function cookie($name = null, $default = null)
{
    $cookie = Cookie::getInstance();
    if ($name === null) {
        return $cookie;
    }
    return $cookie->get($name, $default);
}

function view($name, $data = [])
{
    $view = View::make($name, $data);
    return $view;
}

function config($name, $default = null)
{
    if (is_array($name)) {
        Config::set($name);
    } else {
        return Config::get($name, $default);
    }
}

function str_random($length)
{
    $str = "QWERTYUIOPASDFGHJKLZXCVBNM1234567890qwertyuiopasdfghjklzxcvbnm";
    while (strlen($str) < $length) {
        $str .= $str;
    }
    $str = str_shuffle($str);
    return substr($str, 0, $length);
}
