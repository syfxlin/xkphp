<?php

namespace App\Kernel;

use App\Application;
use App\Facades\Crypt;

class Cookie
{
    public static function getInstance(): Cookie
    {
        return Application::getInstance(self::class);
    }

    /**
     * 判断 Cookie 是否存在，并是否为空
     *
     * @param   string  $name  Cookie 的名称
     *
     * @return  bool
     */
    public function has(string $name): bool
    {
        return isset($_COOKIE[$name]) && $_COOKIE[$name] !== null;
    }

    /**
     * 判断 Cookie 是否存在
     *
     * @param   string  $name  Cookie 的名称
     *
     * @return  bool
     */
    public function exists(string $name): bool
    {
        return isset($_COOKIE[$name]);
    }

    /**
     * 获取 Cookie 的值
     *
     * @param   string       $name     Cookie 的名称
     * @param   string|null  $default  默认值
     *
     * @return  string|null
     */
    public function get(string $name, $default = null)
    {
        if (!isset($_COOKIE[$name])) {
            return $default;
        }
        return Crypt::decrypt($_COOKIE[$name]);
    }

    /**
     * 增加 Cookie
     *
     * @param   array  $cookie  Cookie
     *
     * @return  void
     */
    public function put(array $cookie): void
    {
        response()->withCookies([$cookie]);
    }

    /**
     * 删除 Cookie
     *
     * @param   string|array  $name  Cookie 的名称或名称数组
     *
     * @return  void
     */
    public function forget($name): void
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
