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

/**
 * @param string|null $name
 * @param mixed $default
 * @return mixed
 */
function request($name = null, $default = null)
{
    $request = Application::make(Request::class);
    if ($name === null) {
        return $request;
    }
    return $request->input($name, $default);
}

/**
 * @param mixed $content
 * @param int $code
 * @param array $headers
 * @return Response
 */
function response(
    $content = null,
    int $code = 200,
    array $headers = []
): Response {
    if ($content === null) {
        return Response::make();
    }
    return Response::make($content, $code, $headers);
}

/**
 * @param $url
 * @param int $code
 * @param array $headers
 * @return Response
 */
function redirect($url, int $code = 302, array $headers = []): Response
{
    return Response::redirect($url, $code, $headers);
}

/**
 * @param null|string|array $name
 * @param mixed $default
 * @return mixed|void
 */
function session($name = null, $default = null)
{
    $session = Application::make(SessionManager::class);
    if ($name === null) {
        return $session;
    }
    if (is_string($name)) {
        return $session->get($name, $default);
    }
    if (is_array($name)) {
        foreach ($name as $key => $value) {
            $session->put($key, $value);
        }
    }
}

/**
 * @param null|string|array $name
 * @param mixed $default
 * @return mixed|void
 */
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

/**
 * @param string $name
 * @param array $data
 * @return \App\Kernel\View
 */
function view(string $name, $data = []): \App\Kernel\View
{
    return View::make($name, $data);
}

/**
 * @param array|string $name
 * @param mixed $default
 * @return mixed|void
 */
function config($name, $default = null)
{
    if (is_array($name)) {
        Config::set($name);
    } else {
        return Config::get($name, $default);
    }
}

// String

/**
 * @param int $length
 * @return false|string
 */
function str_random(int $length)
{
    $str = 'QWERTYUIOPASDFGHJKLZXCVBNM1234567890qwertyuiopasdfghjklzxcvbnm';
    while (strlen($str) < $length) {
        $str .= $str;
    }
    $str = str_shuffle($str);
    return substr($str, 0, $length);
}

// Path
/**
 * @param string $sub_path
 * @return string
 */
function app_path(string $sub_path = '')
{
    return realpath(BASE_PATH . "/app/$sub_path");
}

/**
 * @param string $sub_path
 * @return false|string
 */
function base_path(string $sub_path = '')
{
    return realpath(BASE_PATH . "/$sub_path");
}

/**
 * @param string $config_name
 * @return false|string
 */
function config_path(string $config_name = '')
{
    return realpath(BASE_PATH . "/config/$config_name");
}

/**
 * @param string $sub_path
 * @return false|string
 */
function public_path(string $sub_path = '')
{
    return realpath(BASE_PATH . "/public/$sub_path");
}

/**
 * @param string $sub_path
 * @return false|string
 */
function storage_path(string $sub_path = '')
{
    return realpath(BASE_PATH . "/storage/$sub_path");
}

/**
 * @param string $view
 * @return false|string
 */
function view_path(string $view)
{
    $view = str_replace('.', '/', $view);
    return realpath(BASE_PATH . "/app/Views/$view.php");
}

// Process
/**
 * @param int $code
 * @param string $content
 * @param array $headers
 * @return Response
 */
function abort(int $code = 403, $content = '', array $headers = []): Response
{
    return Response::make($content, $code, $headers);
}

/**
 * @param $value
 * @return mixed
 */
function bcrypt($value)
{
    return Hash::make($value);
}

/**
 * @param string $value
 * @param string $hashed_value
 * @return bool
 */
function check(string $value, string $hashed_value): bool
{
    return Hash::check($value, $hashed_value);
}

/**
 * @param string $value
 * @return mixed
 */
function decrypt(string $value)
{
    return Crypt::decrypt($value);
}

/**
 * @param $value
 * @return mixed
 */
function encrypt($value)
{
    return Crypt::encrypt($value);
}

/**
 * @return mixed
 */
function csrf_token()
{
    return session()->token();
}

// Url
/**
 * @param string $asset
 * @return string
 */
function asset(string $asset): string
{
    return env('ASSET_URL', '') . "/$asset";
}
