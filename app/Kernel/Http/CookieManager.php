<?php

namespace App\Kernel\Http;

use App\Facades\App;

class CookieManager
{
    /**
     * @var Cookie[]
     */
    private $response_cookies = [];

    /**
     * 判断 Cookie 是否存在，并是否为空
     *
     * @param   string  $name  Cookie 的名称
     *
     * @return  bool
     */
    public function has(string $name): bool
    {
        return App::make(Request::class)->cookie($name, null) !== null;
    }

    /**
     * 获取 Cookie 的值
     *
     * @param   string       $name     Cookie 的名称
     * @param   string|null  $default  默认值
     *
     * @return  string|null
     */
    public function get(string $name, $default = null): ?string
    {
        return App::make(Request::class)->cookie($name, $default);
    }

    /**
     * 增加 Cookie
     *
     * @param   Cookie  $cookie  Cookie
     *
     * @return  void
     */
    public function put(Cookie $cookie): void
    {
        $this->response_cookies[$cookie->getName()] = $cookie;
    }

    /**
     * 设置 5 年期限的 Cookie
     *
     * @param Cookie $cookie
     */
    public function forever(Cookie $cookie): void
    {
        $this->put($cookie->withMaxAge(2628000));
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
            $this->put(Cookie::make($key, false)->withMaxAge(1));
        }
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasQueue(string $name): bool
    {
        return isset($this->response_cookies[$name]);
    }

    /**
     * @param string $name
     */
    public function unqueue(string $name): void
    {
        unset($this->response_cookies[$name]);
    }

    /**
     * @param Cookie $cookie
     */
    public function queue(Cookie $cookie): void
    {
        $this->put($cookie);
    }

    /**
     * @param string $name
     * @param Cookie|null $default
     * @return Cookie
     */
    public function queued(string $name, Cookie $default = null): Cookie
    {
        return $this->response_cookies[$name] ?? $default;
    }

    /**
     * @return array
     */
    public function getQueues(): array
    {
        return $this->response_cookies;
    }

    /**
     * @return CookieManager
     */
    public static function make(): CookieManager
    {
        return new static();
    }
}
