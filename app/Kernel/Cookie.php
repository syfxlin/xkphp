<?php

namespace App\Kernel;

use App\Application;
use App\Facades\Crypt;

class Cookie
{
    public static function getInstance()
    {
        return Application::getInstance(self::class);
    }

    public function has($name)
    {
        return isset($_COOKIE[$name]) && $_COOKIE[$name] !== null;
    }

    public function exists($name)
    {
        return isset($_COOKIE[$name]);
    }

    public function get($name, $default = null)
    {
        if (!isset($_COOKIE[$name])) {
            return $default;
        }
        return Crypt::decrypt($_COOKIE[$name]);
    }

    public function put($cookie)
    {
        response()->withCookies([$cookie]);
    }

    public function forget($name)
    {
        if (is_string($name)) {
            $name = [$name];
        }
        foreach ($name as $key) {
            $this->put([
                'name' => $key,
                'value' => false,
                'expire' => 1,
            ]);
            unset($_COOKIE[$key]);
        }
    }
}
