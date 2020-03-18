<?php

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

function session($name, $default = null)
{
    $session = Session::getInstance();
    if (is_string($name)) {
        return $session->get($name, $default);
    } else if (is_array($name)) {
        foreach ($name as $key => $value) {
            $session->put($key, $value);
        }
    }
}

function cookie($name, $default = null)
{
    $cookie = Cookie::getInstance();
    return $cookie->get($name, $default);
}

function view($name, $data = [])
{
    $view = View::make($name, $data);
    return $view;
}
