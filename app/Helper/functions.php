<?php

use App\Application;
use App\Facades\Config;
use App\Facades\Crypt;
use App\Facades\Hash;
use App\Kernel\Http\CookieManager;
use App\Kernel\Http\Request;
use App\Kernel\Http\Response;
use App\Facades\View;
use App\Kernel\Http\SessionManager;

/**
 * 辅助函数
 */

// Features

function request($name = null, $default = null)
{
    $request = Application::make(Request::class);
    if ($name === null) {
        return $request;
    }
    return $request->input($name, $default);
}

function response($content = null, $code = 200, $headers = []): Response
{
    if ($content === null) {
        return Response::make();
    }
    return Response::make($content, $code, $headers);
}

function redirect($url, $code = 302, $headers = [])
{
    return Response::redirect($url, $code, $headers);
}

function session($name = null, $default = null)
{
    $session = Application::make(SessionManager::class);
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
    $cookie = Application::make(CookieManager::class);
    if ($name === null) {
        return $cookie;
    }
    if (is_string($name)) {
        return $cookie->get($name, $default);
    }
    if (is_array($name)) {
        foreach ($name as $value) {
            $cookie->put($value);
        }
    }
}

function view($name, $data = [])
{
    return View::make($name, $data);
}

function config($name, $default = null)
{
    if (is_array($name)) {
        Config::set($name);
    } else {
        return Config::get($name, $default);
    }
}

// String

function str_random($length)
{
    $str = 'QWERTYUIOPASDFGHJKLZXCVBNM1234567890qwertyuiopasdfghjklzxcvbnm';
    while (strlen($str) < $length) {
        $str .= $str;
    }
    $str = str_shuffle($str);
    return substr($str, 0, $length);
}


// Path
function app_path($sub_path = '')
{
    return realpath(BASE_PATH . "/app/$sub_path");
}

function base_path($sub_path = '')
{
    return realpath(BASE_PATH . "/$sub_path");
}

function config_path($config_name = '')
{
    return realpath(BASE_PATH . "/config/$config_name");
}

function public_path($sub_path = '')
{
    return realpath(BASE_PATH . "/public/$sub_path");
}

function storage_path($sub_path = '')
{
    return realpath(BASE_PATH . "/storage/$sub_path");
}

function view_path($view)
{
    $view = str_replace('.', '/', $view);
    return realpath(BASE_PATH . "/app/Views/$view.php");
}

// Process
function abort($code = 403, $content = '', $headers = [])
{
    return Response::make($content, $code, $headers);
}

function bcrypt($value)
{
    return Hash::make($value);
}

function check($value, $hashed_value)
{
    return Hash::check($value, $hashed_value);
}

function decrypt($value)
{
    return Crypt::decrypt($value);
}

function encrypt($value)
{
    return Crypt::encrypt($value);
}

function csrf_token()
{
    return session()->token();
}

// Url
function asset($asset)
{
    return env('ASSET_URL', '') . "/$asset";
}
