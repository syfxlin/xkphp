<?php

namespace App\Http;

class SessionManager
{
    /**
     * @var string
     */
    private $id;
    /**
     * @var string
     */
    private $name;
    /**
     * @var array
     */
    private $sessions;

    public function __construct(string $id, string $name, array &$data)
    {
        $this->id = $id;
        $this->name = $name;
        $this->sessions = &$data;
        if (!$this->has('_token')) {
            $this->regenerateToken();
        }
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId(string $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->sessions;
    }

    /**
     * @param array $data
     */
    public function setData(array $data): void
    {
        $this->sessions = $data;
    }

    /**
     * 判断 SessionManager 中是否存在某个值，并且不为 null
     *
     * @param   string  $name  值的名称
     *
     * @return  bool
     */
    public function has(string $name): bool
    {
        return isset($this->sessions[$name]) && $this->sessions[$name] !== null;
    }

    /**
     * 判断 SessionManager 中是否存在某个值
     *
     * @param   string  $name  值的名称
     *
     * @return  bool
     */
    public function exists(string $name): bool
    {
        return isset($this->sessions[$name]);
    }

    /**
     * 获取 SessionManager 中的值
     *
     * @param   string  $name     值的名称
     * @param   mixed   $default  默认值
     *
     * @return  mixed
     */
    public function get(string $name, $default = null)
    {
        if (!isset($this->sessions[$name])) {
            return $default;
        }
        return $this->sessions[$name];
    }

    /**
     * 在 SessionManager 中增加值
     *
     * @param   string  $key    值的名称
     * @param   mixed   $value  值
     *
     * @return  void
     */
    public function put(string $key, $value): void
    {
        $this->sessions[$key] = $value;
    }

    /**
     * 删除 SessionManager 中的一个或多个值
     *
     * @param   string|array  $name  值的名称或数组
     *
     * @return  void
     */
    public function forget($name): void
    {
        if (is_string($name)) {
            unset($this->sessions[$name]);
        } elseif (is_array($name)) {
            foreach ($name as $key) {
                unset($this->sessions[$key]);
            }
        }
    }

    /**
     * 清空 SessionManager
     *
     * @return  void
     */
    public function flush(): void
    {
        session_unset();
    }

    /**
     * 重新生成 SessionManager id
     *
     * @return  string
     */
    public function regenerate(): string
    {
        session_regenerate_id();
        return session_id();
    }

    /**
     * 取得并删除一个 SessionManager 值
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

    /**
     * @param string|null $id
     * @param array $options
     * @return SessionManager
     */
    public static function make(
        string $id = null,
        array $options = []
    ): SessionManager {
        if ($id !== null) {
            session_id($id);
        }
        session_start(
            array_merge($options, [
                'use_cookies' => false,
                'use_only_cookies' => true
            ])
        );
        $id = session_id();
        $name = session_name();
        return new static($id, $name, $_SESSION);
    }

    public static function makeCookie(): ?Cookie
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            return null;
        }
        $id = session_id();
        $name = session_name();
        $cookie_options = session_get_cookie_params();
        return Cookie::make($name, $id)
            ->withMaxAge(60 * $cookie_options['lifetime'])
            ->withPath($cookie_options['path'])
            ->withDomain($cookie_options['domain'])
            ->withSecure($cookie_options['secure'])
            ->withHttpOnly($cookie_options['httponly']);
    }
}
