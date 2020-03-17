<?php

use App\Kernel\Response;
use App\Kernel\Session;

function response($content = '', $code = 200): Response
{
    return Response::getInstance($content, $code);
}

function redirect($url, $code = 301)
{
    echo response('', $code)->header('Location', $url);
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