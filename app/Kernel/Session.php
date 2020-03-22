<?php

namespace App\Kernel;

use App\Application;

class Session
{
    /**
     * 获取 Session 单例
     *
     * @param   string  $sessid  sessid
     *
     * @return  Session          Session 单例
     */
    public static function getInstance(string $sessid = null): Session
    {
        return Application::getInstance(self::class, $sessid);
    }

    /**
     * Session 构造器，外部请勿调用该构造器
     *
     * @param   string  $sessid  sessid
     *
     * @return  this
     */
    public function __construct(string $sessid = null)
    {
        $session_config = config('session');
        if ($session_config['save_path']) {
            session_save_path($session_config['save_path']);
        }
        if ($session_config['cookie']) {
            session_name($session_config['cookie']);
        }
        session_set_cookie_params([
            'lifetime' => 60 * ($session_config['life_time'] ?? 1440),
            'path' => $session_config['path'] ?? '/',
            'domain' => $session_config['domain'] ?? null,
            'secure' => $session_config['secure'] ?? false,
            'httponly' => true
        ]);
        if ($sessid !== null) {
            session_id($sessid);
        }
        session_start();
        if (!$this->has('_token')) {
            $this->regenerateToken();
        }
    }

    /**
     * 判断 Session 中是否存在某个值，并且不为 null
     *
     * @param   string  $name  值的名称
     *
     * @return  bool
     */
    public function has(string $name): bool
    {
        return isset($_SESSION[$name]) && $_SESSION[$name] !== null;
    }

    /**
     * 判断 Session 中是否存在某个值
     *
     * @param   string  $name  值的名称
     *
     * @return  bool
     */
    public function exists(string $name): bool
    {
        return isset($_SESSION[$name]);
    }

    /**
     * 获取 Session 中的值
     *
     * @param   string  $name     值的名称
     * @param   mixed   $default  默认值
     *
     * @return  mixed
     */
    public function get(string $name, $default = null)
    {
        if (!isset($_SESSION[$name])) {
            return $default;
        }
        return $_SESSION[$name];
    }

    /**
     * 在 Session 中增加值
     *
     * @param   string  $key    值的名称
     * @param   mixed   $value  值
     *
     * @return  void
     */
    public function put(string $key, $value): void
    {
        $_SESSION[$key] = $value;
    }

    /**
     * 删除 Session 中的一个或多个值
     *
     * @param   string  $name  值的名称或数组
     *
     * @return  void
     */
    public function forget($name): void
    {
        if (is_string($name)) {
            unset($_SESSION[$name]);
        } else if (is_array($name)) {
            foreach ($name as $key) {
                unset($_SESSION[$key]);
            }
        }
    }

    /**
     * 清空 Session
     *
     * @return  void
     */
    public function flush(): void
    {
        session_unset();
    }

    /**
     * 重新生成 Session id
     *
     * @return  void
     */
    public function regenerate(): void
    {
        session_regenerate_id();
    }

    /**
     * 取得并删除一个 Session 值
     *
     * @param   string  $name     值的名称
     * @param   mixed   $default  默认值
     *
     * @return  mixed             值
     */
    public function pull(string $name, $default = null)
    {
        $value = $this->get($name, $default);
        $this->forget($name);
        return $value;
    }

    /**
     * 获取 CSRF Token
     *
     * @return  string  CSRF Token
     */
    public function token(): string
    {
        return $this->get('_token');
    }

    /**
     * 重新生成 CSRF Token
     *
     * @return  void
     */
    public function regenerateToken(): void
    {
        $this->put('_token', str_random(40));
    }
}
